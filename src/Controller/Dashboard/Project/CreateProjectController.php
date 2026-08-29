<?php

namespace App\Controller\Dashboard\Project;

use App\Application\Project\Dto\ProjectDto;
use App\Application\Project\Form\ProjectType;
use App\Application\Project\Service\CheckProjectSlugAvailabilityService;
use App\Application\Project\Service\SaveProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CreateProjectController extends AbstractController
{
    #[Route('/dashboard/projet/new', name: 'app_dashboard_project_new', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, SaveProjectService $save, CheckProjectSlugAvailabilityService $slugs): Response
    {
        $dto = new ProjectDto();
        $form = $this->createForm(ProjectType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($slugs->isUsed($dto->slug)) {
                $form->get('slug')->addError(new FormError('Ce slug est déjà utilisé.'));
            } else {
                $save->save($dto);
                $this->addFlash('success', 'Le projet a été ajouté.');

                return $this->redirectToRoute('app_dashboard_project', status: 303);
            }
        }

        return $this->render('dashboard/content/project/form.html.twig', ['form' => $form, 'creation' => true], new Response(status: $form->isSubmitted() ? 422 : 200));
    }
}
