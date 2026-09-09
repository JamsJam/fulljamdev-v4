<?php

namespace App\Controller\Front;

use App\Application\Project\Service\BrowsePublishedProjectsService;
use App\Repository\Project\TechnologyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectCatalogController extends AbstractController
{
    #[Route('/projets', name: 'app_front_projects', methods: ['GET'], priority: 10)]
    public function __invoke(Request $request, BrowsePublishedProjectsService $projects, TechnologyRepository $technologies): Response
    {
        $query = mb_substr(trim($request->query->getString('q')), 0, 100);
        $technologyId = $request->query->getInt('technology');

        return $this->render('front/project/index.html.twig', [
            'projects' => $projects->browse($query, $technologyId > 0 ? $technologyId : null, max(1, $request->query->getInt('page', 1))),
            'technologies' => $technologies->findBy([], ['name' => 'ASC']),
            'query' => $query,
            'selected_technology' => $technologyId,
        ]);
    }
}
