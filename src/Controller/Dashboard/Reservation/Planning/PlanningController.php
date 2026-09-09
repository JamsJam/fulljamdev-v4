<?php

namespace App\Controller\Dashboard\Reservation\Planning;

use App\Application\Reservation\Planner\Service\GetPlanningsService;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class PlanningController extends AbstractController
{
    #[Route('/dashboard/reservations/plannings/', name: 'app_dashboard_reservation_plannings', methods: ['GET'])]
    public function index(Request $request, BreadcrumbService $breadcrumbService, GetPlanningsService $service): Response
    {
        return $this->render('dashboard/reservation/planning/index.html.twig', [
            'breadcrumb' => $breadcrumbService->getBreadcrumb($request->attributes->getString('_route')),
            'plannings' => $service->get(),
        ]);
    }
}
