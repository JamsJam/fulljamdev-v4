<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Article\Service\FindArticleService;
use App\Application\Blog\Workflow\Service\RestoreArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RestoreArticleController extends AbstractController
{
    #[Route('/dashboard/blog/article/{id}/restore', name: 'app_dashboard_blog_article_restore', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function __invoke(int $id, Request $request, FindArticleService $finder, RestoreArticleService $restore): Response
    {
        $article = $finder->find($id) ?? throw $this->createNotFoundException('Cet article n’existe pas.');
        if (!$this->isCsrfTokenValid('restore_article_'.$id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $restore->restore($article);
        $this->addFlash('success', 'L’article a été restauré.');

        return $this->redirectToRoute('app_dashboard_blog', status: Response::HTTP_SEE_OTHER);
    }
}
