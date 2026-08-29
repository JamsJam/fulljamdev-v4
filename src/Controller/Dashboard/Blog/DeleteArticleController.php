<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Article\Service\DeleteArticleService;
use App\Application\Blog\Article\Service\FindArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DeleteArticleController extends AbstractController
{
    #[Route('/dashboard/blog/article/{id}/delete', name: 'app_dashboard_blog_article_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function __invoke(int $id, Request $request, FindArticleService $finder, DeleteArticleService $delete): Response
    {
        $article = $finder->find($id) ?? throw $this->createNotFoundException('Cet article n’existe pas.');
        if (!$this->isCsrfTokenValid('delete_article_'.$id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        } $delete->delete($article);
        $this->addFlash('success', 'L’article a été supprimé.');

        return $this->redirectToRoute('app_dashboard_blog', status: Response::HTTP_SEE_OTHER);
    }
}
