<?php

namespace App\Controller\Front;

use App\Application\Page\Page\Builder\PageBuilder;
use App\Application\Page\Page\Service\HomepageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(HomepageService $homepageService, PageBuilder $pageBuilder): Response
    {
        $page = $homepageService->get()
            ?? throw $this->createNotFoundException('Aucune page d’accueil n’est configurée.');

        return $this->render('front/page/show.html.twig', [
            'page' => $pageBuilder->build($page),
        ]);
    }
}
