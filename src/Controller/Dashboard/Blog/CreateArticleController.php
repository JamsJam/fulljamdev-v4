<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Article\Dto\ArticleDto;
use App\Application\Blog\Article\Form\ArticleType;
use App\Application\Blog\Article\Service\CheckArticleSlugAvailabilityService;
use App\Application\Blog\Article\Service\SaveArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CreateArticleController extends AbstractController
{
    #[Route('/dashboard/blog/article/new', name: 'app_dashboard_blog_article_new', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, SaveArticleService $save, CheckArticleSlugAvailabilityService $slugs): Response
    {
        $dto = new ArticleDto();
        $form = $this->createForm(ArticleType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($slugs->isUsed($dto->slug)) {
                $form->get('slug')->addError(new FormError('Ce slug est déjà utilisé.'));
            } else {
                $save->save($dto);
                $this->addFlash('success', 'L’article a été ajouté.');

                return $this->redirectToRoute('app_dashboard_blog', status: 303);
            }
        }

        return $this->render('dashboard/content/blog/article_form.html.twig', ['form' => $form, 'creation' => true], new Response(status: $form->isSubmitted() ? 422 : 200));
    }
}
