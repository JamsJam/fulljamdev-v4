<?php

namespace App\Application\Blog\Workflow\Event;

use App\Entity\Blog\Article;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;

final readonly class ArticleWorkflowSubscriber implements EventSubscriberInterface
{
    public function __construct(private ClockInterface $clock)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.article.completed.publish' => 'onPublished',
            'workflow.article.completed.reject' => 'clearPlannedPublication',
            'workflow.article.completed.unschedule' => 'clearPlannedPublication',
        ];
    }

    public function onPublished(CompletedEvent $event): void
    {
        $article = $event->getSubject();
        if (!$article instanceof Article) {
            return;
        }

        $publishedAt = $article->getPublishedAt();
        if (null === $publishedAt || $publishedAt > $this->clock->now()) {
            $article->setPublishedAt(\DateTimeImmutable::createFromInterface($this->clock->now()));
        }
    }

    public function clearPlannedPublication(CompletedEvent $event): void
    {
        $article = $event->getSubject();
        if ($article instanceof Article) {
            $article->setPublishedAt(null);
        }
    }
}
