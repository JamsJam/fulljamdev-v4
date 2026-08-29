<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Category\Service\DeleteCategoryService;
use App\Application\Blog\Category\Service\FindCategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DeleteCategoryController extends AbstractController
{
    #[Route('/dashboard/blog/category/{id}/delete', name: 'app_dashboard_blog_category_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function __invoke(int $id, Request $request, FindCategoryService $finder, DeleteCategoryService $delete): Response
    {
        $category = $finder->find($id) ?? throw $this->createNotFoundException('Cette catégorie n’existe pas.');
        if (!$this->isCsrfTokenValid('delete_category_'.$id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        } $delete->delete($category);
        $this->addFlash('success', 'La catégorie a été supprimée.');

        return $this->redirectToRoute('app_dashboard_blog', status: Response::HTTP_SEE_OTHER);
    }
}
