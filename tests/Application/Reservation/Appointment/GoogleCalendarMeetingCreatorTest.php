<?php

namespace App\Tests\Application\Reservation\Appointment;

use App\Application\Reservation\Appointment\Meeting\GoogleCalendarMeetingCreator;
use App\Entity\Contact;
use App\Entity\Reservation\Appointment;
use App\Service\Http\HttpClientService;
use App\Service\UuidGeneratorService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class GoogleCalendarMeetingCreatorTest extends TestCase
{
    public function testItCreatesACalendarEventWithAUniqueGoogleMeetConference(): void
    {
        $requests = [];
        $client = new MockHttpClient(static function (string $method, string $url, array $options) use (&$requests): MockResponse {
            $requests[] = compact('method', 'url', 'options');

            if (str_contains($url, 'oauth2.googleapis.com')) {
                return self::jsonResponse(['access_token' => 'access-token']);
            }

            return self::jsonResponse(['hangoutLink' => 'https://meet.google.com/abc-defg-hij']);
        });
        $creator = new GoogleCalendarMeetingCreator(
            new HttpClientService($client),
            new UuidGeneratorService(),
            new NullLogger(),
            'client-id',
            'client-secret',
            'refresh-token',
            'primary',
        );

        $link = $creator->create($this->appointment());

        self::assertSame('https://meet.google.com/abc-defg-hij', $link);
        self::assertCount(2, $requests);
        self::assertStringContainsString('conferenceDataVersion=1', $requests[1]['url']);
        $payload = json_decode($requests[1]['options']['body'], true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('hangoutsMeet', $payload['conferenceData']['createRequest']['conferenceSolutionKey']['type']);
        self::assertSame('contact@example.test', $payload['attendees'][0]['email']);
        self::assertMatchesRegularExpression('/^[0-9a-f-]{36}$/', $payload['conferenceData']['createRequest']['requestId']);
    }

    public function testItDoesNothingWhenGoogleCalendarIsNotConfigured(): void
    {
        $client = new MockHttpClient(static fn (): never => throw new \LogicException('No request expected.'));
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('warning')
            ->with("L'api google n'est pas configuré");
        $creator = new GoogleCalendarMeetingCreator(new HttpClientService($client), new UuidGeneratorService(), $logger, '', '', '', 'primary');

        self::assertNull($creator->create($this->appointment()));
    }

    private function appointment(): Appointment
    {
        return (new Appointment())
            ->setContact(
                (new Contact())
                    ->setFirstName('Ada')
                    ->setLastName('Lovelace')
                    ->setEmail('contact@example.test')
                    ->setPhoneNumber('0102030405'),
            )
            ->setTitle('Présentation du projet')
            ->setStartAt(new \DateTimeImmutable('2026-08-20 10:00:00', new \DateTimeZone('Europe/Paris')))
            ->setEndAt(new \DateTimeImmutable('2026-08-20 10:30:00', new \DateTimeZone('Europe/Paris')));
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
