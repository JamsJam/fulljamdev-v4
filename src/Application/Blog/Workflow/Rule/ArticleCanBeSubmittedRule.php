<?php

namespace App\Application\Blog\Workflow\Rule;

use App\Entity\Blog\Article;

final class ArticleCanBeSubmittedRule
{
    /** @return list<string> */
    public function violations(Article $article): array
    {
        $violations = [];

        if (null === $article->getTitle()) {
            $violations[] = 'Le titre est obligatoire.';
        } elseif (mb_strlen($article->getTitle()) > 50) {
            $violations[] = 'Le titre ne doit pas dépasser 50 caractères.';
        }

        if (null === $article->getSummary()) {
            $violations[] = 'Le résumé est obligatoire.';
        } elseif (mb_strlen($article->getSummary()) > 160) {
            $violations[] = 'Le résumé ne doit pas dépasser 160 caractères.';
        }

        $plainContent = trim(strip_tags($article->getContent() ?? ''));
        if ('' === html_entity_decode($plainContent, ENT_QUOTES | ENT_HTML5)) {
            $violations[] = 'Le contenu est obligatoire.';
        }

        if (null === $article->getCoverImage()) {
            $violations[] = 'L’image de couverture est obligatoire.';
        }

        if (null === $article->getCategory()) {
            $violations[] = 'La catégorie est obligatoire.';
        }

        return $violations;
    }
}
