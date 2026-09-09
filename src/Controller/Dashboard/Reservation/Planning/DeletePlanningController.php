<?php

namespace App\Controller\Dashboard\Reservation\Planning;

use App\Application\Reservation\Planner\Service\FindPlanningService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class DeletePlanningController extends AbstractController
{
    #[Route('/dashboard/reservations/plannings/{id}/delete', name: 'app_dashboard_reservation_planning_delete', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function __invoke(int $id, Request $request, FindPlanningService $finder, EntityManagerInterface $entityManager): Response
    {
        $planning = $finder->find($id) ?? throw $this->createNotFoundException('Ce planning n’existe pas.');
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('delete_planning_'.$id, $request->request->getString('_token'))) {
                throw $this->createAccessDeniedException('Le jeton de sécurité est invalide.');
            }
            $planning->archive();
            $entityManager->flush();
            $this->addFlash('success', 'Le planning a été supprimé. Les rendez-vous existants sont conservés.');

            return $this->redirectToRoute('app_dashboard_reservation_plannings', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/reservation/planning/delete.html.twig', ['planning' => $planning]);
    }
}
