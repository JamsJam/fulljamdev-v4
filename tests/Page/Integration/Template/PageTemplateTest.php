<?php

namespace App\Tests\Page\Integration\Template;

use App\Application\Page\Block\Hero\Main\HeroBlock;
use App\Application\Page\Block\Hero\Main\HeroDTO;
use App\Application\Page\Element\Badge\BadgeDTO;
use App\Application\Page\Element\Cta\CtaDTO;
use App\Application\Page\Element\Image\ImageSource;
use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\Page\Page\Dto\PageDTO;
use App\Application\Page\Page\Form\PageType;
use App\Twig\Components\Page\Block\Hero;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;

final class PageTemplateTest extends KernelTestCase
{
    public function testItRendersHeroFormFragmentWithSymfonyFieldNames(): void
    {
        self::bootKernel();
        $definition = new HeroBlock();
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
        $badge = new BadgeDTO();
        $badge->label = 'Symfony';
        $hero->badges[] = $badge;

        $html = self::getContainer()->get(Environment::class)->render('components/page/block/Hero.html.twig', ['data' => $hero, 'this' => new Hero()]);

        self::assertStringContainsString('id="main-title"', $html);
        self::assertStringNotContainsString('onclick', $html);
        self::assertStringContainsString('href="#"', $html);
        self::assertStringContainsString('Symfony', $html);
    }
}
