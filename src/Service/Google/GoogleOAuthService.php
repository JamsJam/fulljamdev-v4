<?php

namespace App\Service\Google;

use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GoogleOAuthService
{
    private const AUTHORIZATION_ENDPOINT = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';
    private const CALENDAR_EVENTS_SCOPE = 'https://www.googleapis.com/auth/calendar.events';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $clientId,
        private string $clientSecret,
        private string $redirectUri,
        private string $refreshToken,
    ) {
    }

    public function getAuthorizationUrl(string $state): string
    {
        $this->assertClientConfiguration();

        return self::AUTHORIZATION_ENDPOINT.'?'.http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'scope' => self::CALENDAR_EVENTS_SCOPE,
            'state' => $state,
        ], '', '&', PHP_QUERY_RFC3986);
    }

    /** @return array<string, mixed> */
    public function exchangeAuthorizationCode(string $code): array
    {
        $this->assertClientConfiguration();

        return $this->requestToken([
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
        ]);
    }

    public function getAccessTokenFromRefreshToken(): string
    {
        $this->assertClientConfiguration();
        if ('' === $this->refreshToken) {
            throw new \DomainException('GOOGLE_REFRESH_TOKEN n’est pas configuré.');
        }

        $tokens = $this->requestToken([
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $this->refreshToken,
            'grant_type' => 'refresh_token',
        ]);
        $accessToken = $tokens['access_token'] ?? null;

        if (!is_string($accessToken) || '' === $accessToken) {
            throw new \DomainException('Google OAuth n’a retourné aucun jeton d’accès.');
        }

        return $accessToken;
    }

    /**
     * @param array<string, string> $body
     *
     * @return array<string, mixed>
     */
    private function requestToken(array $body): array
    {
        try {
            $response = $this->httpClient->request('POST', self::TOKEN_ENDPOINT, ['body' => $body]);
            $statusCode = $response->getStatusCode();
            $data = $response->toArray(false);
        } catch (ExceptionInterface $exception) {
            throw new \DomainException('Impossible de contacter Google OAuth.', previous: $exception);
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            $description = isset($data['error_description']) && is_string($data['error_description'])
                ? $data['error_description']
                : 'Google a refusé la demande OAuth.';

            throw new \DomainException($description);
        }

        return $data;
    }

    private function assertClientConfiguration(): void
    {
        if ('' === $this->clientId || '' === $this->clientSecret || '' === $this->redirectUri) {
            throw new \DomainException('La configuration OAuth Google est incomplète.');
        }
    }
}
