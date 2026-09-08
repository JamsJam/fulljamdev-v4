<?php

namespace App\Tests\Page\Unit\Block;

use App\Application\Page\Block\Library\Project\Featured\FeaturedProjectsBlock;
use App\Application\Page\Block\Library\Project\Featured\FeaturedProjectsDTO;
use App\Application\Page\Block\Library\Project\Featured\FeaturedProjectsType;
use PHPUnit\Framework\TestCase;

final class FeaturedProjectsBlockTest extends TestCase
{
    public function testItDefinesAStandaloneDynamicProjectBlock(): void
    {
        $block = new FeaturedProjectsBlock();

        self::assertSame('project.featured', $block->type());
        self::assertSame('Projets mis en avant', $block->label());
        self::assertSame('Projet', $block->category());
        self::assertSame(FeaturedProjectsDTO::class, $block->dtoClass());
        self::assertSame(FeaturedProjectsType::class, $block->formType());
        self::assertInstanceOf(FeaturedProjectsDTO::class, $block->createDefaultData());
    }
}
