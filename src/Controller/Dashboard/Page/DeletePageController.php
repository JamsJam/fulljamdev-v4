<?php

namespace App\Controller\Dashboard\Page;

use App\Application\Page\Page\Service\DeletePageService;
use App\Application\Page\Page\Service\FindPageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class DeletePageController extends AbstractController
{
    #[Route('/dashboard/settings/pages/{id}/delete', name: 'app_dashboard_page_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function __invoke(
        int $id,
        Request $request,
        FindPageService $finder,
        DeletePageService $delete,
    ): Response {
        $page = $finder->find($id) ?? throw $this->createNotFoundException('Cette page n’existe pas.');

        if (!$this->isCsrfTokenValid('delete_page_'.$id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $delete->delete($page);
        $this->addFlash('success', 'La page et ses médias non utilisés ont été supprimés.');

        return $this->redirectToRoute('app_dashboard_settings', ['section' => 'pages'], Response::HTTP_SEE_OTHER);
    }
}
