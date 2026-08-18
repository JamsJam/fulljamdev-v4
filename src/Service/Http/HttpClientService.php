<?php

namespace App\Service\Http;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final readonly class HttpClientService
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->httpClient->request($method, $url, $options);
    }
}
