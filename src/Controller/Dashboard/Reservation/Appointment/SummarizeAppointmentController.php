<?php

namespace App\Controller\Dashboard\Reservation\Appointment;

use App\Application\Reservation\Appointment\Service\ApplyAppointmentTransitionService;
use App\Application\Reservation\Appointment\Service\FindAppointmentService;
use App\Entity\Reservation\Summary;
use App\Form\AppointmentSummaryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SummarizeAppointmentController extends AbstractController
{
    #[Route('/dashboard/reservations/appointments/{id}/summary', name: 'app_dashboard_reservation_appointment_summary', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function __invoke(int $id, Request $request, FindAppointmentService $finder, ApplyAppointmentTransitionService $transitionService, EntityManagerInterface $entityManager): Response
    {
        $appointment = $finder->find($id);
        if (null === $appointment) {
            throw $this->createNotFoundException('Ce rendez-vous n’existe pas.');
        }

        $summary = $appointment->getSummary() ?? (new Summary())->setAppointment($appointment);
        $form = $this->createForm(AppointmentSummaryType::class, $summary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($summary);
            try {
                $transitionService->apply($appointment, 'complete');
                $this->addFlash('success', 'Le compte rendu a été enregistré.');

                return $this->redirectToRoute('app_dashboard_reservation_processing', status: Response::HTTP_SEE_OTHER);
            } catch (\DomainException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        } else {
            $this->addFlash('error', 'Le compte rendu est invalide.');
        }

        return $this->redirectToRoute('app_dashboard_reservation_processing', status: Response::HTTP_SEE_OTHER);
    }
}
