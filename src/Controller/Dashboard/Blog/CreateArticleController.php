<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Article\Dto\ArticleDto;
use App\Application\Blog\Article\Form\ArticleType;
use App\Application\Blog\Article\Service\SaveArticleService;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CreateArticleController extends AbstractController
{
    #[Route('/dashboard/blog/article/new', name: 'app_dashboard_blog_article_new', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, SaveArticleService $save, BreadcrumbService $breadcrumbs): Response
    {
        $dto = new ArticleDto();
        $form = $this->createForm(ArticleType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $save->save($dto);
            $this->addFlash('success', 'L’article a été ajouté.');

            return $this->redirectToRoute('app_dashboard_blog', status: 303);
        }

        $breadcrumb = $breadcrumbs->getBreadcrumb($request->attributes->getString('_route'));
        $breadcrumb[array_key_last($breadcrumb)]['label'] = 'Nouvel article';

        return $this->render('dashboard/blog/article_form.html.twig', ['form' => $form, 'creation' => true, 'article' => null, 'breadcrumb' => $breadcrumb], new Response(status: $form->isSubmitted() ? 422 : 200));
    }
}
