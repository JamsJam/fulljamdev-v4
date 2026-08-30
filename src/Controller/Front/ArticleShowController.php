<?php

namespace App\Controller\Front;

use App\Application\Blog\Article\Service\FindPublishedArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleShowController extends AbstractController
{
    #[Route('/blog/{slug}', name: 'app_front_article_show', requirements: ['slug' => '[a-z0-9]+(?:-[a-z0-9]+)*'], methods: ['GET'])]
    public function __invoke(string $slug, FindPublishedArticleService $finder): Response
    {
        $article = $finder->findBySlug($slug)
            ?? throw $this->createNotFoundException('Cet article n’est pas disponible.');

        return $this->render('front/blog/article_show.html.twig', ['article' => $article]);
    }
}
