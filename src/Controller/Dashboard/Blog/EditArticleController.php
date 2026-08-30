<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Article\Factory\ArticleFactory;
use App\Application\Blog\Article\Form\ArticleType;
use App\Application\Blog\Article\Service\FindArticleService;
use App\Application\Blog\Article\Service\SaveArticleService;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EditArticleController extends AbstractController
{
    #[Route('/dashboard/blog/article/{id}/edit', name: 'app_dashboard_blog_article_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function __invoke(int $id, Request $request, FindArticleService $finder, ArticleFactory $factory, SaveArticleService $save, BreadcrumbService $breadcrumbs): Response
    {
        $article = $finder->find($id) ?? throw $this->createNotFoundException('Cet article n’existe pas.');
        $dto = $factory->fromEntity($article);
        $form = $this->createForm(ArticleType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $save->save($dto, $article);
            $this->addFlash('success', 'L’article a été modifié.');

            return $this->redirectToRoute('app_dashboard_blog', status: 303);
        }

        $breadcrumb = $breadcrumbs->getBreadcrumb($request->attributes->getString('_route'));
        $breadcrumb[array_key_last($breadcrumb)]['label'] = 'Modifier l’article';

        return $this->render('dashboard/blog/article_form.html.twig', ['form' => $form, 'creation' => false, 'article' => $article, 'breadcrumb' => $breadcrumb], new Response(status: $form->isSubmitted() ? 422 : 200));
    }
}
