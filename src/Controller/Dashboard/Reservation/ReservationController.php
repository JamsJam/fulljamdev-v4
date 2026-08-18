<?php

namespace App\Controller\Dashboard\Reservation;

use App\Application\Reservation\Service\GetReservationDashboardService;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationController extends AbstractController
{
    #[Route('/dashboard/reservations/', name: 'app_dashboard_reservation', methods: ['GET'])]
    public function index(Request $request, BreadcrumbService $breadcrumbService, GetReservationDashboardService $service): Response
    {
        return $this->render('dashboard/reservation/index.html.twig', [
            'breadcrumb' => $breadcrumbService->getBreadcrumb($request->attributes->getString('_route')),
            ...$service->getDashboard(),
        ]);
    }

    #[Route('/dashboard/reservations/calendar/', name: 'app_dashboard_reservation_calendar', methods: ['GET'])]
    public function calendar(Request $request, BreadcrumbService $breadcrumbService, GetReservationDashboardService $service): Response
    {
        return $this->render('dashboard/reservation/calendar.html.twig', [
            'breadcrumb' => $breadcrumbService->getBreadcrumb($request->attributes->getString('_route')),
            ...$service->getCalendar($request->query->getString('month')),
        ]);
    }
}
