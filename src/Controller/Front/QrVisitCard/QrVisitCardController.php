<?php

namespace App\Controller\Front\QrVisitCard;

use App\Application\QrVisitCard\Service\QrVisitCardRouteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QrVisitCardController extends AbstractController
{
    #[Route('/qrvisitcard', name: 'app_qr_visit_card', methods: ['GET'])]
    public function __invoke(QrVisitCardRouteService $routeService): Response
    {
        return $this->redirect($routeService->read());
    }
}
