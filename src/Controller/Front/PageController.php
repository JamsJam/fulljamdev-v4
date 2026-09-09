<?php

namespace App\Controller\Front;

use App\Application\Page\Page\Builder\PageBuilder;
use App\Application\Page\Page\Service\FindPageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route(
        '/{path}',
        name: 'app_front_page',
        requirements: ['path' => '[a-z0-9]+(?:-[a-z0-9]+)*(?:/[a-z0-9]+(?:-[a-z0-9]+)*)*'],
        methods: ['GET'],
        priority: -1000,
    )]
    public function __invoke(string $path, FindPageService $service, PageBuilder $builder): Response
    {
        $page = $service->findByPath($path) ?? throw $this->createNotFoundException('Cette page n’existe pas.');

        return $this->render('front/page/show.html.twig', ['page' => $builder->build($page)]);
    }
}
