<?php

namespace App\Controller\Front\Legal;

use App\Application\Legal\Service\LegalPageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalPageController extends AbstractController
{
    #[Route(
        '/legal/{slug}',
        name: 'app_front_legal',
        requirements: ['slug' => 'mention-legal|politique-confidentialite|politique-cookies'],
        methods: ['GET'],
    )]
    public function __invoke(string $slug, LegalPageService $pages): Response
    {
        $page = $pages->get($slug) ?? throw $this->createNotFoundException('Cette page légale n’existe pas.');

        return $this->render('front/legal/show.html.twig', ['legal_page' => $page]);
    }
}
