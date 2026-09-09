<?php

namespace App\Tests\Page\Integration\Block;

use App\Application\Page\Block\Library\Cta\Center\CtaCenterDTO;
use App\Application\Page\Block\Mapper\BlockDataMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CtaBlockDataMapperTest extends KernelTestCase
{
    public function testItMapsCtaBlockData(): void
    {
        self::bootKernel();
        $mapper = self::getContainer()->get(BlockDataMapper::class);
        $data = [
            'title' => ['content' => 'Parlons de votre projet', 'level' => 'h2', 'attributes' => []],
            'text' => ['content' => 'Transformons votre idée en produit.', 'attributes' => []],
            'cta' => [
                'label' => 'Nous contacter',
                'href' => '/contact',
                'target' => 'url',
                'routeName' => null,
                'routeParameters' => [],
                'attributes' => [],
            ],
        ];

        $block = $mapper->denormalize('cta.main', $data);

        self::assertInstanceOf(CtaCenterDTO::class, $block);
        self::assertSame('Nous contacter', $block->cta->label);
        self::assertSame($data, $mapper->normalize($block));
    }
}
