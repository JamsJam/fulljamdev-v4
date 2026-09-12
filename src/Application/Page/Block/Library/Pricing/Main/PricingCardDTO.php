<?php

namespace App\Application\Page\Block\Library\Pricing\Main;

use Symfony\Component\Validator\Constraints as Assert;

final class PricingCardDTO
{
    #[Assert\NotBlank] #[Assert\Length(max: 120)] public string $title = '';
    #[Assert\NotBlank] #[Assert\Length(max: 700)] public string $description = '';
    #[Assert\PositiveOrZero] public int $price = 0;
    public PricingPeriod $period = PricingPeriod::FIXED;

    public bool $featured = false;
    /** @var list<string> */ #[Assert\All([new Assert\NotBlank(), new Assert\Length(max: 180)])] public array $features = [];
}
