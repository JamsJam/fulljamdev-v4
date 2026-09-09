<?php

namespace App\Application\Experience\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ExperienceDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 180)]
    public string $title = '';
    #[Assert\NotBlank]
    #[Assert\Length(max: 160)]
    public string $company = '';
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    public string $type = '';
    #[Assert\Length(max: 255)]
    public ?string $contractType = null;
    #[Assert\NotNull]
    public ?\DateTimeImmutable $beginAt = null;
    public ?\DateTimeImmutable $endAt = null;
    #[Assert\NotBlank(message: 'Ajoutez au moins une réalisation.')]
    public string $about = '';
    public bool $isVisible = true;
}
