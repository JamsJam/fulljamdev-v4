<?php

namespace App\Controller\Dashboard\Cv;

use App\Application\Experience\Factory\ExperienceFactory;
use App\Application\Experience\Form\ExperienceType;
use App\Application\Experience\Service\FindExperienceService;
use App\Application\Experience\Service\SaveExperienceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EditExperienceController extends AbstractController
{
    #[Route('/dashboard/cv/{id}/edit', name: 'app_dashboard_cv_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function __invoke(int $id, Request $request, FindExperienceService $finder, ExperienceFactory $factory, SaveExperienceService $service): Response
    {
        $experience = $finder->find($id) ?? throw $this->createNotFoundException('Cette expérience n’existe pas.');
        $dto = $factory->fromEntity($experience);
        $form = $this->createForm(ExperienceType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->save($dto, $experience);
            $this->addFlash('success', 'L’expérience a été modifiée.');

            return $this->redirectToRoute('app_dashboard_cv', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/cv/form.html.twig', ['form' => $form, 'creation' => false], new Response(status: $form->isSubmitted() ? 422 : 200));
    }
}
