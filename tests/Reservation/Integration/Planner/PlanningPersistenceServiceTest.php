<?php

namespace App\Tests\Reservation\Integration\Planner;

use App\Application\Reservation\Planner\Dto\PlanningDto;
use App\Application\Reservation\Planner\Service\CheckPlanningColorAvailabilityService;
use App\Application\Reservation\Planner\Service\CreatePlanningService;
use App\Application\Reservation\Planner\Service\FindPlanningService;
use App\Application\Reservation\Planner\Service\GetPlanningsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PlanningPersistenceServiceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $connection = $this->entityManager->getConnection();
        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        parent::tearDown();
    }

    public function testCreateAndFindServicesUseTheRealDoctrineRepository(): void
    {
        $planning = self::getContainer()->get(CreatePlanningService::class)->create(new PlanningDto(
            title: 'Audit Symfony',
            description: '<p>Conseil</p><script>alert(1)</script>',
            duration: 45,
            gap: 15,
            color: '#123456',
        ));
        $slug = $planning->getSlug();
        $this->entityManager->clear();

        $found = self::getContainer()->get(FindPlanningService::class)->findBySlug((string) $slug);

        self::assertNotNull($planning->getId());
        self::assertNotNull($found);
        self::assertSame('Audit Symfony', $found->getTitle());
        self::assertStringNotContainsString('<script>', (string) $found->getDescription());
    }

    public function testColorAvailabilityUsesPersistedData(): void
    {
        $service = self::getContainer()->get(CheckPlanningColorAvailabilityService::class);
        self::assertTrue($service->isAvailable('#654321'));

        self::getContainer()->get(CreatePlanningService::class)->create(new PlanningDto(
            title: 'Couleur réservée',
            color: '#654321',
        ));

        self::assertFalse($service->isAvailable('#654321'));
    }

    public function testGetPlanningsServiceReturnsPersistedPlanningsOrderedByTitle(): void
    {
        $create = self::getContainer()->get(CreatePlanningService::class);
        $create->create(new PlanningDto(title: 'ZZ Planning intégration', color: '#112233'));
        $create->create(new PlanningDto(title: 'AA Planning intégration', color: '#332211'));

        $matchingTitles = array_values(array_map(
            static fn ($planning): ?string => $planning->getTitle(),
            array_filter(
                self::getContainer()->get(GetPlanningsService::class)->get(),
                static fn ($planning): bool => str_contains((string) $planning->getTitle(), 'Planning intégration'),
            ),
        ));

        self::assertSame(['AA Planning intégration', 'ZZ Planning intégration'], $matchingTitles);
    }
}
