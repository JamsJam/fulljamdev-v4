<?php

namespace App\Controller\Front;

use App\Application\Blog\Article\Service\BrowsePublishedArticlesService;
use App\Application\Blog\Category\Service\GetCategoriesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BlogCatalogController extends AbstractController
{
    #[Route('/blog', name: 'app_front_blog', methods: ['GET'], priority: 10)]
    public function __invoke(Request $request, BrowsePublishedArticlesService $articles, GetCategoriesService $categories): Response
    {
        $query = mb_substr(trim($request->query->getString('q')), 0, 100);
        $category = $request->query->getString('category');
        if (1 !== preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $category)) {
            $category = '';
        }

        return $this->render('front/blog/index.html.twig', [
            'articles' => $articles->browse($query, $category, max(1, $request->query->getInt('page', 1))),
            'categories' => $categories->get(),
            'query' => $query,
            'selected_category' => $category,
        ]);
    }
}
