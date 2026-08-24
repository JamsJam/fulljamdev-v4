<?php

namespace App\Application\Page\Block\Asset;

use App\Application\Page\Element\Image\ImageDTO;
use App\Application\Page\Element\Image\ImageSource;
use App\Service\FileUploaderService;

final readonly class BlockAssetProcessor
{
    public function __construct(private FileUploaderService $uploader)
    {
    }

    public function process(object $data): void
    {
        $processed = [];
        $this->processValue($data, $processed);
    }

    /** @param array<int, true> $processed */
    private function processValue(mixed $value, array &$processed): void
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                $this->processValue($item, $processed);
            }

            return;
        }
        if (!is_object($value) || isset($processed[spl_object_id($value)])) {
            return;
        }

        $processed[spl_object_id($value)] = true;
        if ($value instanceof ImageDTO) {
            if (ImageSource::MEDIA === $value->source && null !== $value->file) {
                $value->mediaId = $this->uploader->upload($value->file);
                $value->url = null;
                $value->file = null;
            } elseif (ImageSource::URL === $value->source) {
                $value->mediaId = null;
                $value->file = null;
            }

            return;
        }

        foreach ((array) $value as $propertyValue) {
            $this->processValue($propertyValue, $processed);
        }
    }
}
