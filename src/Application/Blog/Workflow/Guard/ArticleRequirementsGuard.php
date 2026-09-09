<?php

namespace App\Application\Blog\Workflow\Guard;

use App\Application\Blog\Workflow\Rule\ArticleCanBePublishedRule;
use App\Application\Blog\Workflow\Rule\ArticleCanBeScheduledRule;
use App\Application\Blog\Workflow\Rule\ArticleCanBeSubmittedRule;
use App\Entity\Blog\Article;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

final readonly class ArticleRequirementsGuard implements EventSubscriberInterface
{
    public function __construct(
        private ArticleCanBeSubmittedRule $submissionRule,
        private ArticleCanBeScheduledRule $scheduleRule,
        private ArticleCanBePublishedRule $publicationRule,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.article.guard.submit' => 'guardContent',
            'workflow.article.guard.schedule' => 'guardSchedule',
            'workflow.article.guard.publish' => 'guardPublication',
        ];
    }

    public function guardContent(GuardEvent $event): void
    {
        $this->blockForViolations($event, $this->article($event), $this->submissionRule);
    }

    public function guardSchedule(GuardEvent $event): void
    {
        $article = $this->article($event);
        $this->block($event, $this->submissionRule->violations($article));
        $this->block($event, $this->scheduleRule->violations($article));
    }

    public function guardPublication(GuardEvent $event): void
    {
        $article = $this->article($event);
        $this->block($event, $this->submissionRule->violations($article));
        $this->block($event, $this->publicationRule->violations($article));
    }

    private function article(GuardEvent $event): Article
    {
        $article = $event->getSubject();
        if (!$article instanceof Article) {
            throw new \LogicException('Le workflow article ne prend en charge que les articles.');
        }

        return $article;
    }

    private function blockForViolations(GuardEvent $event, Article $article, ArticleCanBeSubmittedRule $rule): void
    {
        $this->block($event, $rule->violations($article));
    }

    /** @param list<string> $violations */
    private function block(GuardEvent $event, array $violations): void
    {
        foreach ($violations as $violation) {
            $event->setBlocked(true, $violation);
        }
    }
}
