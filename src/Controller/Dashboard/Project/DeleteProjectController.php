<?php

namespace App\Controller\Dashboard\Project;

use App\Application\Project\Service\DeleteProjectService;
use App\Application\Project\Service\FindProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DeleteProjectController extends AbstractController
{
    #[Route('/dashboard/projet/{id}/delete', name: 'app_dashboard_project_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function __invoke(int $id, Request $request, FindProjectService $finder, DeleteProjectService $delete): Response
    {
        $project = $finder->find($id) ?? throw $this->createNotFoundException('Ce projet n’existe pas.');
        if (!$this->isCsrfTokenValid('delete_project_'.$id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        } $delete->delete($project);
        $this->addFlash('success', 'Le projet a été supprimé.');

        return $this->redirectToRoute('app_dashboard_project', status: Response::HTTP_SEE_OTHER);
    }
}
