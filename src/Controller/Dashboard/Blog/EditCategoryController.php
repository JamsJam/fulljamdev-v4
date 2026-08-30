<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Category\Factory\CategoryFactory;
use App\Application\Blog\Category\Form\CategoryType;
use App\Application\Blog\Category\Service\CheckCategorySlugAvailabilityService;
use App\Application\Blog\Category\Service\FindCategoryService;
use App\Application\Blog\Category\Service\SaveCategoryService;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EditCategoryController extends AbstractController
{
    #[Route('/dashboard/blog/category/{id}/edit', name: 'app_dashboard_blog_category_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function __invoke(int $id, Request $request, FindCategoryService $finder, CategoryFactory $factory, SaveCategoryService $save, CheckCategorySlugAvailabilityService $slugs, BreadcrumbService $breadcrumbs): Response
    {
        $category = $finder->find($id) ?? throw $this->createNotFoundException('Cette catégorie n’existe pas.');
        $dto = $factory->fromEntity($category);
        $form = $this->createForm(CategoryType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($slugs->isUsed($dto->slug, $category)) {
                $form->get('slug')->addError(new FormError('Ce slug est déjà utilisé.'));
            } else {
                $save->save($dto, $category);
                $this->addFlash('success', 'La catégorie a été modifiée.');

                return $this->redirectToRoute('app_dashboard_blog', status: 303);
            }
        }

        $breadcrumb = $breadcrumbs->getBreadcrumb($request->attributes->getString('_route'));
        $breadcrumb[array_key_last($breadcrumb)]['label'] = 'Modifier la catégorie';

        return $this->render('dashboard/blog/category_form.html.twig', ['form' => $form, 'creation' => false, 'breadcrumb' => $breadcrumb], new Response(status: $form->isSubmitted() ? 422 : 200));
    }
}
