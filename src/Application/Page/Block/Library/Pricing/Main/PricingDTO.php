<?php

namespace App\Application\Page\Block\Library\Pricing\Main;

use App\Application\Page\Element\Heading\HeadingDTO;
use App\Application\Page\Element\Heading\HeadingLevel;
use App\Application\Page\Element\Text\TextDTO;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class PricingDTO
{
    #[Assert\Valid] public HeadingDTO $title;
    #[Assert\Valid] public TextDTO $text;
    /** @var list<PricingCardDTO> */ #[Assert\Valid] #[Assert\Count(min: 1)] public array $cards = [];
    public function __construct()
    {
        $this->title = new HeadingDTO();
        $this->title->level = HeadingLevel::H2;
        $this->text = new TextDTO();
    }

    #[Assert\Callback]
    public function validateFeaturedCard(ExecutionContextInterface $context): void
    {
        if (count(array_filter($this->cards, static fn (PricingCardDTO $card): bool => $card->featured)) > 1) {
            $context->buildViolation('Une seule offre peut être mise en avant.')
                ->atPath('cards')
                ->addViolation();
        }
    }
}
