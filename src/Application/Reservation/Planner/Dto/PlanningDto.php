<?php

namespace App\Application\Reservation\Planner\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class PlanningDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le nom du planning est obligatoire.')]
        #[Assert\Length(max: 70, maxMessage: 'Le nom du planning ne peut pas dépasser {{ limit }} caractères.')]
        public ?string $title = null,

        public ?string $description = null,

        #[Assert\NotNull(message: 'La durée du rendez-vous est obligatoire.')]
        #[Assert\Range(min: 5, max: 60, notInRangeMessage: 'La durée doit être comprise entre {{ min }} et {{ max }} minutes.')]
        public ?int $duration = 30,

        #[Assert\NotNull(message: 'Le délai entre deux rendez-vous est obligatoire.')]
        #[Assert\Range(min: 10, max: 40, notInRangeMessage: 'Le délai doit être compris entre {{ min }} et {{ max }} minutes.')]
        public ?int $gap = 10,

        #[Assert\NotBlank(message: 'La couleur du planning est obligatoire.')]
        #[Assert\CssColor(formats: Assert\CssColor::HEX_LONG, message: 'La couleur doit être au format hexadécimal #RRGGBB.')]
        public string $color = '#6750A4',
    ) {
    }
}
