<?php

namespace App\Tests\Page\Integration\Writer;

use App\Application\Page\Block\Asset\BlockAssetProcessor;
use App\Application\Page\Block\Hero\Main\HeroDTO;
use App\Application\Page\Block\Mapper\BlockDataMapper;
use App\Application\Page\Block\Registry\BlockRegistry;
use App\Application\Page\Element\Image\ImageSource;
use App\Application\Page\Page\Builder\PageBuilder;
use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\Page\Page\Dto\PageDTO;
use App\Application\Page\Page\Writer\PageWriter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

final class PageWriterTest extends KernelTestCase
{
    public function testItWritesOrderedBlocksAndBuildsTypedPageBack(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())->method('persist');
        $entityManager->expects(self::once())->method('flush');
        $mapper = $container->get(BlockDataMapper::class);
        $serializer = $container->get(SerializerInterface::class);
        $writer = new PageWriter($container->get(BlockRegistry::class), $mapper, $serializer, $entityManager, $container->get(BlockAssetProcessor::class));

        $hero = new HeroDTO();
        $hero->title->content = 'Accueil';
        $hero->text->content = 'Bienvenue';
        $hero->image->source = ImageSource::URL;
        $hero->image->url = 'https://example.com/hero.jpg';
        $hero->image->alt = 'Hero';
        $dto = new PageDTO();
        $dto->title = 'Accueil';
        $dto->path = 'accueil';
        $dto->blocks[] = new PageBlockDTO(null, 'hero.main', $hero);

        $page = $writer->save($dto);
        $rebuilt = (new PageBuilder($mapper, $serializer))->build($page);

        self::assertCount(1, $page->getBlocks());
        self::assertSame(0, $page->getBlocks()->first()->getPosition());
        self::assertInstanceOf(HeroDTO::class, $rebuilt->blocks[0]->data);
        self::assertSame('Accueil', $rebuilt->blocks[0]->data->title->content);
    }
}
