<?php

namespace App\Tests\Page\Unit\Block;

use App\Application\Page\Block\Library\CardDisplay\DisplayCardWithImage\DisplayCardWithImageBlock;
use App\Application\Page\Block\Library\CardDisplay\DisplayCardWithLogo\DisplayCardWithLogoBlock;
use App\Application\Page\Block\Library\CardDisplay\Shared\CardDisplayDTO;
use App\Application\Page\Block\Registry\BlockRegistry;
use PHPUnit\Framework\TestCase;

final class CardDisplayBlockTest extends TestCase
{
    public function testItRegistersBothDynamicCardDisplayVariants(): void
    {
        $registry = new BlockRegistry([
            new DisplayCardWithLogoBlock(),
            new DisplayCardWithImageBlock(),
        ]);

        self::assertSame('DisplayCardWithLogo', $registry->get('services.main')->label());
        self::assertSame('DisplayCardWithImage', $registry->get('card_display.with_image')->label());
        self::assertCount(2, $registry->byCategory('CardDisplay'));
        self::assertInstanceOf(CardDisplayDTO::class, $registry->get('services.main')->createDefaultData());
    }
}
