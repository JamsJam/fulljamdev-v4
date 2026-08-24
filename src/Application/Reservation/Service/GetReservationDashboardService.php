<?php

namespace App\Application\Reservation\Service;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Application\Reservation\Appointment\Provider\AppointmentProvider;
use App\Application\Reservation\Appointment\Provider\AppointmentsByPeriodAndStatusesProvider;
use App\Application\Reservation\Appointment\Provider\AppointmentsByStatusesProvider;
use App\Application\Reservation\Appointment\Provider\AppointmentsToProcessProvider;
use App\Application\Reservation\Planner\Provider\Interface\PlannerProviderInterface;
use App\Entity\Reservation\Appointment;
use App\Entity\Reservation\Planning;
use App\UI\Calendar\Dto\CalendarDayDto;
use App\UI\Calendar\Dto\CalendarMonthDto;
use App\UI\Calendar\Service\CalendarService;

final class GetReservationDashboardService
{
    public function __construct(
        private readonly PlannerProviderInterface $plannerProvider,
        private readonly AppointmentProvider $appointmentsByPeriodProvider,
        private readonly AppointmentsByStatusesProvider $appointmentsByStatusesProvider,
        private readonly AppointmentsByPeriodAndStatusesProvider $appointmentsByPeriodAndStatusesProvider,
        private readonly AppointmentsToProcessProvider $appointmentsToProcessProvider,
        private readonly CalendarService $calendarService,
    ) {
    }

    /**
     * @return array{
     *     plannings: Planning[],
     *     requestedAppointments: list<Appointment>,
     *     proposedAppointments: list<Appointment>,
     *     upcomingDays: list<CalendarDayDto>,
     *     appointmentsToProcess: Appointment[]
     * }
     */
    public function getDashboard(): array
    {
        $now = new \DateTimeImmutable();
        $end = $now->setTime(0, 0)->modify('+3 days');
        $requests = $this->appointmentsByStatusesProvider->provide(statuses: [
            AppointmentStatus::REQUESTED,
            AppointmentStatus::PROPOSED,
        ]);
        $appointments = $this->appointmentsByPeriodAndStatusesProvider->provide(
            startAt: $now,
            endAt: $end,
            statuses: [AppointmentStatus::CONFIRMED],
        );

        return [
            'plannings' => $this->plannerProvider->provide(),
            'requestedAppointments' => array_values(array_filter(
                $requests,
                static fn (Appointment $appointment): bool => AppointmentStatus::REQUESTED === $appointment->getStatus(),
            )),
            'proposedAppointments' => array_values(array_filter(
                $requests,
                static fn (Appointment $appointment): bool => AppointmentStatus::PROPOSED === $appointment->getStatus(),
            )),
            'upcomingDays' => $this->calendarService->createPeriod($now, 3, $appointments, self::resolveAppointmentDate(...)),
            'appointmentsToProcess' => $this->appointmentsToProcessProvider->provide(date: $now),
        ];
    }

    /**
     * @return array{
     *     plannings: Planning[],
     *     calendar: CalendarMonthDto
     * }
     */
    public function getCalendar(string $requestedMonth): array
    {
        $month = $this->calendarService->resolveMonth($requestedMonth);
        $range = $this->calendarService->resolveRange($month);
        $calendarAppointments = $this->appointmentsByPeriodProvider->provide(
            startAt: $range->start,
            endAt: $range->end,
        );

        return [
            'plannings' => $this->plannerProvider->provide(),
            'calendar' => $this->calendarService->createMonth($month, $calendarAppointments, self::resolveAppointmentDate(...)),
        ];
    }

    private static function resolveAppointmentDate(Appointment $appointment): \DateTimeImmutable
    {
        // @phpstan-ignore return.type (L’écriture métier et la contrainte SQL garantissent qu’un rendez-vous persisté possède une date de début.)
        return $appointment->getStartAt();
    }
}
