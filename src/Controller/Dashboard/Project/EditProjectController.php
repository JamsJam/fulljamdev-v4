<?php

namespace App\Controller\Dashboard\Project;

use App\Application\Project\Factory\ProjectFactory;
use App\Application\Project\Form\ProjectType;
use App\Application\Project\Service\FindProjectService;
use App\Application\Project\Service\SaveProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EditProjectController extends AbstractController
{
    #[Route('/dashboard/projet/{id}/edit', name: 'app_dashboard_project_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function __invoke(int $id, Request $request, FindProjectService $finder, ProjectFactory $factory, SaveProjectService $save): Response
    {
        $project = $finder->find($id) ?? throw $this->createNotFoundException('Ce projet n’existe pas.');
        $dto = $factory->fromEntity($project);
        $form = $this->createForm(ProjectType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $save->save($dto, $project);
            $this->addFlash('success', 'Le projet a été modifié.');

            return $this->redirectToRoute('app_dashboard_project', status: 303);
        }

        return $this->render('dashboard/project/form.html.twig', ['form' => $form, 'creation' => false, 'project' => $project], new Response(status: $form->isSubmitted() ? 422 : 200));
    }
}
