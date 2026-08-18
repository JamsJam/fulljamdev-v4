<?php

namespace App\Controller\Dashboard\Reservation\Appointment;

use App\Application\Reservation\Appointment\Provider\AppointmentsToProcessProvider;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppointmentToProcessController extends AbstractController
{
    #[Route('/dashboard/reservations/to-process/', name: 'app_dashboard_reservation_processing', methods: ['GET'])]
    public function __invoke(
        Request $request,
        AppointmentsToProcessProvider $appointmentProvider,
        BreadcrumbService $breadcrumbService,
    ): Response {
        return $this->render('dashboard/reservation/appointment/to_process.html.twig', [
            'breadcrumb' => $breadcrumbService->getBreadcrumb($request->attributes->getString('_route')),
            'appointments' => $appointmentProvider->provide(date: new \DateTimeImmutable()),
        ]);
    }
}
