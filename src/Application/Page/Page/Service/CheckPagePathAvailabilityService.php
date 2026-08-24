<?php

namespace App\Application\Page\Page\Service;

use App\Application\Page\Page\Provider\PageProvider;
use App\Application\Page\Page\Routing\PagePathValidator;
use App\Entity\Page\Page;

final readonly class CheckPagePathAvailabilityService
{
    public function __construct(
        private PageProvider $provider,
        private PagePathValidator $pathValidator,
    ) {
    }

    public function isUsedByAnotherPage(string $path, ?Page $page = null): bool
    {
        return $this->provider->pathIsUsedByAnotherPage($path, $page);
    }

    public function conflictsWithApplicationRoute(string $path): bool
    {
        return !$this->pathValidator->isAvailable($path);
    }
}
