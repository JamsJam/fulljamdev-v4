<?php

namespace App\Controller\Dashboard\Reservation\Planning;

use App\Application\Reservation\Planner\Service\FindPlanningService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShowPlanningAvailabilitiesController extends AbstractController
{
    #[Route('/dashboard/reservations/plannings/{id}/availabilities', name: 'app_dashboard_reservation_planning_availabilities_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, FindPlanningService $service): Response
    {
        $planning = $service->find($id);
        if (null === $planning) {
            throw $this->createNotFoundException('Ce planning n’existe pas.');
        }

        $byDay = [];
        foreach ($planning->getAvailabilities() as $availability) {
            $byDay[$availability->getDow()] = $availability;
        }

        return $this->render('dashboard/reservation/planning/turbo/frame/availability_dialog.html.twig', [
            'planning' => $planning,
            'availabilitiesByDay' => $byDay,
        ]);
    }
}
