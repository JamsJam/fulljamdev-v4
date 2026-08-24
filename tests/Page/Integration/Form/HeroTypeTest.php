<?php

namespace App\Tests\Page\Integration\Form;

use App\Application\Page\Block\Hero\Main\HeroDTO;
use App\Application\Page\Block\Hero\Main\HeroType;
use App\Application\Page\Element\Cta\CtaDTO;
use App\Application\Page\Element\Cta\CtaTarget;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

final class HeroTypeTest extends KernelTestCase
{
    public function testItSubmitsValidHeroWithBadgesAndOptionalCtas(): void
    {
        self::bootKernel();
        $hero = new HeroDTO();
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(HeroType::class, $hero, ['csrf_protection' => false]);
        $form->submit([
            'title' => ['content' => 'Mon titre', 'level' => 'h1', 'attributes' => [
                ['name' => 'id', 'value' => 'main-title'],
            ]],
            'text' => ['content' => 'Mon texte', 'attributes' => [
                ['name' => 'aria-label', 'value' => 'Présentation'],
                ['name' => 'data-section', 'value' => 'hero'],
            ]],
            'cta1' => ['label' => 'Me contacter', 'href' => '/contact', 'attributes' => []],
            'cta2' => ['label' => '', 'href' => '', 'attributes' => []],
            'image' => ['source' => 'url', 'mediaId' => '', 'url' => 'https://example.com/hero.jpg', 'alt' => 'Illustration', 'title' => ''],
            'badges' => [['label' => 'Symfony'], ['label' => 'SEO']],
        ]);

        self::assertTrue($form->isValid(), (string) $form->getErrors(true));
        self::assertCount(2, $hero->badges);
        self::assertSame(['aria-label' => 'Présentation', 'data-section' => 'hero'], $hero->text->attributes);
        self::assertInstanceOf(CtaDTO::class, $hero->cta2);
        self::assertSame('', $hero->cta2->label);
        self::assertSame(['id' => 'main-title'], $hero->title->attributes);
    }

    public function testItRejectsUnsafeAttributesAndTooManyBadges(): void
    {
        self::bootKernel();
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(HeroType::class, new HeroDTO(), ['csrf_protection' => false]);
        $form->submit([
            'title' => ['content' => 'Titre', 'level' => 'h1', 'attributes' => [
                ['name' => 'onclick', 'value' => 'alert(1)'],
            ]],
            'text' => ['content' => 'Texte', 'attributes' => []],
            'cta1' => [], 'cta2' => [],
            'image' => ['source' => 'url', 'url' => 'https://example.com/image.jpg', 'alt' => 'Image'],
            'badges' => array_fill(0, 4, ['label' => 'Badge']),
        ]);

        self::assertFalse($form->isValid());
    }

    public function testItSubmitsACtaTargetingAnApplicationRoute(): void
    {
        self::bootKernel();
        $hero = new HeroDTO();
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(HeroType::class, $hero, ['csrf_protection' => false]);
        $form->submit([
            'title' => ['content' => 'Titre', 'level' => 'h1', 'attributes' => []],
            'text' => ['content' => 'Texte', 'attributes' => []],
            'cta1' => [
                'label' => 'Accueil',
                'target' => 'route',
                'routeName' => 'app_home',
                'routeParameters' => [],
                'href' => '',
                'attributes' => [],
            ],
            'cta2' => [],
            'image' => ['source' => 'url', 'url' => 'https://example.com/image.jpg', 'alt' => 'Image'],
            'badges' => [],
        ]);

        self::assertTrue($form->isValid(), (string) $form->getErrors(true));
        self::assertSame(CtaTarget::ROUTE, $hero->cta1?->target);
        self::assertSame('app_home', $hero->cta1?->routeName);
    }
}
