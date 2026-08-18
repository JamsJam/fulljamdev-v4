<?php

namespace App\Controller\Dashboard\Reservation\Planning;

use App\Application\Reservation\Planner\Dto\PlanningDto;
use App\Application\Reservation\Planner\Service\CheckPlanningColorAvailabilityService;
use App\Application\Reservation\Planner\Service\CreatePlanningService;
use App\Form\Factory\PlanningAvailabilitiesFormFactory;
use App\Form\PlanningType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboStreamResponse;

final class CreatePlanningController extends AbstractController
{
    #[Route('/dashboard/reservations/plannings/new', name: 'app_dashboard_reservation_planning_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CreatePlanningService $createService, CheckPlanningColorAvailabilityService $colorService, PlanningAvailabilitiesFormFactory $formFactory): Response
    {
        $dto = new PlanningDto();
        $form = $this->createForm(PlanningType::class, $dto, ['action' => $this->generateUrl('app_dashboard_reservation_planning_new')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$colorService->isAvailable($dto->color)) {
                $form->get('color')->addError(new FormError('Cette couleur est déjà utilisée par un autre planning.'));
            } else {
                $planning = $createService->create($dto);

                return new TurboStreamResponse($this->renderView('dashboard/reservation/planning/turbo/stream/availability.stream.html.twig', [
                    'form' => $formFactory->create($planning),
                ]));
            }
        }

        return $this->render('dashboard/reservation/planning/turbo/frame/planning_dialog.html.twig', ['form' => $form], new Response(status: $form->isSubmitted() ? 422 : 200));
    }
}
