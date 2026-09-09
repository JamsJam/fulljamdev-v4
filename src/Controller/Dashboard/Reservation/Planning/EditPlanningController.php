<?php

namespace App\Controller\Dashboard\Reservation\Planning;

use App\Application\Reservation\Planner\Dto\PlanningDto;
use App\Application\Reservation\Planner\Service\CheckPlanningColorAvailabilityService;
use App\Application\Reservation\Planner\Service\FindPlanningService;
use App\Application\Reservation\Planner\Service\UpdatePlanningService;
use App\Form\PlanningType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class EditPlanningController extends AbstractController
{
    #[Route('/dashboard/reservations/plannings/{id}/edit', name: 'app_dashboard_reservation_planning_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function __invoke(int $id, Request $request, FindPlanningService $finder, CheckPlanningColorAvailabilityService $colors, UpdatePlanningService $updater): Response
    {
        $planning = $finder->find($id) ?? throw $this->createNotFoundException('Ce planning n’existe pas.');
        $dto = new PlanningDto($planning->getTitle(), $planning->getDescription(), $planning->getDuration(), $planning->getGap(), $planning->getColor(), $planning->isActive(), $planning->isOnline());
        $form = $this->createForm(PlanningType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$colors->isAvailable($dto->color, $planning)) {
                $form->get('color')->addError(new FormError('Cette couleur est déjà utilisée par un autre planning.'));
            } else {
                $updater->update($planning, $dto);
                $this->addFlash('success', 'Le planning a été mis à jour.');

                return $this->redirectToRoute('app_dashboard_reservation_plannings', status: Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('dashboard/reservation/planning/edit.html.twig', ['planning' => $planning, 'form' => $form], new Response(status: $form->isSubmitted() ? 422 : 200));
    }
}
