<?php

namespace App\Controller\Dashboard;

use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationController extends AbstractController
{
    #[Route('/dashboard/reservations/', name: 'app_dashboard_reservation', methods: ['GET'])]
    public function index(Request $request, BreadcrumbService $breadcrumbService): Response
    {
        return $this->render('reservation/index.html.twig', [
            'breadcrumb' => $breadcrumbService->getBreadcrumb($request->attributes->getString('_route')),
        ]);
    }
}
