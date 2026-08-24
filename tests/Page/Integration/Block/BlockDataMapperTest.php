<?php

namespace App\Tests\Page\Integration\Block;

use App\Application\Page\Block\Hero\Main\HeroDTO;
use App\Application\Page\Block\Mapper\BlockDataMapper;
use App\Application\Page\Element\Heading\HeadingLevel;
use App\Application\Page\Element\Image\ImageSource;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BlockDataMapperTest extends KernelTestCase
{
    public function testItMapsHeroJsonToTypedDtoAndBack(): void
    {
        self::bootKernel();
        $mapper = self::getContainer()->get(BlockDataMapper::class);
        $data = [
            'title' => ['content' => 'Construisons votre produit', 'level' => 'h1', 'attributes' => ['id' => 'hero-title']],
            'text' => ['content' => 'Une proposition claire.', 'attributes' => []],
            'cta1' => [
                'label' => 'Démarrer',
                'href' => '/contact',
                'target' => 'url',
                'routeName' => null,
                'routeParameters' => [],
                'attributes' => ['aria-label' => 'Nous contacter'],
            ],
            'cta2' => [
                'label' => '',
                'href' => '',
                'target' => 'url',
                'routeName' => null,
                'routeParameters' => [],
                'attributes' => [],
            ],
            'image' => ['source' => 'url', 'mediaId' => null, 'url' => 'https://example.com/hero.jpg', 'alt' => 'Équipe au travail', 'title' => null],
            'badges' => [['label' => 'Symfony']],
        ];

        $hero = $mapper->denormalize('hero.main', $data);

        self::assertInstanceOf(HeroDTO::class, $hero);
        self::assertSame(HeadingLevel::H1, $hero->title->level);
        self::assertSame(ImageSource::URL, $hero->image->source);
        self::assertSame('Symfony', $hero->badges[0]->label);
        self::assertSame($data, $mapper->normalize($hero));
    }
}
