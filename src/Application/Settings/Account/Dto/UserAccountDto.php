<?php

namespace App\Application\Settings\Account\Dto;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

final class UserAccountDto
{
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(max: 50)]
    public string $firstName = '';

    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 50)]
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

    #[Assert\Length(min: 12, minMessage: 'Le nouveau mot de passe doit contenir au moins {{ limit }} caractères.')]
    public ?string $newPassword = null;

    #[Assert\NotBlank(message: 'Saisissez votre mot de passe actuel pour enregistrer les modifications.')]
    public ?string $currentPassword = null;

    public static function fromUser(User $user): self
    {
        $dto = new self();
        $dto->firstName = (string) $user->getFirstName();
        $dto->lastName = (string) $user->getLastName();
        $dto->email = (string) $user->getEmail();
        $dto->phoneNumber = (string) $user->getPhoneNumber();
        $dto->company = (string) $user->getCompany();
        $dto->jobTitle = (string) $user->getJobTitle();

        return $dto;
    }
}
