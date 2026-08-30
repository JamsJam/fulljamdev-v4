<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Category\Dto\CategoryDto;
use App\Application\Blog\Category\Form\CategoryType;
use App\Application\Blog\Category\Service\CheckCategorySlugAvailabilityService;
use App\Application\Blog\Category\Service\SaveCategoryService;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CreateCategoryController extends AbstractController
{
    #[Route('/dashboard/blog/category/new', name: 'app_dashboard_blog_category_new', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, SaveCategoryService $save, CheckCategorySlugAvailabilityService $slugs, BreadcrumbService $breadcrumbs): Response
    {
        $dto = new CategoryDto();
        $form = $this->createForm(CategoryType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($slugs->isUsed($dto->slug)) {
                $form->get('slug')->addError(new FormError('Ce slug est déjà utilisé.'));
            } else {
                $save->save($dto);
                $this->addFlash('success', 'La catégorie a été ajoutée.');

                return $this->redirectToRoute('app_dashboard_blog', status: 303);
            }
        }

        $breadcrumb = $breadcrumbs->getBreadcrumb($request->attributes->getString('_route'));
        $breadcrumb[array_key_last($breadcrumb)]['label'] = 'Nouvelle catégorie';

        return $this->render('dashboard/blog/category_form.html.twig', ['form' => $form, 'creation' => true, 'breadcrumb' => $breadcrumb], new Response(status: $form->isSubmitted() ? 422 : 200));
    }
}
