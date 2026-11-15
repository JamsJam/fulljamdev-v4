<?php

namespace App\Controller\Front\Sitemap;

use App\Application\Page\Page\Service\HomepageService;
use App\Repository\Blog\ArticleRepository;
use App\Repository\Page\PageRepository;
use App\Repository\Project\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_front_sitemap', methods: ['GET'])]
    public function __invoke(
        PageRepository $pageRepository,
        ArticleRepository $articleRepository,
        ProjectRepository $projectRepository,
        HomepageService $homepageService,
        ClockInterface $clock,
    ): Response {
        $urls = [
            ['loc' => $this->generateUrl('app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)],
            ['loc' => $this->generateUrl('app_front_blog', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)],
            ['loc' => $this->generateUrl('app_front_projects', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)],
        ];

        $homepagePageId = $homepageService->getConfiguredPageId();

        foreach ($pageRepository->findAll() as $page) {
            if (
                $page->getId() === $homepagePageId
                || '' === $page->getPath()
                || true === ($page->getSeo()['noIndex'] ?? false)
            ) {
                continue;
            }

            $urls[] = [
                'loc' => $this->generateUrl(
                    'app_front_page',
                    ['path' => $page->getPath()],
                    UrlGeneratorInterface::ABSOLUTE_URL,
                ),
            ];
        }

        $now = \DateTimeImmutable::createFromInterface($clock->now());

        foreach ($articleRepository->createPublishedCatalogQuery($now)->getQuery()->getResult() as $article) {
            if (null === $article->getSlug()) {
                continue;
            }

            $urls[] = [
                'loc' => $this->generateUrl(
                    'app_front_article_show',
                    ['slug' => $article->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL,
                ),
                'lastmod' => $article->getUpdatedAt()?->format('Y-m-d'),
            ];
        }

        foreach ($projectRepository->createPublishedCatalogQuery($now)->getQuery()->getResult() as $project) {
            $urls[] = [
                'loc' => $this->generateUrl(
                    'app_front_project_show',
                    ['slug' => $project->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL,
                ),
                'lastmod' => $project->getUpdatedAt()?->format('Y-m-d'),
            ];
        }

        return $this->render('sitemap/index.html.twig', ['urls' => $urls], new Response(headers: [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]));
    }
}
