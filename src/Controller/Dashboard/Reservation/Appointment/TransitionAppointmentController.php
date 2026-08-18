<?php

namespace App\Controller\Dashboard\Reservation\Appointment;

use App\Application\Reservation\Appointment\Service\ApplyAppointmentTransitionService;
use App\Application\Reservation\Appointment\Service\FindAppointmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TransitionAppointmentController extends AbstractController
{
    private const TRANSITIONS = [
        'confirm' => 'Le rendez-vous a été confirmé.',
        'reject' => 'La demande a été refusée.',
        'cancel' => 'Le rendez-vous a été annulé.',
        'no_show' => 'Le contact a été marqué absent.',
        'mark_held' => 'Le rendez-vous a été marqué comme réalisé.',
    ];

    #[Route('/dashboard/reservations/appointments/{id}/{transition}', name: 'app_dashboard_reservation_appointment_transition', requirements: ['id' => '\d+', 'transition' => 'confirm|reject|cancel|no_show|mark_held'], methods: ['POST'])]
    public function __invoke(int $id, string $transition, Request $request, FindAppointmentService $finder, ApplyAppointmentTransitionService $service): Response
    {
        $appointment = $finder->find($id);
        if (null === $appointment) {
            throw $this->createNotFoundException('Ce rendez-vous n’existe pas.');
        }

        if (!$this->isCsrfTokenValid('appointment_transition_'.$appointment->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Le jeton de sécurité est invalide.');
        }

        try {
            $service->apply($appointment, $transition);
            $this->addFlash('success', self::TRANSITIONS[$transition]);
        } catch (\DomainException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_dashboard_reservation_processing', status: Response::HTTP_SEE_OTHER);
    }
}
