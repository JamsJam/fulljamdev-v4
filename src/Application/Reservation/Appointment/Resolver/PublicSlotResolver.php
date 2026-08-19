<?php

namespace App\Application\Reservation\Appointment\Resolver;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Application\Reservation\Appointment\Provider\AppointmentProvider;
use App\Application\Reservation\Unavailability\Provider\Interface\UnavailabilityProviderInterface;
use App\Entity\Reservation\Planning;
use App\Service\ConfigurationService;

final readonly class PublicSlotResolver
{
    public function __construct(
        private AppointmentProvider $appointmentProvider,
        private UnavailabilityProviderInterface $unavailabilityProvider,
        private ConfigurationService $configuration,
    ) {
    }

    /** @return array<string, string[]> */
    public function resolve(Planning $planning, int $days = 30): array
    {
        $timezone = new \DateTimeZone((string) $this->configuration->get('parameters.timezone', 'Europe/Paris'));
        $now = new \DateTimeImmutable('now', $timezone);
        $end = $now->modify(sprintf('+%d days', $days))->setTime(23, 59, 59);

        return $this->resolveRange($planning, $now->setTime(0, 0), $end, $now);
    }

    /** @return array<string, string[]> */
    public function resolveMonth(Planning $planning, \DateTimeImmutable $month): array
    {
        $timezone = new \DateTimeZone((string) $this->configuration->get('parameters.timezone', 'Europe/Paris'));
        $now = new \DateTimeImmutable('now', $timezone);
        $start = $month->setTimezone($timezone)->modify('first day of this month')->setTime(0, 0);
        $end = $start->modify('last day of this month')->setTime(23, 59, 59);

        return $this->resolveRange($planning, $start, $end, $now);
    }

    /** @return array<string, string[]> */
    private function resolveRange(Planning $planning, \DateTimeImmutable $start, \DateTimeImmutable $end, \DateTimeImmutable $now): array
    {
        $appointments = $this->appointmentProvider->provide(startAt: $start, endAt: $end);
        $unavailabilities = $this->unavailabilityProvider->provide();
        $slots = [];

        for ($date = $start; $date <= $end; $date = $date->modify('+1 day')) {
            foreach ($planning->getAvailabilities() as $availability) {
                if ($availability->getDow() !== (int) $date->format('N')) {
                    continue;
                }

                $cursor = $date->setTime(
                    (int) $availability->getStartHour()?->format('H'),
                    (int) $availability->getStartHour()?->format('i'),
                );
                $limit = $date->setTime(
                    (int) $availability->getEndHour()?->format('H'),
                    (int) $availability->getEndHour()?->format('i'),
                );

                while ($cursor->modify(sprintf('+%d minutes', $planning->getDuration())) <= $limit) {
                    $slotEnd = $cursor->modify(sprintf('+%d minutes', $planning->getDuration()));
                    $isOccupied = false;

                    foreach ($appointments as $appointment) {
                        if (in_array($appointment->getStatus(), [AppointmentStatus::REQUESTED, AppointmentStatus::PROPOSED, AppointmentStatus::CONFIRMED], true)
                            && $appointment->getPlanning()?->getId() === $planning->getId()
                            && $appointment->getStartAt() < $slotEnd
                            && $appointment->getEndAt() > $cursor) {
                            $isOccupied = true;
                            break;
                        }
                    }

                    foreach ($unavailabilities as $unavailability) {
                        if ($unavailability->getStartAt() < $slotEnd && $unavailability->getEndAt() > $cursor) {
                            $isOccupied = true;
                            break;
                        }
                    }

                    if ($cursor > $now && !$isOccupied) {
                        $slots[$date->format('Y-m-d')][] = $cursor->format('H:i');
                    }

                    $cursor = $slotEnd->modify(sprintf('+%d minutes', $planning->getGap()));
                }
            }
        }

        return $slots;
    }

    /** @param array<string, string[]> $slots */
    public function contains(array $slots, string $date, string $time): bool
    {
        return in_array($time, $slots[$date] ?? [], true);
    }
}
