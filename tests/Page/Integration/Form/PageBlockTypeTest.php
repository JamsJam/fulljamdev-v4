<?php

namespace App\Tests\Page\Integration\Form;

use App\Application\Page\Block\Hero\Main\HeroDTO;
use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\Page\Page\Dto\PageDTO;
use App\Application\Page\Page\Form\PageBlockType;
use App\Application\Page\Page\Form\PageType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

final class PageBlockTypeTest extends KernelTestCase
{
    public function testItUsesHeroFormAtLoadAndSubmit(): void
    {
        self::bootKernel();
        $block = new PageBlockDTO(null, 'hero.main', new HeroDTO());
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(PageBlockType::class, $block, ['csrf_protection' => false]);

        self::assertTrue($form->has('data'));
        self::assertSame(HeroDTO::class, $form->get('data')->getConfig()->getDataClass());

        $form->submit([
            'id' => '', 'type' => 'hero.main',
            'data' => [
                'title' => ['content' => 'Titre', 'level' => 'h1', 'attributes' => []],
                'text' => ['content' => 'Texte', 'attributes' => []],
                'cta1' => [], 'cta2' => [],
                'image' => ['source' => 'url', 'url' => 'https://example.com/image.jpg', 'alt' => 'Image'],
                'badges' => [],
            ],
        ]);

        self::assertTrue($form->isSynchronized());
        self::assertInstanceOf(HeroDTO::class, $block->data);
        self::assertSame('Titre', $block->data->title->content);
    }

    public function testItRejectsUnknownSubmittedType(): void
    {
        self::bootKernel();
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(PageBlockType::class, new PageBlockDTO(), ['csrf_protection' => false]);

        $this->expectException(\InvalidArgumentException::class);
        $form->submit(['type' => 'unknown.block', 'data' => []]);
    }

    public function testPageCollectionCreatesNewTypedBlockFromSubmittedType(): void
    {
        self::bootKernel();
        $page = new PageDTO();
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(PageType::class, $page, ['csrf_protection' => false]);
        $form->submit([
            'title' => 'Accueil', 'path' => 'accueil',
            'seo' => ['title' => '', 'description' => '', 'canonicalUrl' => '', 'noIndex' => '0'],
            'blocks' => [
                4 => [
                    'id' => '', 'type' => 'hero.main',
                    'data' => [
                        'title' => ['content' => 'Premier Hero', 'level' => 'h1', 'attributes' => []],
                        'text' => ['content' => 'Premier texte', 'attributes' => []],
                        'cta1' => [], 'cta2' => [],
                        'image' => ['source' => 'url', 'url' => 'https://example.com/first.jpg', 'alt' => 'Première image'],
                        'badges' => [],
                    ],
                ],
                9 => [
                    'id' => '', 'type' => 'hero.main',
                    'data' => [
                        'title' => ['content' => 'Second Hero', 'level' => 'h2', 'attributes' => []],
                        'text' => ['content' => 'Second texte', 'attributes' => []],
                        'cta1' => [], 'cta2' => [],
                        'image' => ['source' => 'url', 'url' => 'https://example.com/second.jpg', 'alt' => 'Seconde image'],
                        'badges' => [],
                    ],
                ],
            ],
        ]);

        self::assertTrue($form->isSynchronized(), (string) $form->getErrors(true));
        self::assertCount(2, $page->blocks);
        self::assertInstanceOf(HeroDTO::class, $page->blocks[4]->data);
        self::assertInstanceOf(HeroDTO::class, $page->blocks[9]->data);
        self::assertSame('Premier Hero', $page->blocks[4]->data->title->content);
        self::assertSame('Second Hero', $page->blocks[9]->data->title->content);
    }
}
