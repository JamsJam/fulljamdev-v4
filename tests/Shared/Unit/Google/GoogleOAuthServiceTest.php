<?php

namespace App\Tests\Shared\Unit\Google;

use App\Service\Google\GoogleOAuthService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class GoogleOAuthServiceTest extends TestCase
{
    public function testItBuildsTheOfflineConsentAuthorizationUrl(): void
    {
        $service = $this->service(new MockHttpClient());

        $query = parse_url($service->getAuthorizationUrl('secure-state'), PHP_URL_QUERY);
        self::assertIsString($query);
        parse_str($query, $parameters);

        self::assertSame('client-id', $parameters['client_id']);
        self::assertSame('http://localhost:8000/google/callback', $parameters['redirect_uri']);
        self::assertSame('code', $parameters['response_type']);
        self::assertSame('offline', $parameters['access_type']);
        self::assertSame('consent', $parameters['prompt']);
        self::assertSame('https://www.googleapis.com/auth/calendar.events', $parameters['scope']);
        self::assertSame('secure-state', $parameters['state']);
    }

    public function testItExchangesAnAuthorizationCodeUsingFormData(): void
    {
        $request = null;
        $client = new MockHttpClient(static function (string $method, string $url, array $options) use (&$request): MockResponse {
            $request = compact('method', 'url', 'options');

            return self::jsonResponse(['access_token' => 'temporary', 'refresh_token' => 'permanent']);
        });

        $tokens = $this->service($client)->exchangeAuthorizationCode('authorization-code');

        self::assertSame('permanent', $tokens['refresh_token']);
        self::assertSame('POST', $request['method']);
        self::assertSame('https://oauth2.googleapis.com/token', $request['url']);
        parse_str($request['options']['body'], $body);
        self::assertSame('authorization-code', $body['code']);
        self::assertSame('authorization_code', $body['grant_type']);
        self::assertSame('client-secret', $body['client_secret']);
    }

    public function testItGetsAnAccessTokenFromTheConfiguredRefreshToken(): void
    {
        $client = new MockHttpClient(static function (string $method, string $url, array $options): MockResponse {
            parse_str($options['body'], $body);
            self::assertSame('refresh-token', $body['refresh_token']);
            self::assertSame('refresh_token', $body['grant_type']);

            return self::jsonResponse(['access_token' => 'fresh-access-token']);
        });

        self::assertSame('fresh-access-token', $this->service($client)->getAccessTokenFromRefreshToken());
    }

    private function service(MockHttpClient $client): GoogleOAuthService
    {
        return new GoogleOAuthService(
            $client,
            'client-id',
            'client-secret',
            'http://localhost:8000/google/callback',
            'refresh-token',
        );
    }

    /** @param array<string, mixed> $data */
    private static function jsonResponse(array $data): MockResponse
    {
        return new MockResponse(json_encode($data, JSON_THROW_ON_ERROR), [
            'http_code' => 200,
            'response_headers' => ['content-type: application/json'],
        ]);
    }
}
