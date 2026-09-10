<?php

namespace App\Application\SEO\JsonLd\Contributor;

use App\Application\Page\Block\Library\Faq\Main\FaqDTO;
use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\SEO\JsonLd\Dto\JsonLdContribution;
use App\Application\SEO\JsonLd\Interface\BlockJsonLdContributorInterface;

final readonly class FaqJsonLdContributor implements BlockJsonLdContributorInterface
{
    public function supports(PageBlockDTO $block): bool
    {
        return 'faq.main' === $block->type && $block->data instanceof FaqDTO;
    }

    public function contribute(PageBlockDTO $block, string $blockId): JsonLdContribution
    {
        if (!$block->data instanceof FaqDTO) {
            return new JsonLdContribution();
        }
        $nodes = [];
        $pageUrl = explode('#', $blockId, 2)[0];
        foreach ($block->data->items as $item) {
            $question = trim($item->question);
            $answer = trim($item->answer);
            if ('' === $question || '' === $answer) {
                continue;
            }
            // Equal visible questions/answers share one node across FAQ blocks.
            $id = $pageUrl.'#question-'.hash('sha256', $question."\0".$answer);
            $nodes[$id] = [
                '@type' => 'Question', '@id' => $id, 'name' => $question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answer],
            ];
        }

        return new JsonLdContribution(nodes: array_values($nodes), questions: array_keys($nodes));
    }
}
