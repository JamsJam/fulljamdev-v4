<?php

namespace App\Application\Page\Block\Library\CardDisplay\Data;

use App\Application\Page\Element\Cta\CtaDTO;
use App\Application\Page\Element\Image\ImageDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class CardDisplayItemDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    public string $title = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 600)]
    public string $text = '';

    #[Assert\Valid]
    public ?ImageDTO $logo = null;

    #[Assert\Valid]
    public ?ImageDTO $image = null;

    #[Assert\Valid]
    public ?CtaDTO $cta;

    public function __construct()
    {
        $this->cta = new CtaDTO();
    }
}
