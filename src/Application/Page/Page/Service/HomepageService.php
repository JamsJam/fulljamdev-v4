<?php

namespace App\Application\Page\Page\Service;

use App\Application\Page\Page\Provider\PageProvider;
use App\Application\Settings\Service\GetGeneralSettingsService;
use App\Entity\Page\Page;

final readonly class HomepageService
{
    public function __construct(
        private GetGeneralSettingsService $getGeneralSettingsService,
        private PageProvider $provider,
    ) {
    }

    public function get(): ?Page
    {
        $pageId = $this->getConfiguredPageId();

        return null === $pageId ? null : $this->provider->provideOne($pageId);
    }

    public function getConfiguredPageId(): ?int
    {
        return $this->getGeneralSettingsService->get()->homepagePageId;
    }

    public function hasPath(string $path): bool
    {
        return $path === $this->get()?->getPath();
    }
}
