<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Article\Service\FindArticleService;
use App\Application\Blog\Workflow\Service\ArchiveArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArchiveArticleController extends AbstractController
{
    #[Route('/dashboard/blog/article/{id}/archive', name: 'app_dashboard_blog_article_archive', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function __invoke(int $id, Request $request, FindArticleService $finder, ArchiveArticleService $archive): Response
    {
        $article = $finder->find($id) ?? throw $this->createNotFoundException('Cet article n’existe pas.');
        if (!$this->isCsrfTokenValid('archive_article_'.$id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $archive->archive($article);
        $this->addFlash('success', 'L’article a été archivé.');

        return $this->redirectToRoute('app_dashboard_blog', status: Response::HTTP_SEE_OTHER);
    }
}
