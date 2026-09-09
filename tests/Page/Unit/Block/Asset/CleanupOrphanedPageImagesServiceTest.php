<?php

namespace App\Tests\Page\Unit\Block\Asset;

use App\Application\Page\Block\Asset\CleanupOrphanedPageImagesService;
use App\Application\Page\Block\Asset\PageBlockDataProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Filesystem\Filesystem;

final class CleanupOrphanedPageImagesServiceTest extends TestCase
{
    private string $uploadDirectory;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->uploadDirectory = sys_get_temp_dir().'/page-images-cleanup-'.bin2hex(random_bytes(8));
        $this->filesystem->mkdir($this->uploadDirectory);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->uploadDirectory);
    }

    public function testItOnlyRemovesOldUnreferencedFiles(): void
    {
        $this->createFile('referenced.jpg', '2026-08-20 12:00:00');
        $this->createFile('nested-reference.webp', '2026-08-20 12:00:00');
        $this->createFile('orphan.png', '2026-08-20 12:00:00');
        $this->createFile('recent-orphan.gif', '2026-08-29 02:30:00');
        $this->filesystem->mkdir($this->uploadDirectory.'/preserved-directory');

        $provider = new class implements PageBlockDataProviderInterface {
            public function provide(): iterable
            {
                yield [
                    'image' => ['mediaId' => 'referenced.jpg'],
                    'cards' => [['image' => ['mediaId' => 'nested-reference.webp']]],
                ];
            }
        };
        $service = new CleanupOrphanedPageImagesService(
            $provider,
            $this->filesystem,
            new MockClock('2026-08-29 03:00:00 Europe/Paris'),
            $this->uploadDirectory,
        );

        self::assertSame(['orphan.png'], $service->cleanup());
        self::assertFileExists($this->uploadDirectory.'/referenced.jpg');
        self::assertFileExists($this->uploadDirectory.'/nested-reference.webp');
        self::assertFileExists($this->uploadDirectory.'/recent-orphan.gif');
        self::assertDirectoryExists($this->uploadDirectory.'/preserved-directory');
        self::assertFileDoesNotExist($this->uploadDirectory.'/orphan.png');
    }

    public function testItDoesNothingWhenTheUploadDirectoryDoesNotExist(): void
    {
        $this->filesystem->remove($this->uploadDirectory);
        $provider = new class implements PageBlockDataProviderInterface {
            public function provide(): iterable
            {
                throw new \LogicException('The provider must not be called.');
            }
        };
        $service = new CleanupOrphanedPageImagesService(
            $provider,
            $this->filesystem,
            new MockClock(),
            $this->uploadDirectory,
        );

        self::assertSame([], $service->cleanup());
    }

    private function createFile(string $filename, string $modifiedAt): void
    {
        $path = $this->uploadDirectory.'/'.$filename;
        file_put_contents($path, 'image');
        touch($path, (new \DateTimeImmutable($modifiedAt, new \DateTimeZone('Europe/Paris')))->getTimestamp());
    }
}
