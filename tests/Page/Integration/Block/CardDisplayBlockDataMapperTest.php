<?php

namespace App\Tests\Page\Integration\Block;

use App\Application\Page\Block\Library\CardDisplay\Shared\CardDisplayDTO;
use App\Application\Page\Block\Mapper\BlockDataMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CardDisplayBlockDataMapperTest extends KernelTestCase
{
    public function testItReadsLegacyServicesData(): void
    {
        self::bootKernel();
        $mapper = self::getContainer()->get(BlockDataMapper::class);
        $legacyData = [
            'title' => ['content' => 'Nos services', 'level' => 'h2', 'attributes' => []],
            'text' => ['content' => 'Nos expertises.', 'attributes' => []],
            'cards' => [['title' => 'Ancienne carte']],
        ];

        $block = $mapper->denormalize('services.main', $legacyData);

        self::assertInstanceOf(CardDisplayDTO::class, $block);
        self::assertSame('', $block->cta->label);
        self::assertSame('Ancienne carte', $block->cards[0]->title);
    }

    public function testItMapsOnlyBlockContentAndNotDynamicCards(): void
    {
        self::bootKernel();
        $mapper = self::getContainer()->get(BlockDataMapper::class);
        $data = [
            'title' => ['content' => 'Projets mis en avant', 'level' => 'h2', 'attributes' => []],
            'text' => ['content' => 'Découvrez une sélection de projets.', 'attributes' => []],
            'cta' => [
                'label' => 'Tous les projets',
                'href' => '/projets',
                'target' => 'url',
                'routeName' => null,
                'routeParameters' => [],
                'attributes' => [],
            ],
        ];

        $block = $mapper->denormalize('card_display.with_image', $data);

        self::assertInstanceOf(CardDisplayDTO::class, $block);
        self::assertSame([], $block->cards);
        self::assertSame('static', $mapper->normalize($block)['source']);
    }
}
