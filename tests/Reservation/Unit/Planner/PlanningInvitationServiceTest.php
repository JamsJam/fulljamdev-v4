<?php

namespace App\Tests\Reservation\Unit\Planner;

use App\Application\Reservation\Planner\Service\PlanningInvitationService;
use App\Entity\Reservation\Planning;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class PlanningInvitationServiceTest extends TestCase
{
    public function testLinkIsScopedToOnePlanningAndExpiresAtTheDeadline(): void
    {
        $clock = new MockClock('2026-09-09 12:00:00');
        $service = new PlanningInvitationService($clock, 'test-secret');
        $planning = $this->planning(1);
        $token = $service->create($planning, 1);
        self::assertFalse($service->canAccess($planning));
        self::assertTrue($service->canAccess($planning, $token));
        self::assertFalse($service->canAccess($this->planning(2), $token));
        self::assertFalse($service->canAccess($planning, '9999999999.'.explode('.', $token)[1]));
        self::assertFalse($service->canAccess($planning, $token.'a'));
        $clock->sleep(3599);
        self::assertTrue($service->canAccess($planning, $token));
        $clock->sleep(1);
        self::assertFalse($service->canAccess($planning, $token));
    }

    public function testInactiveAndArchivedPlanningsRejectEvenValidLinks(): void
    {
        $service = new PlanningInvitationService(new MockClock(), 'test-secret');
        $planning = $this->planning(1);
        $token = $service->create($planning, 72);
        self::assertTrue($service->canAccess($planning, $token));
        $planning->setIsActive(false)->setIsOnline(true);
        self::assertFalse($service->canAccess($planning, $token));
        $planning->setIsActive(true);
        self::assertTrue($service->canAccess($planning));
        $planning->archive();
        self::assertFalse($service->canAccess($planning, $token));
    }

    #[DataProvider('invalidDurations')]
    public function testDurationMustBeBetweenOneAndSeventyTwoHours(int $hours): void
    {
        $service = new PlanningInvitationService(new MockClock(), 'test-secret');
        $this->expectException(\InvalidArgumentException::class);
        $service->create($this->planning(1), $hours);
    }

    public static function invalidDurations(): iterable
    {
        yield [0];
        yield [73];
    }

    private function planning(int $id): Planning
    {
        $planning = (new Planning())->setSlug('private-'.$id)->setIsActive(true)->setIsOnline(false);
        (new \ReflectionProperty(Planning::class, 'id'))->setValue($planning, $id);

        return $planning;
    }
}
