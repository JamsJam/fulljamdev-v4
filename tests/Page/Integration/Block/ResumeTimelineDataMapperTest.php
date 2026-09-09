<?php

namespace App\Tests\Page\Integration\Block;

use App\Application\Page\Block\Library\Resume\Timeline\ResumeTimelineDTO;
use App\Application\Page\Block\Mapper\BlockDataMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ResumeTimelineDataMapperTest extends KernelTestCase
{
    public function testItMapsItsTitle(): void
    {
        self::bootKernel();
        $mapper = self::getContainer()->get(BlockDataMapper::class);
        $data = ['title' => ['content' => 'Mon parcours', 'level' => 'h2', 'attributes' => []]];

        $block = $mapper->denormalize('resume.timeline', $data);

        self::assertInstanceOf(ResumeTimelineDTO::class, $block);
        self::assertSame('Mon parcours', $block->title->content);
        self::assertSame($data, $mapper->normalize($block));
    }
}
