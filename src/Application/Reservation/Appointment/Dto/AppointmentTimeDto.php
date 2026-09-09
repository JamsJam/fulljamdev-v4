<?php

namespace App\Application\Reservation\Appointment\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class AppointmentTimeDto
{
    #[Assert\NotBlank(message: 'Le fuseau horaire est obligatoire.')]
    #[Assert\Timezone(message: 'Ce fuseau horaire n’est pas valide.')]
    public ?string $timezone = 'Europe/Paris';

    #[Assert\NotBlank(message: 'Sélectionnez une heure.')]
    public ?string $value = null;
}
