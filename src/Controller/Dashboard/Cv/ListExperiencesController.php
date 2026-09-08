<?php

namespace App\Controller\Dashboard\Cv;

use App\Application\Experience\Service\GetExperiencesService;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class ListExperiencesController extends AbstractController
{
    #[Route('/dashboard/cv', name: 'app_dashboard_cv', methods: ['GET'])]
    public function __invoke(Request $request, GetExperiencesService $service, BreadcrumbService $breadcrumbs): Response
    {
        return $this->render('dashboard/cv/index.html.twig', [
            'experiences' => $service->get(),
            'breadcrumb' => $breadcrumbs->getBreadcrumb($request->attributes->getString('_route')),
        ]);
    }
}
