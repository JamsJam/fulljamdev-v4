<?php

namespace App\Controller\Front;

use App\Application\Legal\Service\LegalPageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalPageController extends AbstractController
{
    #[Route('/mention-legal', name: 'app_front_legal_notice', defaults: ['slug' => 'mention-legal'], methods: ['GET'])]
    #[Route('/politique-confidentialite', name: 'app_front_privacy_policy', defaults: ['slug' => 'politique-confidentialite'], methods: ['GET'])]
    #[Route('/polotique-cookies', name: 'app_front_cookie_policy', defaults: ['slug' => 'polotique-cookies'], methods: ['GET'])]
    public function __invoke(string $slug, LegalPageService $pages): Response
    {
        $page = $pages->get($slug) ?? throw $this->createNotFoundException('Cette page légale n’existe pas.');

        return $this->render('front/legal/show.html.twig', ['legal_page' => $page]);
    }
}
