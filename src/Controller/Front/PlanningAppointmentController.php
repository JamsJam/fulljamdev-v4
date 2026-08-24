<?php

namespace App\Controller\Front;

use App\Application\Reservation\Appointment\Dto\PublicAppointmentDto;
use App\Application\Reservation\Appointment\Resolver\PublicSlotResolver;
use App\Application\Reservation\Appointment\Service\CreateRequestedAppointmentService;
use App\Application\Reservation\Appointment\Service\SlotTimezoneConverter;
use App\Application\Reservation\Planner\Service\FindPlanningService;
use App\Application\Settings\Service\GetGeneralSettingsService;
use App\Form\PublicAppointmentType;
use App\UI\DatePicker\Service\DatePickerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;
use Symfony\UX\Turbo\TurboStreamResponse;

final class PlanningAppointmentController extends AbstractController
{
    #[Route('/book-meeting/{slug}', name: 'app_front_planning_appointment', requirements: ['slug' => '[a-z0-9-]+'], methods: ['GET', 'POST'])]
    public function __invoke(
        string $slug,
        Request $request,
        FindPlanningService $findPlanningService,
        PublicSlotResolver $slotResolver,
        DatePickerService $datePicker,
        CreateRequestedAppointmentService $createAppointmentService,
        GetGeneralSettingsService $getGeneralSettingsService,
        SlotTimezoneConverter $timezoneConverter,
    ): Response {
        $planning = $findPlanningService->findBySlug($slug);
        if (null === $planning || !$planning->isActive()) {
            throw $this->createNotFoundException('Ce planning n’est pas disponible.');
        }

        $slots = $slotResolver->resolve($planning);
        $submittedData = $request->request->all('public_appointment');
        $submittedDate = $submittedData['date']['value'] ?? null;
        $planningTimezone = $getGeneralSettingsService->get()->timezone;
        $submittedTimezone = $submittedData['time']['timezone'] ?? null;
        $displayTimezone = is_string($submittedTimezone) && in_array($submittedTimezone, timezone_identifiers_list(), true)
            ? $submittedTimezone
            : $planningTimezone;
        if (is_string($submittedDate) && 1 === preg_match('/^\d{4}-\d{2}-\d{2}$/', $submittedDate)) {
            $submittedMonth = \DateTimeImmutable::createFromFormat('!Y-m-d', $submittedDate);
            if (false !== $submittedMonth) {
                $slots = array_replace($slots, $slotResolver->resolveMonth($planning, $submittedMonth));
            }
        }
        $dto = new PublicAppointmentDto();
        $dto->time->timezone = $displayTimezone;
        $formOptions = [
            'slots' => $slots,
            'selected_date' => is_string($submittedDate) ? $submittedDate : null,
            'display_timezone' => $displayTimezone,
            'planning_timezone' => $planningTimezone,
        ];
        $form = $this->createForm(PublicAppointmentType::class, $dto, $formOptions);
        $form->handleRequest($request);

        $requestedStep = (string) $request->request->get('_booking_step', '');
        if ('date' === $requestedStep) {
            $dto->date->value = null;
            $dto->time->value = null;
            $formOptions['selected_date'] = null;
            $form = $this->createForm(PublicAppointmentType::class, $dto, $formOptions);
        } elseif ('time' === $requestedStep) {
            $dto->time->value = null;
            $form = $this->createForm(PublicAppointmentType::class, $dto, $formOptions);
        } elseif ('contact' === $requestedStep) {
            $form = $this->createForm(PublicAppointmentType::class, $dto, $formOptions);
        }

        $step = $this->resolveStep($dto, $slots);
        if ('contact' === $requestedStep && 'contact' !== $step) {
            $form->get('time')->get('value')->addError(new FormError('Ce créneau n’est plus disponible.'));
        }

        if ($form->isSubmitted() && 'submit' === $requestedStep && $form->isValid()) {
            try {
                $createAppointmentService->create($dto, $planning);

                return $this->redirectToRoute('app_front_planning_appointment_confirmation', [
                    'slug' => $planning->getSlug(),
                ]);
            } catch (\DomainException $exception) {
                $form->get('time')->get('value')->addError(new FormError($exception->getMessage()));
                $step = 'time';
            }
        }

        $context = [
            'planning' => $planning,
            'form' => $form,
            'slots' => $slots,
            'step' => $step,
            'calendar' => $datePicker->create($this->resolveDisplayedMonth($slots, $dto->date->value), array_keys($slots)),
            'selected_date' => $dto->date->value,
            'selected_time' => $dto->time->value,
            'selected_timezone' => $dto->time->timezone,
            'selected_time_label' => null !== $dto->date->value && null !== $dto->time->value
                ? $timezoneConverter->formatTime($dto->date->value, $dto->time->value, $planningTimezone, $dto->time->timezone)
                : null,
            'show_errors' => $form->isSubmitted() && 'submit' === $requestedStep,
        ];

        if (str_contains((string) $request->headers->get('Accept'), TurboBundle::STREAM_MEDIA_TYPE)) {
            return new TurboStreamResponse($this->renderView('front/reservation/turbo/stream/booking.stream.html.twig', $context));
        }

        return $this->render('front/reservation/planning.html.twig', $context);
    }

    /** @param array<string, string[]> $slots */
    private function resolveStep(PublicAppointmentDto $dto, array $slots): string
    {
        if (null === $dto->date->value || !array_key_exists($dto->date->value, $slots)) {
            return 'date';
        }

        if (null === $dto->time->value || !in_array($dto->time->value, $slots[$dto->date->value], true)) {
            return 'time';
        }

        return 'contact';
    }

    /** @param array<string, string[]> $slots */
    private function resolveDisplayedMonth(array $slots, ?string $selectedDate): \DateTimeImmutable
    {
        return new \DateTimeImmutable($selectedDate ?? array_key_first($slots) ?? 'first day of this month');
    }
}
