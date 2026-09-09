<?php

namespace App\Application\Reservation\Appointment\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class AppointmentDateDto
{
    #[Assert\NotBlank(message: 'Sélectionnez une date.')]
    public ?string $value = null;
}
