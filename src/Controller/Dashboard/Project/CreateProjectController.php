<?php

namespace App\Controller\Dashboard\Project;

use App\Application\Project\Dto\ProjectDto;
use App\Application\Project\Form\ProjectType;
use App\Application\Project\Service\SaveProjectService;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class CreateProjectController extends AbstractController
{
    #[Route('/dashboard/projet/new', name: 'app_dashboard_project_new', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, SaveProjectService $save, BreadcrumbService $breadcrumbs): Response
    {
        $dto = new ProjectDto();
        $form = $this->createForm(ProjectType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $save->save($dto);
            $this->addFlash('success', 'Le projet a été ajouté.');

            return $this->redirectToRoute('app_dashboard_project', status: 303);
        }

        return $this->render('dashboard/project/form.html.twig', [
            'form' => $form,
            'creation' => true,
            'project' => null,
            'breadcrumb' => $breadcrumbs->getBreadcrumb($request->attributes->getString('_route')),
        ], new Response(status: $form->isSubmitted() ? 422 : 200));
    }
}
