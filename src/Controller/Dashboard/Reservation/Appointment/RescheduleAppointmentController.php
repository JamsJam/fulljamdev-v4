<?php

namespace App\Controller\Dashboard\Reservation\Appointment;

use App\Application\Reservation\Appointment\Service\ApplyAppointmentTransitionService;
use App\Application\Reservation\Appointment\Service\FindAppointmentService;
use App\Form\AppointmentRescheduleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RescheduleAppointmentController extends AbstractController
{
    #[Route('/dashboard/reservations/appointments/{id}/reschedule', name: 'app_dashboard_reservation_appointment_reschedule', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function __invoke(int $id, Request $request, FindAppointmentService $finder, ApplyAppointmentTransitionService $service): Response
    {
        $appointment = $finder->find($id);
        if (null === $appointment) {
            throw $this->createNotFoundException('Ce rendez-vous n’existe pas.');
        }

        $previousStart = $appointment->getStartAt();
        $previousEnd = $appointment->getEndAt();
        $form = $this->createForm(AppointmentRescheduleType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $appointment->getStartAt() < $appointment->getEndAt()) {
            try {
                $service->apply($appointment, 'reschedule');
                $this->addFlash('success', 'Le rendez-vous a été reprogrammé.');

                return $this->redirectToRoute('app_dashboard_reservation_processing', status: Response::HTTP_SEE_OTHER);
            } catch (\DomainException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        } else {
            $this->addFlash('error', 'Les nouvelles dates du rendez-vous sont invalides.');
        }

        $appointment->setStartAt($previousStart)->setEndAt($previousEnd);

        return $this->redirectToRoute('app_dashboard_reservation_processing', status: Response::HTTP_SEE_OTHER);
    }
}
