<?php

namespace App\Application\Blog\Workflow\Service;

use App\Application\Blog\Workflow\Rule\ArticleCanBeSubmittedRule;
use App\Repository\Blog\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Workflow\Registry;

final readonly class ProcessScheduledArticlesService
{
    public function __construct(
        private ArticleRepository $articles,
        private ArticleCanBeSubmittedRule $submissionRule,
        private Registry $workflows,
        private ClockInterface $clock,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function process(): void
    {
        foreach ($this->articles->findActiveScheduled() as $article) {
            $workflow = $this->workflows->get($article, 'article');

            if ([] !== $this->submissionRule->violations($article)) {
                $workflow->apply($article, 'unschedule');
                continue;
            }

            if (null !== $article->getPublishedAt() && $article->getPublishedAt() <= $this->clock->now()) {
                $workflow->apply($article, 'publish');
            }
        }

        $this->entityManager->flush();
    }
}
