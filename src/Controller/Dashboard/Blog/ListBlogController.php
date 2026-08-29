<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Article\Service\GetArticlesService;
use App\Application\Blog\Category\Service\GetCategoriesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListBlogController extends AbstractController
{
    #[Route('/dashboard/blog', name: 'app_dashboard_blog', methods: ['GET'])]
    public function __invoke(GetArticlesService $articles, GetCategoriesService $categories): Response
    {
        return $this->render('dashboard/content/blog/index.html.twig', ['articles' => $articles->get(), 'categories' => $categories->get()]);
    }
}
