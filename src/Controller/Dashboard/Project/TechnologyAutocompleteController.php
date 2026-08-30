<?php

namespace App\Controller\Dashboard\Project;

use App\Entity\Project\Technology;
use App\Repository\Project\TechnologyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class TechnologyAutocompleteController extends AbstractController
{
    #[Route('/dashboard/projet/technologie/autocomplete', name: 'app_dashboard_project_technology_autocomplete', methods: ['GET'])]
    public function __invoke(TechnologyRepository $technologies): JsonResponse
    {
        return $this->json(array_map(
            static fn (Technology $technology): string => $technology->getName(),
            $technologies->findBy([], ['name' => 'ASC']),
        ));
    }
}
