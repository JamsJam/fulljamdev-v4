<?php

namespace App\Application\Blog\Workflow\Service;

use App\Application\Blog\Workflow\Event\ArticleArchivedEvent;
use App\Entity\Blog\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class ArchiveArticleService
{
    public function __construct(
        private ClockInterface $clock,
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function archive(Article $article): void
    {
        if ($article->isArchived()) {
            return;
        }

        $article->archive(\DateTimeImmutable::createFromInterface($this->clock->now()));
        $this->eventDispatcher->dispatch(new ArticleArchivedEvent($article));
        $this->entityManager->flush();
    }
}
