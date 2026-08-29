<?php

namespace App\Application\Settings\Service;

use App\Application\Page\Page\Provider\PageProvider;
use App\Application\Settings\General\Cache\GeneralSettingsCache;
use App\Application\Settings\General\Asset\SiteAssetUploader;
use App\Application\Settings\General\Dto\GeneralSettingsDto;
use App\Application\Settings\General\Writer\GeneralSettingsWriter;

final readonly class UpdateGeneralSettingsService
{
    public function __construct(
        private GeneralSettingsWriter $writer,
        private GeneralSettingsCache $cache,
        private PageProvider $pageProvider,
        private SiteAssetUploader $assetUploader,
    ) {
    }

    public function update(GeneralSettingsDto $dto): void
    {
        if (null === $dto->homepagePageId || null === $this->pageProvider->provideOne($dto->homepagePageId)) {
            throw new \DomainException('La page sélectionnée comme page d’accueil n’existe pas.');
        }

        if (null !== $dto->logoFile) {
            $dto->logoPath = $this->assetUploader->upload($dto->logoFile, 'logo');
        }
        if (null !== $dto->faviconSvgFile) {
            $dto->faviconSvgPath = $this->assetUploader->upload($dto->faviconSvgFile, 'favicon');
        }
        if (null !== $dto->faviconIcoFile) {
            $dto->faviconIcoPath = $this->assetUploader->upload($dto->faviconIcoFile, 'favicon');
        }
        if (null !== $dto->appleTouchIconFile) {
            $dto->appleTouchIconPath = $this->assetUploader->upload($dto->appleTouchIconFile, 'apple-touch-icon');
        }

        $this->writer->write($dto);
        $this->cache->invalidate();
    }
}
