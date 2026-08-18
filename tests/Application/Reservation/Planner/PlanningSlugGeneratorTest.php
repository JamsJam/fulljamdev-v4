<?php

namespace App\Tests\Application\Reservation\Planner;

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
}
