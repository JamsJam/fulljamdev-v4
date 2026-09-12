<?php

namespace App\Application\Page\Block\Library\Testimonials\Main;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;

final class TestimonialsBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'testimonials.main';
    }

    public function label(): string
    {
        return 'Témoignages';
    }

    public function category(): string
    {
        return 'Contenu';
    }

    public function dtoClass(): string
    {
        return TestimonialsDTO::class;
    }

    public function formType(): string
    {
        return TestimonialsType::class;
    }

    public function component(): string
    {
        return 'Page:Block:Testimonials:Main';
    }

    public function formTemplate(): string
    {
        return 'dashboard/page/block/_testimonials_form.html.twig';
    }

    public function createDefaultData(): object
    {
        return new TestimonialsDTO();
    }
}
