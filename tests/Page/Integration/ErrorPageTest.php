<?php

namespace App\Tests\Page\Integration;

use Symfony\Bridge\Twig\ErrorRenderer\TwigErrorRenderer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class ErrorPageTest extends KernelTestCase
{
    public function testNativeRendererUsesTheCustomPageFor404And500(): void
    {
        self::bootKernel();
        $renderer = new TwigErrorRenderer(self::getContainer()->get(Environment::class), debug: false);

        foreach ([
            [new NotFoundHttpException('Private details'), 404, 'Cette page est introuvable.'],
            [new \RuntimeException('Private details'), 500, 'Le site est momentanément indisponible.'],
        ] as [$exception, $status, $heading]) {
            $result = $renderer->render($exception);

            self::assertSame($status, $result->getStatusCode());
            self::assertStringContainsString($heading, $result->getAsString());
            self::assertStringContainsString('noindex, nofollow', $result->getAsString());
            self::assertStringNotContainsString('Private details', $result->getAsString());
        }
    }
}
