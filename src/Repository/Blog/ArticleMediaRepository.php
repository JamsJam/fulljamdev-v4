<?php

namespace App\Repository\Blog;

use App\Entity\Blog\Article;
use App\Entity\Blog\ArticleMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<ArticleMedia> */
final class ArticleMediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleMedia::class);
    }

    public function attachReferencedMedia(Article $article): void
    {
        $content = $article->getContent() ?? '';
        if ('' === $content) {
            return;
        }

        $changed = false;
        foreach ($this->findBy(['article' => null]) as $media) {
            if (str_contains($content, $media->getPath())) {
                $media->setArticle($article);
                $changed = true;
            }
        }
        if ($changed) {
            $this->getEntityManager()->flush();
        }
    }
}
