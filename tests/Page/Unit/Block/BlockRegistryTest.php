<?php

namespace App\Tests\Page\Unit\Block;

use App\Application\Page\Block\Hero\Main\HeroBlock;
use App\Application\Page\Block\Hero\Main\HeroDTO;
use App\Application\Page\Block\Registry\BlockRegistry;
use PHPUnit\Framework\TestCase;

final class BlockRegistryTest extends TestCase
{
    public function testItFindsAndGroupsHeroDefinition(): void
    {
        $registry = new BlockRegistry([new HeroBlock()]);

        self::assertSame('Hero principal', $registry->get('hero.main')->label());
        self::assertArrayHasKey('hero.main', $registry->byCategory('hero'));
        self::assertInstanceOf(HeroDTO::class, $registry->get('hero.main')->createDefaultData());
    }

    public function testItRejectsUnknownAndDuplicateTypes(): void
    {
        $registry = new BlockRegistry([new HeroBlock()]);
        $this->expectException(\InvalidArgumentException::class);
        $registry->get('unknown.block');
    }

    public function testItRejectsDuplicateDefinitions(): void
    {
        $this->expectException(\LogicException::class);
        new BlockRegistry([new HeroBlock(), new HeroBlock()]);
    }
}
