<?php

namespace App\Application\Settings\Service;

use App\Application\Page\Page\Provider\PageProvider;
use App\Application\Settings\General\Cache\GeneralSettingsCache;
use App\Application\Settings\General\Dto\GeneralSettingsDto;
use App\Application\Settings\General\Writer\GeneralSettingsWriter;

final readonly class UpdateGeneralSettingsService
{
    public function __construct(
        private GeneralSettingsWriter $writer,
        private GeneralSettingsCache $cache,
        private PageProvider $pageProvider,
    ) {
    }

    public function update(GeneralSettingsDto $dto): void
    {
        if (null === $dto->homepagePageId || null === $this->pageProvider->provideOne($dto->homepagePageId)) {
            throw new \DomainException('La page sélectionnée comme page d’accueil n’existe pas.');
        }

        $this->writer->write($dto);
        $this->cache->invalidate();
    }
}
