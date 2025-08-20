<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SitemapController extends AbstractController
{
    #[Route('/sitemap', name: 'app_sitemap')]
    public function index(): Response
    {
        $paths = [
            'app_home',
            
        ];
        // find published blog posts from db
        
        $today = new \DateTimeImmutable();

        $urls = [];
                foreach ($paths as $path) {
            $urls[] = [
                'loc' => $this->generateUrl(
                    $path,
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'lastmod' => "2025-08-19",
                'changefreq' => 'monthly',
                'priority' => '1.0',
            ];
        }
        $response = new Response(
            $this->renderView('sitemap/index.html.twig', ['urls' => $urls]),
            200
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
