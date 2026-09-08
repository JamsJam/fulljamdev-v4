<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Article\Service\GetArticlesService;
use App\Application\Blog\Category\Service\GetCategoriesService;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class ListBlogController extends AbstractController
{
    #[Route('/dashboard/blog', name: 'app_dashboard_blog', methods: ['GET'])]
    public function __invoke(Request $request, GetArticlesService $articles, GetCategoriesService $categories, BreadcrumbService $breadcrumbs): Response
    {
        return $this->render('dashboard/blog/index.html.twig', [
            'articles' => $articles->get(),
            'categories' => $categories->get(),
            'breadcrumb' => $breadcrumbs->getBreadcrumb($request->attributes->getString('_route')),
        ]);
    }
}
