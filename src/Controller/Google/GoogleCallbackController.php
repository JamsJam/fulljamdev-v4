<?php

namespace App\Controller\Google;

use App\Service\Google\GoogleOAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class GoogleCallbackController extends AbstractController
{
    public const OAUTH_STATE_SESSION_KEY = 'google_oauth_state';

    #[Route('/google/callback', name: 'app_google_callback', methods: ['GET'])]
    public function __invoke(Request $request, GoogleOAuthService $googleOAuth): Response
    {
        $session = $request->getSession();
        $expectedState = $session->get(self::OAUTH_STATE_SESSION_KEY);
        $session->remove(self::OAUTH_STATE_SESSION_KEY);
        $state = $request->query->get('state');

        if (!is_string($expectedState) || !is_string($state) || !hash_equals($expectedState, $state)) {
            throw new BadRequestHttpException('Le paramètre OAuth state est invalide.');
        }

        $error = $request->query->get('error');
        if (is_string($error) && '' !== $error) {
            throw new BadRequestHttpException('Google a refusé l’autorisation OAuth.');
        }

        $code = $request->query->get('code');
        if (!is_string($code) || '' === $code) {
            throw new BadRequestHttpException('Le code d’autorisation Google est absent.');
        }

        $tokens = $googleOAuth->exchangeAuthorizationCode($code);
        $refreshToken = $tokens['refresh_token'] ?? null;
        if (!is_string($refreshToken) || '' === $refreshToken) {
            throw new BadRequestHttpException('Google n’a retourné aucun refresh token. Révoquez l’accès existant puis recommencez avec prompt=consent.');
        }

        $response = $this->render('google/oauth_success.html.twig', ['refresh_token' => $refreshToken]);
        $response->setPrivate();
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('Content-Security-Policy', "default-src 'none'; style-src 'unsafe-inline'; base-uri 'none'; form-action 'none'; frame-ancestors 'none'");
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');

        return $response;
    }
}
