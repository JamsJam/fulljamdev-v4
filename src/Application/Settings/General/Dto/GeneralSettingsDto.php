<?php

namespace App\Application\Settings\General\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class GeneralSettingsDto
{
    #[Assert\NotBlank(message: 'Sélectionnez un fuseau horaire.')]
    #[Assert\Timezone(message: 'Ce fuseau horaire n’est pas valide.')]
    public string $timezone = 'Europe/Paris';

    #[Assert\NotNull(message: 'Sélectionnez une page d’accueil.')]
    #[Assert\Positive(message: 'La page d’accueil sélectionnée n’est pas valide.')]
    public ?int $homepagePageId = null;
}
