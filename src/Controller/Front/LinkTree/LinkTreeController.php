<?php

namespace App\Controller\Front\LinkTree;

use App\Application\LinkTree\Service\GetPublicLinkTreeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LinkTreeController extends AbstractController
{
    #[Route('/linktree', name: 'app_front_linktree', methods: ['GET'], priority: 10)]
    public function __invoke(GetPublicLinkTreeService $service): Response
    {
        return $this->render('front/linktree/show.html.twig', $service->get());
    }
}
