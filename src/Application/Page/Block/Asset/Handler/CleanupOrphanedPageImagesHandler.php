<?php

namespace App\Application\Page\Block\Asset\Handler;

use App\Application\Page\Block\Asset\CleanupOrphanedPageImagesService;
use App\Application\Page\Block\Asset\Message\CleanupOrphanedPageImages;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CleanupOrphanedPageImagesHandler
{
    public function __construct(
        private CleanupOrphanedPageImagesService $cleanupService,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(CleanupOrphanedPageImages $message): void
    {
        $removedFiles = $this->cleanupService->cleanup();

        $this->logger->info('Nettoyage quotidien des images du page builder terminé.', [
            'removed_count' => count($removedFiles),
            'removed_files' => $removedFiles,
        ]);
    }
}
