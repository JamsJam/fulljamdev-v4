<?php

namespace App\Tests\Service;

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
}
