<?php

namespace App\Controller\Front;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class MissingPageController
{
    // Let the firewall and maintenance check run even for unknown public URLs.
    #[Route('/{path}', name: 'app_front_missing_page', requirements: ['path' => '(?!google(?:/|$)).+'], priority: -2000)]
    public function __invoke(): never
    {
        throw new NotFoundHttpException();
    }
}
