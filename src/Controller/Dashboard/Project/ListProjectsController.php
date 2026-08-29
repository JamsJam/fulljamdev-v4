<?php

namespace App\Controller\Dashboard\Project;

use App\Application\Project\Service\GetProjectsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListProjectsController extends AbstractController
{
    #[Route('/dashboard/projet', name: 'app_dashboard_project', methods: ['GET'])]
    public function __invoke(GetProjectsService $service): Response
    {
        return $this->render('dashboard/content/project/index.html.twig', ['projects' => $service->get()]);
    }
}
