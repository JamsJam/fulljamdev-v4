<?php

namespace App\Controller\Front;

use App\Application\Project\Service\FindPublishedProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectShowController extends AbstractController
{
    #[Route('/projet/{slug}', name: 'app_front_project_show', requirements: ['slug' => '[a-z0-9]+(?:-[a-z0-9]+)*'], methods: ['GET'], priority: 10)]
    public function __invoke(string $slug, FindPublishedProjectService $projects): Response
    {
        $project = $projects->findBySlug($slug) ?? throw $this->createNotFoundException('Ce projet n’est pas disponible.');

        return $this->render('front/project/show.html.twig', ['project' => $project]);
    }
}
