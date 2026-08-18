<?php

namespace App\Application\Reservation\Appointment\Provider\Interface;

use App\Entity\Reservation\Appointment;

interface AppointmentProviderInterface
{
    /**
     * Appeler cette méthode exclusivement avec des arguments nommés.
     * Arguments disponibles : `id`, `startAt`, `endAt`, `statuses` et `date`.
     * Chaque implémentation précise les arguments qu'elle utilise réellement.
     *
     * @param list<\App\Application\Reservation\Appointment\Enum\AppointmentStatus> $statuses
     *
     * @return Appointment|Appointment[]|int|null
     */
    public function provide(
        ?int $id = null,
        ?\DateTimeImmutable $startAt = null,
        ?\DateTimeImmutable $endAt = null,
        array $statuses = [],
        ?\DateTimeImmutable $date = null,
    ): Appointment|array|int|null;
}
