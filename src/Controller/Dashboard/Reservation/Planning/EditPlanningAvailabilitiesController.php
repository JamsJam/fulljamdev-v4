<?php

namespace App\Controller\Dashboard\Reservation\Planning;

use App\Application\Reservation\Planner\Service\FindPlanningService;
use App\Form\Factory\PlanningAvailabilitiesFormFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EditPlanningAvailabilitiesController extends AbstractController
{
    #[Route('/dashboard/reservations/plannings/{id}/availabilities/edit', name: 'app_dashboard_reservation_planning_availabilities_edit', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function edit(int $id, FindPlanningService $service, PlanningAvailabilitiesFormFactory $formFactory): Response
    {
        $planning = $service->find($id);
        if (null === $planning) {
            throw $this->createNotFoundException('Ce planning n’existe pas.');
        }

        return $this->render('dashboard/reservation/planning/turbo/frame/availability_edit_dialog.html.twig', [
            'planning' => $planning,
            'form' => $formFactory->create($planning),
        ]);
    }
}
