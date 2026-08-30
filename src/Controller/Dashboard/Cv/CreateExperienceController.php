<?php

namespace App\Controller\Dashboard\Cv;

use App\Application\Experience\Dto\ExperienceDto;
use App\Application\Experience\Form\ExperienceType;
use App\Application\Experience\Service\SaveExperienceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CreateExperienceController extends AbstractController
{
    #[Route('/dashboard/cv/new', name: 'app_dashboard_cv_new', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, SaveExperienceService $service): Response
    {
        $dto = new ExperienceDto();
        $form = $this->createForm(ExperienceType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->save($dto);
            $this->addFlash('success', 'L’expérience a été ajoutée.');

            return $this->redirectToRoute('app_dashboard_cv', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/cv/form.html.twig', ['form' => $form, 'creation' => true], new Response(status: $form->isSubmitted() ? 422 : 200));
    }
}
