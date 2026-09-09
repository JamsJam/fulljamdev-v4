<?php

namespace App\Controller\Dashboard\Reservation\Planning;

use App\Application\Reservation\Planner\Service\FindPlanningService;
use App\Application\Reservation\Planner\Service\PlanningInvitationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;

#[IsGranted('ROLE_ADMIN')]
final class PlanningInvitationController extends AbstractController
{
    #[Route('/dashboard/reservations/plannings/{id}/invitation', name: 'app_dashboard_reservation_planning_invitation', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function __invoke(int $id, Request $request, FindPlanningService $finder, PlanningInvitationService $invitations): Response
    {
        $planning = $finder->find($id) ?? throw $this->createNotFoundException('Ce planning n’existe pas.');
        $form = $this->createFormBuilder(['hours' => 24])
            ->add('hours', IntegerType::class, [
                'label' => 'Validité du lien (en heures)',
                'attr' => ['min' => 1, 'max' => 72],
                'constraints' => [new Assert\NotBlank(), new Assert\Range(min: 1, max: 72)],
            ])->getForm();
        $form->handleRequest($request);
        $url = null;
        if ($planning->isActive() && $form->isSubmitted() && $form->isValid()) {
            $url = $this->generateUrl('app_front_planning_appointment', [
                'slug' => $planning->getSlug(),
                'access' => $invitations->create($planning, $form->get('hours')->getData()),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $response = $this->render('dashboard/reservation/planning/invitation.html.twig', [
            'planning' => $planning,
            'form' => $form,
            'invitation_url' => $url,
        ], new Response(status: $form->isSubmitted() && (!$form->isValid() || !$planning->isActive()) ? 422 : 200));
        $response->headers->set('Cache-Control', 'private, no-store');

        return $response;
    }
}
