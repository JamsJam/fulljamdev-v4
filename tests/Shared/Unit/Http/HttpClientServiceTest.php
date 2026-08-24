<?php

namespace App\Tests\Shared\Unit\Http;

use App\Service\Http\HttpClientService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class HttpClientServiceTest extends TestCase
{
    public function testItSendsTheRequestAndReturnsTheSymfonyResponse(): void
    {
        $response = new MockResponse('{"success":true}', [
            'http_code' => 200,
            'response_headers' => ['content-type: application/json'],
        ]);
        $service = new HttpClientService(new MockHttpClient($response));

        $result = $service->request('POST', 'https://api.example.test/resource', [
            'json' => ['name' => 'Meeting'],
        ]);

        self::assertInstanceOf(ResponseInterface::class, $result);
        self::assertSame(['success' => true], $result->toArray());
    }
}
