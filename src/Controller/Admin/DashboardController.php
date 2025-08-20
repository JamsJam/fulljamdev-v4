<?php

namespace App\Controller\Admin;

use App\Service\ThemeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(
        ThemeService $theme_service,
    ): Response {
        $theme = $theme_service->getTheme();

        return $this->render('admin/dashboard/index.html.twig', [
            'theme' => $theme,
        ]);
    }
}
