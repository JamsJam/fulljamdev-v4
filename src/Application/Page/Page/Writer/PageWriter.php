<?php

namespace App\Application\Page\Page\Writer;

use App\Application\Page\Block\Asset\BlockAssetProcessor;
use App\Application\Page\Block\Mapper\BlockDataMapper;
use App\Application\Page\Block\Registry\BlockRegistry;
use App\Application\Page\Page\Dto\PageDTO;
use App\Entity\Page\Page;
use App\Entity\Page\PageBlock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class PageWriter
{
    public function __construct(
        private BlockRegistry $registry,
        private BlockDataMapper $blockMapper,
        private SerializerInterface $serializer,
        private EntityManagerInterface $entityManager,
        private BlockAssetProcessor $assetProcessor,
    ) {
    }

    public function save(PageDTO $dto, ?Page $page = null): Page
    {
        $page ??= new Page();
        $page->setTitle($dto->title)->setPath($dto->path);
        $seo = json_decode($this->serializer->serialize($dto->seo, 'json'), true, flags: JSON_THROW_ON_ERROR);
        $page->setSeo(is_array($seo) ? $seo : []);

        $existing = [];
        foreach ($page->getBlocks() as $block) {
            if (null !== $block->getId()) {
                $existing[$block->getId()] = $block;
            }
        }

        $blocks = array_values($dto->blocks);
        usort($blocks, static fn ($left, $right): int => $left->position <=> $right->position);

        $kept = [];
        foreach ($blocks as $position => $blockDto) {
            $definition = $this->registry->get($blockDto->type);
            $dtoClass = $definition->dtoClass();
            if (!$blockDto->data instanceof $dtoClass) {
                throw new \InvalidArgumentException(sprintf('Les données du bloc « %s » sont invalides.', $blockDto->type));
            }

            $this->assetProcessor->process($blockDto->data);

            $block = null !== $blockDto->id && isset($existing[$blockDto->id]) ? $existing[$blockDto->id] : new PageBlock();
            $block->setType($blockDto->type)->setPosition($position)->setData($this->blockMapper->normalize($blockDto->data));
            $page->addBlock($block);
            $kept[spl_object_id($block)] = true;
        }

        foreach ($page->getBlocks()->toArray() as $block) {
            if (!isset($kept[spl_object_id($block)])) {
                $page->removeBlock($block);
            }
        }

        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return $page;
    }
}
