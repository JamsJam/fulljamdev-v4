<?php

namespace App\Controller\Dashboard\Legal;

use App\Application\Legal\Form\LegalSettingsType;
use App\Application\Legal\Service\LegalPageService;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class LegalController extends AbstractController
{
    #[Route('/dashboard/legal', name: 'app_dashboard_legal', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, LegalPageService $pages, BreadcrumbService $breadcrumbs): Response
    {
        $settings = $pages->settings();
        $form = $this->createForm(LegalSettingsType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pages->save($settings);
            $this->addFlash('success', 'Les pages légales ont été enregistrées.');

            return $this->redirectToRoute('app_dashboard_legal', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/legal/edit.html.twig', [
            'form' => $form,
            'breadcrumb' => $breadcrumbs->getBreadcrumb($request->attributes->getString('_route')),
        ], new Response(status: $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK));
    }
}
