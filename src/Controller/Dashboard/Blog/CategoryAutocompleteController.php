<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Category\Service\GetCategoriesService;
use App\Entity\Blog\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryAutocompleteController extends AbstractController
{
    #[Route('/dashboard/blog/category/autocomplete', name: 'app_dashboard_blog_category_autocomplete', methods: ['GET'])]
    public function __invoke(GetCategoriesService $categories): JsonResponse
    {
        return $this->json(array_map(
            static fn (Category $category): string => $category->getName(),
            $categories->get(),
        ));
    }
}
