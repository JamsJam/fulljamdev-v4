<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Article\Service\FindArticleService;
use App\Application\Blog\Workflow\Transition\ApplyArticleTransitionService;
use App\Application\Blog\Workflow\Transition\ArticleTransition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TransitionArticleController extends AbstractController
{
    private const MESSAGES = [
        'submit' => 'L’article a été envoyé en relecture.',
        'reject' => 'L’article est repassé en brouillon.',
        'schedule' => 'L’article a été planifié.',
        'publish' => 'L’article a été publié.',
        'unschedule' => 'La planification de l’article a été annulée.',
        'take_offline' => 'L’article a été mis hors ligne.',
    ];

    #[Route(
        '/dashboard/blog/article/{id}/{transition}',
        name: 'app_dashboard_blog_article_transition',
        requirements: ['id' => '\d+', 'transition' => 'submit|reject|schedule|publish|unschedule|take_offline'],
        methods: ['POST'],
    )]
    public function __invoke(
        int $id,
        string $transition,
        Request $request,
        FindArticleService $finder,
        ApplyArticleTransitionService $workflow,
    ): Response {
        $article = $finder->find($id) ?? throw $this->createNotFoundException('Cet article n’existe pas.');
        if (!$this->isCsrfTokenValid('article_transition_'.$id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        try {
            $workflow->apply($article, ArticleTransition::from($transition));
            $this->addFlash('success', self::MESSAGES[$transition]);
        } catch (\DomainException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_dashboard_blog', status: Response::HTTP_SEE_OTHER);
    }
}
