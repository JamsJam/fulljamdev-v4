<?php

namespace App\Tests\Page\Integration\Block;

use App\Application\Page\Block\Library\Planning\Main\PlanningBlockDTO;
use App\Application\Page\Block\Mapper\BlockDataMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PlanningBlockDataMapperTest extends KernelTestCase
{
    public function testItMapsPlanningReference(): void
    {
        self::bootKernel();
        $mapper = self::getContainer()->get(BlockDataMapper::class);

        $block = $mapper->denormalize('planning.main', [
            'title' => ['content' => 'Prendre rendez-vous', 'level' => 'h2', 'attributes' => []],
            'text' => ['content' => 'Choisissez directement votre créneau.', 'attributes' => []],
            'planningId' => 42,
        ]);

        self::assertInstanceOf(PlanningBlockDTO::class, $block);
        self::assertSame('Prendre rendez-vous', $block->title->content);
        self::assertSame('Choisissez directement votre créneau.', $block->text->content);
        self::assertSame(42, $block->planningId);
        self::assertSame([
            'title' => ['content' => 'Prendre rendez-vous', 'level' => 'h2', 'attributes' => []],
            'text' => ['content' => 'Choisissez directement votre créneau.', 'attributes' => []],
            'planningId' => 42,
        ], $mapper->normalize($block));
    }
}
