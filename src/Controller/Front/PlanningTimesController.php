<?php

namespace App\Controller\Front;

use App\Application\Reservation\Appointment\Dto\PublicAppointmentDto;
use App\Application\Reservation\Appointment\Resolver\PublicSlotResolver;
use App\Application\Reservation\Planner\Service\FindPlanningService;
use App\Application\Settings\Service\GetGeneralSettingsService;
use App\Form\PublicAppointmentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboStreamResponse;

final class PlanningTimesController extends AbstractController
{
    #[Route(
        '/book-meeting/{slug}/times/{date}',
        name: 'app_front_planning_times',
        requirements: ['slug' => '[a-z0-9-]+', 'date' => '\d{4}-\d{2}-\d{2}'],
        methods: ['GET'],
    )]
    public function __invoke(
        string $slug,
        string $date,
        Request $request,
        FindPlanningService $findPlanningService,
        PublicSlotResolver $slotResolver,
        GetGeneralSettingsService $getGeneralSettingsService,
    ): Response {
        $planning = $findPlanningService->findBySlug($slug);
        if (null === $planning || !$planning->isActive()) {
            throw $this->createNotFoundException('Ce planning n’est pas disponible.');
        }

        $selectedDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        $timezone = $request->query->getString('timezone');
        if (false === $selectedDate || $selectedDate->format('Y-m-d') !== $date || !in_array($timezone, timezone_identifiers_list(), true)) {
            throw $this->createNotFoundException('La date ou le fuseau horaire n’est pas valide.');
        }

        $planningTimezone = $getGeneralSettingsService->get()->timezone;
        $slots = $slotResolver->resolveMonth($planning, $selectedDate);
        $dto = new PublicAppointmentDto();
        $dto->date->value = $date;
        $dto->time->timezone = $timezone;
        $form = $this->createForm(PublicAppointmentType::class, $dto, [
            'slots' => $slots,
            'selected_date' => $date,
            'display_timezone' => $timezone,
            'planning_timezone' => $planningTimezone,
        ]);

        return new TurboStreamResponse($this->renderView('front/reservation/turbo/stream/times.stream.html.twig', [
            'form' => $form,
        ]));
    }
}
