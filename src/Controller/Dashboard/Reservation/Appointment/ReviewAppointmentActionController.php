<?php

namespace App\Controller\Dashboard\Reservation\Appointment;

use App\Application\Reservation\Appointment\Service\FindAppointmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Workflow\Registry;

#[IsGranted('ROLE_ADMIN')]
final class ReviewAppointmentActionController extends AbstractController
{
    #[Route('/dashboard/reservations/appointments/{id}/{transition}/review', name: 'app_dashboard_reservation_appointment_review', requirements: ['id' => '\d+', 'transition' => 'confirm|reject'], methods: ['GET'])]
    public function __invoke(int $id, string $transition, FindAppointmentService $finder, Registry $workflows): Response
    {
        $appointment = $finder->find($id) ?? throw $this->createNotFoundException('Ce rendez-vous n’existe pas.');

        $response = $this->render('dashboard/reservation/appointment/review_action.html.twig', [
            'appointment' => $appointment,
            'transition' => $transition,
            'can_apply' => $workflows->get($appointment, 'appointment')->can($appointment, $transition),
        ]);
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }
}
