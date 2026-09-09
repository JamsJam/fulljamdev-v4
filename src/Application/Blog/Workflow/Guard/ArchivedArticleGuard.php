<?php

namespace App\Application\Blog\Workflow\Guard;

use App\Entity\Blog\Article;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

final class ArchivedArticleGuard implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return ['workflow.article.guard' => 'guard'];
    }

    public function guard(GuardEvent $event): void
    {
        $article = $event->getSubject();
        if ($article instanceof Article && $article->isArchived()) {
            $event->setBlocked(true, 'Un article archivé doit être restauré avant de poursuivre son workflow.');
        }
    }
}
