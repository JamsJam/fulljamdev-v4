<?php

namespace App\Tests\Page\Integration\Template;

use App\Application\Page\Block\Library\Hero\ClassicSquare\HeroClassicSquareBlock;
use App\Application\Page\Block\Library\Hero\Shared\HeroDTO;
use App\Application\Page\Element\Cta\CtaDTO;
use App\Application\Page\Element\Image\ImageSource;
use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\Page\Page\Dto\PageDTO;
use App\Application\Page\Page\Form\PageType;
use App\Twig\Components\Page\Block\HeroClassicSquare;
use App\Twig\Components\Page\Block\HeroWithFsImage;
use App\Twig\Components\Page\Block\HeroWithXlImage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;

final class PageTemplateTest extends KernelTestCase
{
    public function testPageTemplateExposesItsPathAsADataAttribute(): void
    {
        self::bootKernel();
        $page = new PageDTO();
        $page->title = 'À propos';
        $page->path = 'about';

        $html = self::getContainer()->get(Environment::class)->render('front/page/show.html.twig', ['page' => $page]);

        self::assertStringContainsString('<main data-page="about">', $html);
    }

    public function testHomepageUsesAccueilWhenItsPathIsEmpty(): void
    {
        self::bootKernel();
        $page = new PageDTO();
        $page->title = 'Accueil';

        $html = self::getContainer()->get(Environment::class)->render('front/page/show.html.twig', ['page' => $page]);

        self::assertStringContainsString('<main data-page="accueil">', $html);
    }

    public function testItRendersHeroFormFragmentWithSymfonyFieldNames(): void
    {
        self::bootKernel();
        $definition = new HeroClassicSquareBlock();
        $page = new PageDTO();
        $page->blocks[] = new PageBlockDTO(null, $definition->type(), $definition->createDefaultData());
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(PageType::class, $page);
        $html = self::getContainer()->get(Environment::class)->render('dashboard/page/_block_form.html.twig', [
            'block' => $form->get('blocks')->get('0')->createView(),
            'definition' => $definition,
        ]);

        self::assertStringContainsString('page[blocks][0][data][title][content]', $html);
        self::assertStringContainsString('page_blocks_0_data_badges', $html);
    }

    public function testHeroTemplateRendersTypedDataAndFiltersUnsafeValues(): void
    {
        self::bootKernel();
        $hero = new HeroDTO();
        $hero->title->content = 'Un titre sûr';
        $hero->title->attributes = ['id' => 'main-title', 'onclick' => 'alert(1)'];
        $hero->text->content = 'Une description.';
        $hero->cta1 = new CtaDTO();
        $hero->cta1->label = 'Action';
        $hero->cta1->href = 'javascript:alert(1)';
        $hero->image->source = ImageSource::URL;
        $hero->image->url = 'https://example.com/hero.jpg';
        $hero->image->alt = 'Illustration';
        $html = self::getContainer()->get(Environment::class)->render(
            'components/page/block/hero/classic-square/Hero.html.twig',
            ['data' => $hero, 'blockId' => 42, 'this' => new HeroClassicSquare()],
        );

        self::assertStringContainsString('data-block-id="42"', $html);
        self::assertStringContainsString('id="main-title"', $html);
        self::assertStringNotContainsString('onclick', $html);
        self::assertStringContainsString('href="#"', $html);
    }

    public function testHeroWithXlImageRendersItsReverseLayout(): void
    {
        self::bootKernel();
        $hero = new HeroDTO();
        $hero->title->content = 'Une grande image';
        $hero->text->content = 'Un contenu éditorial.';
        $hero->image->source = ImageSource::URL;
        $hero->image->url = 'https://example.com/hero-xl.jpg';
        $hero->image->alt = 'Équipe en réunion';
        $hero->reverse = true;

        $html = self::getContainer()->get(Environment::class)->render(
            'components/page/block/hero/with-xl-image/HeroWithXlImage.html.twig',
            ['data' => $hero, 'blockId' => 84, 'this' => new HeroWithXlImage()],
        );

        self::assertStringContainsString('hero-with-xl-image--reverse', $html);
        self::assertStringContainsString('src="https://example.com/hero-xl.jpg"', $html);
        self::assertStringContainsString('data-block-id="84"', $html);
    }

    public function testHeroWithFsImageRendersItsFullSizeMedia(): void
    {
        self::bootKernel();
        $hero = new HeroDTO();
        $hero->title->content = 'Une image plein cadre';
        $hero->text->content = 'Le média occupe tout son conteneur.';
        $hero->image->source = ImageSource::URL;
        $hero->image->url = 'https://example.com/hero-fs.jpg';
        $hero->image->alt = 'Image plein cadre';

        $html = self::getContainer()->get(Environment::class)->render(
            'components/page/block/hero/with-fs-image/HeroWithFsImage.html.twig',
            ['data' => $hero, 'blockId' => 126, 'this' => new HeroWithFsImage()],
        );

        self::assertStringContainsString('class="hero-with-fs-image"', $html);
        self::assertStringContainsString('src="https://example.com/hero-fs.jpg"', $html);
        self::assertStringContainsString('data-block-id="126"', $html);
    }
}
