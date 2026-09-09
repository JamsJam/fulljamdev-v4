<?php

namespace App\Controller\Dashboard\Project;

use App\Application\Project\Service\GetProjectsService;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class ListProjectsController extends AbstractController
{
    #[Route('/dashboard/projet', name: 'app_dashboard_project', methods: ['GET'])]
    public function __invoke(Request $request, GetProjectsService $service, BreadcrumbService $breadcrumbs): Response
    {
        return $this->render('dashboard/project/index.html.twig', [
            'projects' => $service->get(),
            'breadcrumb' => $breadcrumbs->getBreadcrumb($request->attributes->getString('_route')),
        ]);
    }
}
