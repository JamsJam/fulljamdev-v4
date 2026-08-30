<?php

namespace App\Application\Blog\Workflow\Service;

use App\Application\Blog\Workflow\Event\ArticleRestoredEvent;
use App\Entity\Blog\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class RestoreArticleService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function restore(Article $article): void
    {
        if (!$article->isArchived()) {
            return;
        }

        $article->restore();
        $this->eventDispatcher->dispatch(new ArticleRestoredEvent($article));
        $this->entityManager->flush();
    }
}
