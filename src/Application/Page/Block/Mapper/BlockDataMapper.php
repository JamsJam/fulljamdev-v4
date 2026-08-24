<?php

namespace App\Application\Page\Block\Mapper;

use App\Application\Page\Block\Interface\InitializableBlockDataInterface;
use App\Application\Page\Block\Registry\BlockRegistry;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class BlockDataMapper
{
    public function __construct(
        private BlockRegistry $registry,
        private SerializerInterface $serializer,
    ) {
    }

    /** @param array<string, mixed> $data */
    public function denormalize(string $type, array $data): object
    {
        $dto = $this->serializer->deserialize(
            json_encode($data, JSON_THROW_ON_ERROR),
            $this->registry->get($type)->dtoClass(),
            'json',
        );

        if ($dto instanceof InitializableBlockDataInterface) {
            $dto->initializeDefaults();
        }

        return $dto;
    }

    /** @return array<string, mixed> */
    public function normalize(object $data): array
    {
        $normalized = json_decode($this->serializer->serialize($data, 'json'), true, flags: JSON_THROW_ON_ERROR);
        if (!is_array($normalized)) {
            throw new \UnexpectedValueException('Le DTO de bloc n’a pas pu être normalisé.');
        }

        return $normalized;
    }
}
