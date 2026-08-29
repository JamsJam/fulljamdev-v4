<?php

namespace App\Tests\Page\Unit\Block;

use App\Application\Page\Block\Library\Hero\ClassicSquare\HeroClassicSquareBlock;
use App\Application\Page\Block\Library\Hero\Shared\HeroDTO;
use App\Application\Page\Block\Library\Hero\WithFsImage\HeroWithFsImageBlock;
use App\Application\Page\Block\Library\Hero\WithXlImage\HeroWithXlImageBlock;
use App\Application\Page\Block\Registry\BlockRegistry;
use PHPUnit\Framework\TestCase;

final class BlockRegistryTest extends TestCase
{
    public function testItFindsAndGroupsHeroDefinition(): void
    {
        $registry = new BlockRegistry([new HeroClassicSquareBlock(), new HeroWithFsImageBlock(), new HeroWithXlImageBlock()]);

        self::assertSame('Hero classique avec image carrée', $registry->get('hero.main')->label());
        self::assertArrayHasKey('hero.main', $registry->byCategory('hero'));
        self::assertInstanceOf(HeroDTO::class, $registry->get('hero.main')->createDefaultData());
        self::assertSame('Hero avec image XL', $registry->get('hero.with_xl_image')->label());
        self::assertSame('Hero avec image plein cadre', $registry->get('hero.with_fs_image')->label());
    }

    public function testItRejectsUnknownAndDuplicateTypes(): void
    {
        $registry = new BlockRegistry([new HeroClassicSquareBlock()]);
        $this->expectException(\InvalidArgumentException::class);
        $registry->get('unknown.block');
    }

    public function testItRejectsDuplicateDefinitions(): void
    {
        $this->expectException(\LogicException::class);
        new BlockRegistry([new HeroClassicSquareBlock(), new HeroClassicSquareBlock()]);
    }
}
