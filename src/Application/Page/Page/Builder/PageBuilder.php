<?php

namespace App\Application\Page\Page\Builder;

use App\Application\Page\Block\Mapper\BlockDataMapper;
use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\Page\Page\Dto\PageDTO;
use App\Application\Page\SEO\Dto\SeoDTO;
use App\Entity\Page\Page;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class PageBuilder
{
    public function __construct(private BlockDataMapper $blockMapper, private SerializerInterface $serializer)
    {
    }

    public function build(Page $page): PageDTO
    {
        $dto = new PageDTO();
        $dto->id = $page->getId();
        $dto->title = $page->getTitle();
        $dto->path = $page->getPath();
        $dto->seo = $this->serializer->deserialize(json_encode($page->getSeo(), JSON_THROW_ON_ERROR), SeoDTO::class, 'json');

        foreach ($page->getBlocks() as $block) {
            $dto->blocks[] = new PageBlockDTO(
                $block->getId(),
                $block->getType(),
                $this->blockMapper->denormalize($block->getType(), $block->getData()),
                $block->getPosition(),
            );
        }

        return $dto;
    }
}
