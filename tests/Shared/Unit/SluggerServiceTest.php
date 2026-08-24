<?php

namespace App\Tests\Shared\Unit;

use App\Service\SluggerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class SluggerServiceTest extends TestCase
{
    public function testItSlugifiesAndLimitsText(): void
    {
        $slugger = new SluggerService(new AsciiSlugger());

        self::assertSame('appel-decouverte', $slugger->slugify('Appel découverte'));
        self::assertSame('appel', $slugger->slugify('Appel découverte', 5));
    }

    public function testItNormalizesAccentsSpacesAndSymbols(): void
    {
        $slugger = new SluggerService(new AsciiSlugger());

        self::assertSame('developpement-web-seo', $slugger->slugify('  Développement web & SEO  '));
    }

    public function testItReturnsAnEmptySlugForContentWithoutLettersOrNumbers(): void
    {
        self::assertSame('', (new SluggerService(new AsciiSlugger()))->slugify('---'));
    }

    public function testItHandlesAZeroLengthLimit(): void
    {
        self::assertSame('', (new SluggerService(new AsciiSlugger()))->slugify('Accueil', 0));
    }
}
