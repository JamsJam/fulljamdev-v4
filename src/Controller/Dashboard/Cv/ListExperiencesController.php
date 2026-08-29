<?php

namespace App\Controller\Dashboard\Cv;

use App\Application\Experience\Service\GetExperiencesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListExperiencesController extends AbstractController
{
    #[Route('/dashboard/cv', name: 'app_dashboard_cv', methods: ['GET'])]
    public function __invoke(GetExperiencesService $service): Response
    {
        return $this->render('dashboard/content/cv/index.html.twig', ['experiences' => $service->get()]);
    }
}
