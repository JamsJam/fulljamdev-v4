<?php

namespace App\Tests\Page\Unit\Block;

use App\Application\Page\Block\Library\Bento\Main\BentoBlock;
use App\Application\Page\Block\Library\Hero\Positioning\PositioningHeroBlock;
use App\Application\Page\Block\Library\Pricing\Main\PricingBlock;
use App\Application\Page\Block\Library\Testimonials\Main\TestimonialsBlock;
use App\Application\Page\Block\Registry\BlockRegistry;
use PHPUnit\Framework\TestCase;

final class NewBlockDefinitionsTest extends TestCase
{
    public function testItRegistersTheNewBlockDefinitions(): void
    {
        $registry = new BlockRegistry([new PositioningHeroBlock(), new BentoBlock(), new TestimonialsBlock(), new PricingBlock()]);

        self::assertSame('Hero de positionnement', $registry->get('hero.positioning')->label());
        self::assertArrayHasKey('bento.main', $registry->byCategory('Contenu'));
        self::assertArrayHasKey('testimonials.main', $registry->byCategory('Contenu'));
        self::assertArrayHasKey('pricing.main', $registry->byCategory('Contenu'));
    }
}
