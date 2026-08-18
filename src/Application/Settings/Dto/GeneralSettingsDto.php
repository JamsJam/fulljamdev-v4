<?php

namespace App\Application\Settings\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class GeneralSettingsDto
{
    #[Assert\NotBlank(message: 'Sélectionnez un fuseau horaire.')]
    #[Assert\Timezone(message: 'Ce fuseau horaire n’est pas valide.')]
    public string $timezone = 'Europe/Paris';
}
