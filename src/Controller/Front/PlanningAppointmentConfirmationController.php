<?php

namespace App\Controller\Front;

use App\Application\Reservation\Planner\Service\FindPlanningService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PlanningAppointmentConfirmationController extends AbstractController
{
    #[Route('/book-meeting/{slug}/confirmation', name: 'app_front_planning_appointment_confirmation', requirements: ['slug' => '[a-z0-9-]+'], methods: ['GET'])]
    public function __invoke(string $slug, Request $request, FindPlanningService $findPlanningService): Response
    {
        $planning = $findPlanningService->findBySlug($slug);
        if (null === $planning || !$planning->isActive()) {
            throw $this->createNotFoundException('Ce planning n’est pas disponible.');
        }

        return $this->render('front/reservation/confirmation.html.twig', [
            'planning' => $planning,
            'booking_frame' => 1 === preg_match('/^[a-z0-9-]+$/', (string) $request->query->get('_frame'))
                ? (string) $request->query->get('_frame')
                : 'public-booking',
        ]);
    }
}
