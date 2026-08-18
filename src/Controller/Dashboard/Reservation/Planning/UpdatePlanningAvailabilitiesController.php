<?php

namespace App\Controller\Dashboard\Reservation\Planning;

use App\Application\Reservation\Availability\Dto\PlanningAvailabilitiesDto;
use App\Application\Reservation\Availability\Service\UpdatePlanningAvailabilitiesService;
use App\Application\Reservation\Planner\Service\FindPlanningService;
use App\Form\Factory\PlanningAvailabilitiesFormFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboStreamResponse;

final class UpdatePlanningAvailabilitiesController extends AbstractController
{
    #[Route('/dashboard/reservations/plannings/{id}/availabilities', name: 'app_dashboard_reservation_planning_availabilities', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function update(int $id, Request $request, FindPlanningService $findService, UpdatePlanningAvailabilitiesService $updateService, PlanningAvailabilitiesFormFactory $formFactory): Response
    {
        $planning = $findService->find($id);
        if (null === $planning) {
            throw $this->createNotFoundException('Ce planning n’existe pas.');
        }

        $form = $formFactory->create($planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PlanningAvailabilitiesDto $data */
            $data = $form->getData();
            $updateService->update($planning, $data->availabilities);
            $this->addFlash('success', 'Le planning et ses disponibilités ont été enregistrés.');

            return (new TurboStreamResponse())->refresh();
        }

        return new TurboStreamResponse($this->renderView('dashboard/reservation/planning/turbo/stream/availability.stream.html.twig', [
            'form' => $form,
        ]), status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
