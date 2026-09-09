<?php

namespace App\Application\Blog\Workflow\Transition;

use App\Application\Blog\Article\Service\ArticleSlugGenerator;
use App\Entity\Blog\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;

final readonly class ApplyArticleTransitionService
{
    public function __construct(
        private Registry $workflows,
        private EntityManagerInterface $entityManager,
        private ArticleSlugGenerator $slugGenerator,
    ) {
    }

    public function apply(Article $article, ArticleTransition $transition): void
    {
        if (ArticleTransition::SUBMIT === $transition) {
            $this->slugGenerator->generate($article);
        }

        $workflow = $this->workflows->get($article, 'article');
        if (!$workflow->can($article, $transition->value)) {
            throw new \DomainException(sprintf('La transition « %s » n’est pas autorisée pour cet article.', $transition->value));
        }

        $workflow->apply($article, $transition->value);
        $this->entityManager->flush();
    }
}
