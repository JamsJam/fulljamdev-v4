<?php

namespace App\Tests\Legal\Integration;

use App\Application\Legal\Dto\LegalPageDTO;
use App\Application\Legal\Form\LegalPageType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

final class LegalPageTypeTest extends KernelTestCase
{
    public function testLegalPagesUseOneRestrictedRoute(): void
    {
        self::bootKernel();
        $route = self::getContainer()->get(RouterInterface::class)->getRouteCollection()->get('app_front_legal');

        self::assertSame('/legal/{slug}', $route?->getPath());
        self::assertSame(
            'mention-legal|politique-confidentialite|politique-cookies',
            $route?->getRequirement('slug'),
        );
    }

    public function testEditorOnlyKeepsLegalTextElements(): void
    {
        self::bootKernel();
        $dto = new LegalPageDTO();
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(LegalPageType::class, $dto, ['csrf_protection' => false]);

        self::assertSame('legal', $form->get('content')->getConfig()->getOption('attr')['data-suneditor-profile-value']);

        $form->submit([
            'title' => 'Mentions légales',
            'content' => '<h1>Interdit</h1><h2>Autorisé</h2><p>Texte</p><ul><li>Élément</li></ul><img src="x"><code>code</code><script>alert(1)</script>',
        ]);

        self::assertTrue($form->isValid(), (string) $form->getErrors(true));
        self::assertStringContainsString('<h2>Autorisé</h2>', $dto->content);
        self::assertStringContainsString('<ul><li>Élément</li></ul>', $dto->content);
        self::assertStringNotContainsString('<h1', $dto->content);
        self::assertStringNotContainsString('<img', $dto->content);
        self::assertStringNotContainsString('<code', $dto->content);
        self::assertStringNotContainsString('<script', $dto->content);
    }
}
