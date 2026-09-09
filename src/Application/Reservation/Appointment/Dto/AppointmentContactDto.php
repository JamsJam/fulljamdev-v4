<?php

namespace App\Application\Reservation\Appointment\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class AppointmentContactDto
{
    #[Assert\NotBlank(message: 'Votre prénom est obligatoire.')]
    #[Assert\Length(max: 100)]
    public ?string $firstName = null;

    #[Assert\NotBlank(message: 'Votre nom est obligatoire.')]
    #[Assert\Length(max: 100)]
    public ?string $lastName = null;

    #[Assert\NotBlank(message: 'Votre adresse e-mail est obligatoire.')]
    #[Assert\Email(message: 'Cette adresse e-mail n’est pas valide.')]
    #[Assert\Length(max: 180)]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Votre numéro de téléphone est obligatoire.')]
    #[Assert\Length(max: 30)]
    public ?string $phoneNumber = null;

    #[Assert\NotBlank(message: 'Précisez la raison du rendez-vous.')]
    #[Assert\Length(max: 70)]
    public ?string $reason = null;
}
