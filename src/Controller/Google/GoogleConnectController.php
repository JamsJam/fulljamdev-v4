<?php

namespace App\Controller\Google;

use App\Service\Google\GoogleOAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class GoogleConnectController extends AbstractController
{
    #[Route('/google/connect', name: 'app_google_connect', methods: ['GET'])]
    public function __invoke(Request $request, GoogleOAuthService $googleOAuth): RedirectResponse
    {
        $state = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $request->getSession()->set(GoogleCallbackController::OAUTH_STATE_SESSION_KEY, $state);

        return $this->redirect($googleOAuth->getAuthorizationUrl($state));
    }
}
