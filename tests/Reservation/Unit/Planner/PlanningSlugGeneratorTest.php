<?php

namespace App\Tests\Reservation\Unit\Planner;

use App\Application\Reservation\Planner\Service\PlanningSlugGenerator;
use App\Service\SluggerService;
use App\Service\UuidGeneratorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class PlanningSlugGeneratorTest extends TestCase
{
    public function testItGeneratesTheExpectedPlanningSlugFormat(): void
    {
        $generator = new PlanningSlugGenerator(
            new SluggerService(new AsciiSlugger()),
            new UuidGeneratorService(),
        );

        self::assertMatchesRegularExpression(
            '/^[a-f0-9]{10}-appel-decouverte-strategie-[a-f0-9]{10}$/',
            $generator->generate('Appel découverte & stratégie'),
        );
    }

    public function testItUsesTheFallbackForAnEmptyTitle(): void
    {
        self::assertMatchesRegularExpression(
            '/^[a-f0-9]{10}-planning-[a-f0-9]{10}$/',
            $this->generator()->generate('---'),
        );
    }

    public function testItLimitsTheTitlePartToSeventyCharacters(): void
    {
        $slug = $this->generator()->generate(str_repeat('a', 100));
        $parts = explode('-', $slug);

        self::assertSame(70, strlen($parts[1]));
    }

    private function generator(): PlanningSlugGenerator
    {
        return new PlanningSlugGenerator(
            new SluggerService(new AsciiSlugger()),
            new UuidGeneratorService(),
        );
    }
}
