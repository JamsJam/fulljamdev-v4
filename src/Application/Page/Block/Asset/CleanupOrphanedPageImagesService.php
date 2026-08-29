<?php

namespace App\Application\Page\Block\Asset;

use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

final readonly class CleanupOrphanedPageImagesService
{
    private const GRACE_PERIOD_SECONDS = 86400;

    public function __construct(
        private PageBlockDataProviderInterface $blockDataProvider,
        private Filesystem $filesystem,
        private ClockInterface $clock,
        #[Autowire(param: 'page_upload_directory')]
        private string $uploadDirectory,
    ) {
    }

    /** @return list<string> Names of the deleted files. */
    public function cleanup(): array
    {
        if (!is_dir($this->uploadDirectory)) {
            return [];
        }

        $referencedFiles = [];
        foreach ($this->blockDataProvider->provide() as $blockData) {
            $this->collectMediaIds($blockData, $referencedFiles);
        }

        $oldestAllowedTimestamp = $this->clock->now()->getTimestamp() - self::GRACE_PERIOD_SECONDS;
        $removedFiles = [];
        $files = new \FilesystemIterator($this->uploadDirectory, \FilesystemIterator::SKIP_DOTS);

        foreach ($files as $file) {
            if (!$file->isFile() || $file->isLink()) {
                continue;
            }

            $filename = $file->getFilename();
            if (isset($referencedFiles[$filename]) || $file->getMTime() > $oldestAllowedTimestamp) {
                continue;
            }

            $this->filesystem->remove($file->getPathname());
            $removedFiles[] = $filename;
        }

        sort($removedFiles);

        return $removedFiles;
    }

    /** @param array<string, true> $mediaIds */
    private function collectMediaIds(mixed $value, array &$mediaIds): void
    {
        if (!is_array($value)) {
            return;
        }

        foreach ($value as $key => $item) {
            if ('mediaId' === $key && is_string($item) && '' !== $item) {
                $mediaIds[$item] = true;
                continue;
            }

            $this->collectMediaIds($item, $mediaIds);
        }
    }
}
