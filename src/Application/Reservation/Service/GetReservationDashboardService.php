<?php

namespace App\Application\Reservation\Service;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Application\Reservation\Appointment\Provider\AppointmentsByPeriodAndStatusesProvider;
use App\Application\Reservation\Appointment\Provider\AppointmentsByStatusesProvider;
use App\Application\Reservation\Appointment\Provider\AppointmentsToProcessProvider;
use App\Application\Reservation\Appointment\Provider\Interface\AppointmentProviderInterface;
use App\Application\Reservation\Planner\Provider\Interface\PlannerProviderInterface;
use App\Application\Reservation\Resolver\MonthResolver;
use App\Entity\Reservation\Appointment;
use App\Entity\Reservation\Planning;

final class GetReservationDashboardService
{
    public function __construct(
        private readonly PlannerProviderInterface $plannerProvider,
        private readonly AppointmentProviderInterface $appointmentsByPeriodProvider,
        private readonly AppointmentsByStatusesProvider $appointmentsByStatusesProvider,
        private readonly AppointmentsByPeriodAndStatusesProvider $appointmentsByPeriodAndStatusesProvider,
        private readonly AppointmentsToProcessProvider $appointmentsToProcessProvider,
        private readonly MonthResolver $monthResolver,
    ) {
    }

    /**
     * @return array{
     *     plannings: Planning[],
     *     requestedAppointments: list<Appointment>,
     *     proposedAppointments: list<Appointment>,
     *     upcomingDays: list<array{date: \DateTimeImmutable, appointments: Appointment[]}>,
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
            'upcomingDays' => $this->buildUpcomingDays($now, $appointments),
            'appointmentsToProcess' => $this->appointmentsToProcessProvider->provide(date: $now),
        ];
    }

    /**
     * @return array{
     *     plannings: Planning[],
     *     calendarWeeks: array<int, array<int, array{
     *         date: \DateTimeImmutable,
     *         isCurrentMonth: bool,
     *         isToday: bool,
     *         appointments: Appointment[]
     *     }>>,
     *     month: \DateTimeImmutable,
     *     monthLabel: string,
     *     previousMonth: string,
     *     nextMonth: string
     * }
     */
    public function getCalendar(string $requestedMonth): array
    {
        $month = $this->monthResolver->resolve($requestedMonth);
        $calendarStart = $month->modify('monday this week');
        $calendarEnd = $month
            ->modify('last day of this month')
            ->modify('sunday this week')
            ->modify('+1 day');
        $calendarAppointments = $this->appointmentsByPeriodProvider->provide(
            startAt: $calendarStart,
            endAt: $calendarEnd,
        );

        return [
            'plannings' => $this->plannerProvider->provide(),
            'calendarWeeks' => $this->buildCalendarWeeks(
                $calendarStart,
                $calendarEnd,
                $month,
                $calendarAppointments,
            ),
            'month' => $month,
            'monthLabel' => ucfirst((string) (new \IntlDateFormatter(
                'fr_FR',
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::NONE,
                null,
                null,
                'MMMM yyyy',
            ))->format($month)),
            'previousMonth' => $month->modify('-1 month')->format('Y-m'),
            'nextMonth' => $month->modify('+1 month')->format('Y-m'),
        ];
    }

    /**
     * @param Appointment[] $appointments
     *
     * @return list<array{date: \DateTimeImmutable, appointments: Appointment[]}>
     */
    private function buildUpcomingDays(\DateTimeImmutable $now, array $appointments): array
    {
        $appointmentsByDay = [];

        foreach ($appointments as $appointment) {
            $appointmentsByDay[$appointment->getStartAt()?->format('Y-m-d')][] = $appointment;
        }

        $days = [];
        $today = $now->setTime(0, 0);

        for ($offset = 0; $offset < 3; ++$offset) {
            $date = $today->modify(sprintf('+%d days', $offset));
            $days[] = [
                'date' => $date,
                'appointments' => $appointmentsByDay[$date->format('Y-m-d')] ?? [],
            ];
        }

        return $days;
    }

    /**
     * @param Appointment[] $appointments
     *
     * @return array<int, array<int, array{date: \DateTimeImmutable, isCurrentMonth: bool, isToday: bool, appointments: Appointment[]}>>
     */
    private function buildCalendarWeeks(
        \DateTimeImmutable $calendarStart,
        \DateTimeImmutable $calendarEnd,
        \DateTimeImmutable $month,
        array $appointments,
    ): array {
        $appointmentsByDay = [];

        foreach ($appointments as $appointment) {
            $appointmentsByDay[$appointment->getStartAt()?->format('Y-m-d')][] = $appointment;
        }

        $weeks = [];
        $week = [];
        $today = new \DateTimeImmutable('today');

        for ($date = $calendarStart; $date < $calendarEnd; $date = $date->modify('+1 day')) {
            $dateKey = $date->format('Y-m-d');
            $week[] = [
                'date' => $date,
                'isCurrentMonth' => $date->format('Y-m') === $month->format('Y-m'),
                'isToday' => $dateKey === $today->format('Y-m-d'),
                'appointments' => $appointmentsByDay[$dateKey] ?? [],
            ];

            if (7 === count($week)) {
                $weeks[] = $week;
                $week = [];
            }
        }

        return $weeks;
    }
}
