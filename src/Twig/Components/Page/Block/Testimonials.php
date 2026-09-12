<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Page\Block\Library\Testimonials\Main\TestimonialsDTO;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Page:Block:Testimonials:Main', template: 'components/page/block/testimonials/Testimonials.html.twig')] final class Testimonials
{
    public TestimonialsDTO $data;
    public ?int $blockId = null;
}
