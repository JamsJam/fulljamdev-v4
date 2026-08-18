<?php

namespace App\Controller\Dashboard\Reservation\Planning;

use App\Application\Reservation\Planner\Service\CheckPlanningColorAvailabilityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CheckPlanningColorController extends AbstractController
{
    #[Route('/dashboard/reservations/plannings/check-color', name: 'app_dashboard_reservation_planning_check_color', methods: ['GET'])]
    public function check(Request $request, CheckPlanningColorAvailabilityService $service): Response
    {
        $color = $request->query->getString('color');

        return $this->json([
            'available' => 1 === preg_match('/^#[0-9a-fA-F]{6}$/', $color) && $service->isAvailable($color),
        ]);
    }
}
