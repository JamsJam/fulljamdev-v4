<?php

namespace App\Application\Settings\Account\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class AccountSettingsDto
{
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(max: 100)]
    public string $firstName = '';

    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 100)]
    public string $lastName = '';

    #[Assert\NotBlank(message: 'L’adresse email est obligatoire.')]
    #[Assert\Email(message: 'Cette adresse email n’est pas valide.')]
    #[Assert\Length(max: 180)]
    public string $email = '';

    #[Assert\NotBlank(message: 'Le numéro de téléphone est obligatoire.')]
    #[Assert\Length(max: 30)]
    public string $phoneNumber = '';

    #[Assert\NotBlank(message: 'L’entreprise est obligatoire.')]
    #[Assert\Length(max: 150)]
    public string $company = '';

    #[Assert\NotBlank(message: 'Le poste est obligatoire.')]
    #[Assert\Length(max: 150)]
    public string $jobTitle = '';
}
