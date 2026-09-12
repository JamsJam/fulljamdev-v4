<?php

namespace App\Application\Page\Block\Library\Testimonials\Main;

use App\Application\Page\Element\Heading\HeadingDTO;
use App\Application\Page\Element\Heading\HeadingLevel;
use App\Application\Page\Element\Text\TextDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class TestimonialsDTO
{
    #[Assert\Valid] public HeadingDTO $title;
    #[Assert\Valid] public TextDTO $text;
    /** @var list<TestimonialDTO> */ #[Assert\Valid] #[Assert\Count(min: 1)] public array $testimonials = [];
    public function __construct()
    {
        $this->title = new HeadingDTO();
        $this->title->level = HeadingLevel::H2;
        $this->text = new TextDTO();
    }
}
