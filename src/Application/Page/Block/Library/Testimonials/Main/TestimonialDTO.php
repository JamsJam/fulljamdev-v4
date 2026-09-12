<?php

namespace App\Application\Page\Block\Library\Testimonials\Main;

use Symfony\Component\Validator\Constraints as Assert;

final class TestimonialDTO
{
    #[Assert\Range(min: 1, max: 5)] public int $rating = 5;
    #[Assert\NotBlank] #[Assert\Length(max: 1200)] public string $comment = '';
}
