<?php

namespace App\Controller\Dashboard\Blog;

use App\Application\Blog\Article\Asset\ArticleMediaUploader;
use App\Application\Blog\Article\Service\FindArticleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class UploadArticleMediaController extends AbstractController
{
    #[Route('/dashboard/blog/media/upload', name: 'app_dashboard_blog_media_upload', methods: ['POST'])]
    public function __invoke(
        Request $request,
        CsrfTokenManagerInterface $csrf,
        FindArticleService $articles,
        ArticleMediaUploader $uploader,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        if (!$this->hasValidCsrfToken($request, $csrf)) {
            return $this->json(['errorMessage' => 'Jeton de sécurité invalide.'], 403);
        }

        $article = null;
        $articleId = $this->articleId($request);
        if (null !== $articleId) {
            $article = $articles->find($articleId);
            if (null === $article) {
                return $this->json(['errorMessage' => 'Article introuvable.'], 404);
            }
        }

        $result = [];
        try {
            foreach ($request->files->all() as $file) {
                if (!$file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    continue;
                }

                $media = $uploader->upload($file, $article);
                $entityManager->persist($media);
                $result[] = [
                    'url' => $media->getPath(),
                    'name' => $media->getDisplayName(),
                    'size' => $media->getSize(),
                ];
            }

            if ([] === $result) {
                return $this->json(['errorMessage' => 'Aucun fichier reçu.'], 422);
            }

            $entityManager->flush();
        } catch (\Throwable $exception) {
            return $this->json(['errorMessage' => $exception->getMessage()], 422);
        }

        return $this->json(['result' => $result]);
    }

    private function hasValidCsrfToken(Request $request, CsrfTokenManagerInterface $csrf): bool
    {
        $token = new CsrfToken('blog-media-upload', $request->headers->get('X-CSRF-TOKEN'));

        return $csrf->isTokenValid($token);
    }

    private function articleId(Request $request): ?int
    {
        $articleId = $request->request->get('articleId');
        if (!is_string($articleId) || !ctype_digit($articleId) || 0 >= (int) $articleId) {
            return null;
        }

        return (int) $articleId;
    }
}
