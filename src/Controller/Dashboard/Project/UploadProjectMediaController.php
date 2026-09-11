<?php

namespace App\Controller\Dashboard\Project;

use App\Application\Project\Asset\ProjectMediaUploader;
use App\Application\Project\Service\FindProjectService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class UploadProjectMediaController extends AbstractController
{
    #[Route('/dashboard/projet/media/upload', name: 'app_dashboard_project_media_upload', methods: ['POST'])]
    public function __invoke(
        Request $request,
        CsrfTokenManagerInterface $csrf,
        FindProjectService $projects,
        ProjectMediaUploader $uploader,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        if (!$csrf->isTokenValid(new CsrfToken('project-media-upload', $request->headers->get('X-CSRF-TOKEN')))) {
            return $this->json(['errorMessage' => 'Jeton de sécurité invalide.'], 403);
        }

        $project = null;
        $projectId = $this->projectId($request);
        if (null !== $projectId) {
            $project = $projects->find($projectId);
            if (null === $project) {
                return $this->json(['errorMessage' => 'Projet introuvable.'], 404);
            }
        }

        $file = $request->files->get('file');
        if (!$file instanceof UploadedFile) {
            return $this->json(['errorMessage' => 'Aucun fichier reçu.'], 422);
        }

        try {
            $media = $uploader->upload($file, $project);
            $entityManager->persist($media);
            $entityManager->flush();
        } catch (\Throwable $exception) {
            return $this->json(['errorMessage' => $exception->getMessage()], 422);
        }

        return $this->json(['result' => [[
            'url' => $media->getPath(),
            'name' => $media->getDisplayName(),
            'size' => $media->getSize(),
        ]]]);
    }

    private function projectId(Request $request): ?int
    {
        $projectId = $request->request->get('projectId');
        if (!is_string($projectId) || !ctype_digit($projectId) || 0 >= (int) $projectId) {
            return null;
        }

        return (int) $projectId;
    }
}
