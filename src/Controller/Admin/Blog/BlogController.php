<?php

namespace App\Controller\Admin\Blog;

use App\Service\ThemeService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\PageGenerator\Services\PageService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_admin_blog')]
    public function index(
        ThemeService $theme_service,
        PageService $pageService,
        #[Autowire('%kernel.project_dir%/src/Pages/Back/Blog/')] string $yamlFilePath
    ): Response
    {
        $theme = $theme_service->getTheme();


        $page = $pageService->createPageFromYamlFile($yamlFilePath . "index.yaml");

        dump($page);

        return $this->render('admin/blog/index.html.twig', [
            'theme' => $theme,
            'page' => $page,
        ]);
    }
}
