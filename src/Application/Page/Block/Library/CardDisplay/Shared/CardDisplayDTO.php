<?php

namespace App\Application\Page\Block\Library\CardDisplay\Shared;

use App\Application\Page\Block\Interface\InitializableBlockDataInterface;
use App\Application\Page\Block\Library\CardDisplay\Data\CardDisplayItemDTO;
use App\Application\Page\Data\Enum\ValueSource;
use App\Application\Page\Element\Cta\CtaDTO;
use App\Application\Page\Element\Heading\HeadingDTO;
use App\Application\Page\Element\Heading\HeadingLevel;
use App\Application\Page\Element\Text\TextDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class CardDisplayDTO implements InitializableBlockDataInterface
{
    #[Assert\Valid]
    public HeadingDTO $title;

    #[Assert\Valid]
    public TextDTO $text;

    public ValueSource $source = ValueSource::STATIC;

    public ?string $sourceKey = 'featured_projects';

    /** @var list<CardDisplayItemDTO> */
    #[Assert\Valid]
    public array $cards = [];

    #[Assert\Valid]
    public ?CtaDTO $cta;

    public function __construct()
    {
        $this->title = new HeadingDTO();
        $this->title->level = HeadingLevel::H2;
        $this->text = new TextDTO();
        $this->cta = new CtaDTO();
    }

    public function initializeDefaults(): void
    {
        $this->cta ??= new CtaDTO();
        foreach ($this->cards as $card) {
            $card->cta ??= new CtaDTO();
        }
    }
}
