<?php

namespace App\Tests\Page\Integration\Service;

use App\Application\Page\Page\Dto\PageDTO;
use App\Application\Page\Page\Service\CheckPagePathAvailabilityService;
use App\Application\Page\Page\Service\FindPageService;
use App\Application\Page\Page\Service\GetPagesService;
use App\Application\Page\Page\Service\SavePageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PagePersistenceServiceTest extends KernelTestCase
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

    public function testSaveAndFindServicesUseTheRealDoctrineRepository(): void
    {
        $dto = $this->pageDto('Page intégration', 'integration/page-persistence');
        $page = self::getContainer()->get(SavePageService::class)->save($dto);
        $this->entityManager->clear();

        $found = self::getContainer()->get(FindPageService::class)->findByPath($dto->path);

        self::assertNotNull($page->getId());
        self::assertNotNull($found);
        self::assertSame('Page intégration', $found->getTitle());
        self::assertSame($page->getId(), $found->getId());
    }

    public function testPathAvailabilityDetectsAnotherPersistedPageButAllowsTheCurrentPage(): void
    {
        $page = self::getContainer()->get(SavePageService::class)->save(
            $this->pageDto('Chemin unique', 'integration/unique-path'),
        );
        $service = self::getContainer()->get(CheckPagePathAvailabilityService::class);

        self::assertTrue($service->isUsedByAnotherPage('integration/unique-path'));
        self::assertFalse($service->isUsedByAnotherPage('integration/unique-path', $page));
        self::assertFalse($service->isUsedByAnotherPage('integration/free-path'));
    }

    public function testGetPagesServiceReturnsPersistedPagesOrderedByTitle(): void
    {
        $save = self::getContainer()->get(SavePageService::class);
        $save->save($this->pageDto('ZZ Integration', 'integration/z-page'));
        $save->save($this->pageDto('AA Integration', 'integration/a-page'));

        $matchingTitles = array_values(array_map(
            static fn ($page): string => $page->getTitle(),
            array_filter(
                self::getContainer()->get(GetPagesService::class)->get(),
                static fn ($page): bool => str_ends_with($page->getPath(), '-page'),
            ),
        ));

        self::assertSame(['AA Integration', 'ZZ Integration'], $matchingTitles);
    }

    private function pageDto(string $title, string $path): PageDTO
    {
        $dto = new PageDTO();
        $dto->title = $title;
        $dto->path = $path;

        return $dto;
    }
}
