<?php

namespace App\Controller\Dashboard\Reservation\Appointment;

use App\Application\Reservation\Appointment\Service\FindAppointmentService;
use App\Entity\Reservation\Summary;
use App\Form\AppointmentRescheduleType;
use App\Form\AppointmentSummaryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;
use Symfony\UX\Turbo\TurboStreamResponse;

final class ShowAppointmentController extends AbstractController
{
    #[Route('/dashboard/reservations/appointments/{id}', name: 'app_dashboard_reservation_appointment_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, Request $request, FindAppointmentService $service): Response
    {
        $appointment = $service->find($id);

        if (null === $appointment) {
            throw $this->createNotFoundException('Ce rendez-vous n’existe pas.');
        }

        $context = [
            'appointment' => $appointment,
            'reschedule_form' => $this->createForm(AppointmentRescheduleType::class, $appointment, [
                'action' => $this->generateUrl('app_dashboard_reservation_appointment_reschedule', ['id' => $appointment->getId()]),
            ]),
            'summary_form' => $this->createForm(AppointmentSummaryType::class, $appointment->getSummary() ?? new Summary(), [
                'action' => $this->generateUrl('app_dashboard_reservation_appointment_summary', ['id' => $appointment->getId()]),
            ]),
        ];

        if (str_contains((string) $request->headers->get('Accept'), TurboBundle::STREAM_MEDIA_TYPE)) {
            return new TurboStreamResponse($this->renderView(
                'dashboard/reservation/appointment/turbo/stream/details_dialog.stream.html.twig',
                $context,
            ));
        }

        return $this->render('dashboard/reservation/appointment/turbo/frame/details_dialog.html.twig', $context);
    }
}
