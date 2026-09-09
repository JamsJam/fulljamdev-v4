<?php

namespace App\Twig\Components\Front;

use App\Application\Page\Block\Library\Faq\Main\FaqItemDTO;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/front/accordion/Accordion.html.twig')]
final class Accordion
{
    /** @var list<FaqItemDTO> */
    public array $items = [];

    public string $name = 'accordion';
}
