<?php

namespace App\Application\Page\Page\Service;

use App\Entity\Page\Page;
use App\Repository\Page\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

final readonly class DeletePageService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PageRepository $pages,
        private Filesystem $filesystem,
        #[Autowire(param: 'page_upload_directory')]
        private string $uploadDirectory,
    ) {
    }

    public function delete(Page $page): void
    {
        $mediaFilenames = $this->mediaFilenames($page);

        $this->entityManager->remove($page);
        $this->entityManager->flush();

        $referencedFilenames = $this->referencedFilenames();
        foreach ($mediaFilenames as $filename => $_) {
            if (isset($referencedFilenames[$filename])) {
                continue;
            }

            $this->removeFile($filename);
        }
    }

    /** @return array<string, true> */
    private function mediaFilenames(Page $page): array
    {
        $filenames = [];

        foreach ($page->getBlocks() as $block) {
            $this->collectBlockMedia($block->getData(), $filenames);
        }

        $this->collectSeoMedia($page->getSeo(), $filenames);

        return $filenames;
    }

    /** @return array<string, true> */
    private function referencedFilenames(): array
    {
        $filenames = [];

        foreach ($this->pages->findAll() as $page) {
            foreach ($page->getBlocks() as $block) {
                $this->collectBlockMedia($block->getData(), $filenames);
            }

            $this->collectSeoMedia($page->getSeo(), $filenames);
        }

        return $filenames;
    }

    /** @param array<string, true> $filenames */
    private function collectBlockMedia(mixed $value, array &$filenames): void
    {
        if (!is_array($value)) {
            return;
        }

        foreach ($value as $key => $item) {
            if ('mediaId' === $key && is_string($item) && '' !== $item) {
                $filenames[$item] = true;

                continue;
            }

            $this->collectBlockMedia($item, $filenames);
        }
    }

    /** @param array<string, mixed> $seo
     *  @param array<string, true> $filenames
     */
    private function collectSeoMedia(array $seo, array &$filenames): void
    {
        $socialImagePath = $seo['socialImagePath'] ?? null;
        if (!is_string($socialImagePath) || !str_starts_with($socialImagePath, '/uploads/pages/')) {
            return;
        }

        $filenames[basename($socialImagePath)] = true;
    }

    private function removeFile(string $filename): void
    {
        if (basename($filename) !== $filename) {
            return;
        }

        $path = $this->uploadDirectory.'/'.$filename;
        if ($this->filesystem->exists($path)) {
            $this->filesystem->remove($path);
        }
    }
}
