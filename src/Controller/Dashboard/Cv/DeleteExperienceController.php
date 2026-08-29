<?php

namespace App\Controller\Dashboard\Cv;

use App\Application\Experience\Service\DeleteExperienceService;
use App\Application\Experience\Service\FindExperienceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DeleteExperienceController extends AbstractController
{
    #[Route('/dashboard/cv/{id}/delete', name: 'app_dashboard_cv_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function __invoke(int $id, Request $request, FindExperienceService $finder, DeleteExperienceService $service): Response
    {
        $experience = $finder->find($id) ?? throw $this->createNotFoundException('Cette expérience n’existe pas.');
        if (!$this->isCsrfTokenValid('delete_experience_'.$id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        $service->delete($experience);
        $this->addFlash('success', 'L’expérience a été supprimée.');

        return $this->redirectToRoute('app_dashboard_cv', status: Response::HTTP_SEE_OTHER);
    }
}
