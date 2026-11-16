<?php

namespace App\Application\LinkTree\Dto;

use App\Entity\LinkTree\Link;
use Symfony\Component\Validator\Constraints as Assert;

final class LinkDto
{
    #[Assert\Length(max: 180)] public ?string $label = null;
    #[Assert\Length(max: 2048), Assert\Url(requireTld: true)] public ?string $href = null;
    public bool $isOnline = false;
    public bool $isPrefered = false;

    public static function fromLink(Link $link): self
    {
        $dto = new self();
        $dto->label = $link->getLabel();
        $dto->href = $link->getHref();
        $dto->isOnline = $link->isOnline();
        $dto->isPrefered = $link->isPrefered();

        return $dto;
    }
}
