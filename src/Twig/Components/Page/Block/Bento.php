<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Page\Block\Library\Bento\Main\BentoDTO;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Page:Block:Bento:Main', template: 'components/page/block/bento/Bento.html.twig')] final class Bento
{
    public BentoDTO $data;
    public ?int $blockId = null;
}
