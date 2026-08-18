<?php

namespace App\Controller\Front;

use App\Application\Reservation\Appointment\Dto\PublicAppointmentDto;
use App\Application\Reservation\Appointment\Resolver\PublicSlotResolver;
use App\Application\Reservation\Planner\Service\FindPlanningService;
use App\Form\PublicAppointmentType;
use App\UI\DatePicker\Service\DatePickerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboStreamResponse;

final class PlanningCalendarController extends AbstractController
{
    #[Route(
        '/book-meeting/{slug}/calendar/{month}',
        name: 'app_front_planning_calendar',
        requirements: ['slug' => '[a-z0-9-]+', 'month' => '\d{4}-(?:0[1-9]|1[0-2])'],
        methods: ['GET'],
    )]
    public function __invoke(
        string $slug,
        string $month,
        FindPlanningService $findPlanningService,
        PublicSlotResolver $slotResolver,
        DatePickerService $datePicker,
    ): Response {
        $planning = $findPlanningService->findBySlug($slug);
        if (null === $planning || !$planning->isActive()) {
            throw $this->createNotFoundException('Ce planning n’est pas disponible.');
        }

        try {
            $displayedMonth = $datePicker->resolveMonth($month);
        } catch (\InvalidArgumentException) {
            throw $this->createNotFoundException('Ce mois n’est pas valide.');
        }

        $slots = $slotResolver->resolveMonth($planning, $displayedMonth);
        $form = $this->createForm(PublicAppointmentType::class, new PublicAppointmentDto(), ['slots' => $slots]);

        return new TurboStreamResponse($this->renderView('front/reservation/turbo/stream/calendar.stream.html.twig', [
            'planning' => $planning,
            'form' => $form,
            'calendar' => $datePicker->create($displayedMonth, array_keys($slots)),
        ]));
    }
}
