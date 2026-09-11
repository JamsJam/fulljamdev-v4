<?php

namespace App\Tests\Page\Integration\Block;

use App\Application\Page\Block\Mapper\BlockDataMapper;
use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\Page\Page\Dto\PageDTO;
use App\Application\SEO\JsonLd\Builder\JsonLdBuilder;
use App\Application\SEO\JsonLd\Context\BuiltPageContextProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BlockJsonLdIntegrationTest extends KernelTestCase
{
    public function testStoredBlocksReachTheGraphThroughAutoconfiguredContributors(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $mapper = $container->get(BlockDataMapper::class);
        $page = new PageDTO();
        $page->title = 'Services';
        $page->path = 'services';
        $page->blocks = [
            new PageBlockDTO(11, 'faq.main', $mapper->denormalize('faq.main', ['items' => [['question' => 'Comment ?', 'answer' => 'Ensemble.']]])),
            new PageBlockDTO(12, 'services.main', $mapper->denormalize('services.main', ['cards' => [['title' => 'Développement', 'text' => 'Sur mesure.']]])),
        ];
        $provider = $container->get(BuiltPageContextProvider::class);
        $context = $provider->provide('app_front_page', ['page' => $page]);
        $graph = $container->get(JsonLdBuilder::class)->build($context)['@graph'];
        self::assertSame(['WebPage', 'FAQPage'], $graph[0]['@type']);
        self::assertSame('Question', $graph[1]['@type']);
        self::assertSame('ItemList', $graph[2]['@type']);
        self::assertSame('Service', $graph[3]['@type']);
        self::assertSame([['@id' => $graph[2]['@id']]], $graph[0]['mentions']);
        $page->seo->noIndex = true;
        self::assertSame([], $provider->provide('app_front_page', ['page' => $page])->contributions);
    }
}
