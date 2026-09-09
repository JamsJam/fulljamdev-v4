<?php

namespace App\Tests\Reservation\Unit\Appointment;

use App\Application\Reservation\Appointment\Meeting\GoogleCalendarMeetingCreator;
use App\Entity\Reservation\Appointment;
use App\Service\Google\GoogleOAuthService;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class GoogleCalendarMeetingCreatorTest extends TestCase
{
    public function testCalendarRejectionIsLoggedWithoutPayloadOrToken(): void
    {
        $handler = new TestHandler();
        $client = new MockHttpClient([
            self::jsonResponse(['access_token' => 'private-access-token']),
            self::jsonResponse(['error' => ['message' => 'private-response-content', 'errors' => [['reason' => 'insufficientPermissions']]]], 403),
        ]);
        $creator = new GoogleCalendarMeetingCreator($client, $this->oauth($client), new Logger('google', [$handler]));
        try {
            $creator->create($this->appointment());
            self::fail('Expected a Calendar rejection');
        } catch (\DomainException) {
            self::assertTrue($handler->hasErrorThatContains('google.calendar.request_rejected'));
        }
        $records = $handler->getRecords();
        $last = $records[array_key_last($records)];
        self::assertSame(403, $last->context['status_code']);
        self::assertSame('insufficientPermissions', $last->context['error_code']);
        self::assertArrayHasKey('duration_ms', $last->context);
        self::assertStringNotContainsString('private-', json_encode($records, JSON_THROW_ON_ERROR));
    }

    public function testItCreatesAnEventWithoutGuestAndReturnsTheMeetLink(): void
    {
        $requests = [];
        $client = new MockHttpClient(static function (string $method, string $url, array $options) use (&$requests): MockResponse {
            $requests[] = compact('method', 'url', 'options');

            if (str_contains($url, 'oauth2.googleapis.com/token')) {
                return self::jsonResponse(['access_token' => 'temporary-access-token']);
            }

            return self::jsonResponse([
                'id' => 'google-event-id',
                'hangoutLink' => 'https://meet.google.com/abc-defg-hij',
            ]);
        });
        $creator = new GoogleCalendarMeetingCreator($client, $this->oauth($client));

        $link = $creator->create($this->appointment());

        self::assertSame('https://meet.google.com/abc-defg-hij', $link);
        self::assertCount(2, $requests);
        self::assertSame('POST', $requests[1]['method']);
        self::assertSame(
            'https://www.googleapis.com/calendar/v3/calendars/primary/events?conferenceDataVersion=1&sendUpdates=none',
            $requests[1]['url'],
        );
        $event = json_decode($requests[1]['options']['body'], true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('hangoutsMeet', $event['conferenceData']['createRequest']['conferenceSolutionKey']['type']);
        self::assertArrayNotHasKey('attendees', $event);
        self::assertSame('Europe/Paris', $event['start']['timeZone']);
    }

    public function testItReportsTheGoogleCalendarError(): void
    {
        $client = new MockHttpClient([
            self::jsonResponse(['access_token' => 'temporary-access-token']),
            self::jsonResponse(['error' => ['message' => 'Calendar API disabled']], 403),
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Calendar API disabled');

        (new GoogleCalendarMeetingCreator($client, $this->oauth($client)))->create($this->appointment());
    }

    public function testItFetchesTheEventUntilGoogleReturnsTheMeetLink(): void
    {
        $requests = [];
        $client = new MockHttpClient(static function (string $method, string $url) use (&$requests): MockResponse {
            $requests[] = compact('method', 'url');

            return match (count($requests)) {
                1 => self::jsonResponse(['access_token' => 'temporary-access-token']),
                2 => self::jsonResponse(['id' => 'google/event id']),
                default => self::jsonResponse([
                    'id' => 'google/event id',
                    'hangoutLink' => 'https://meet.google.com/abc-defg-hij',
                ]),
            };
        });

        $link = (new GoogleCalendarMeetingCreator($client, $this->oauth($client)))->create($this->appointment());

        self::assertSame('https://meet.google.com/abc-defg-hij', $link);
        self::assertCount(3, $requests);
        self::assertSame('GET', $requests[2]['method']);
        self::assertSame(
            'https://www.googleapis.com/calendar/v3/calendars/primary/events/google%2Fevent%20id',
            $requests[2]['url'],
        );
    }

    private function oauth(MockHttpClient $client): GoogleOAuthService
    {
        return new GoogleOAuthService(
            $client,
            'client-id',
            'client-secret',
            'http://localhost:8001/google/callback',
            'refresh-token',
        );
    }

    private function appointment(): Appointment
    {
        return (new Appointment())
            ->setTitle('Audit du projet')
            ->setDescription('Présentation du besoin et prochaines étapes.')
            ->setStartAt(new \DateTimeImmutable('2026-09-10 14:00:00 Europe/Paris'))
            ->setEndAt(new \DateTimeImmutable('2026-09-10 15:00:00 Europe/Paris'))
            ->setTimezone('Europe/Paris');
    }

    /** @param array<string, mixed> $data */
    private static function jsonResponse(array $data, int $status = 200): MockResponse
    {
        return new MockResponse(json_encode($data, JSON_THROW_ON_ERROR), [
            'http_code' => $status,
            'response_headers' => ['content-type: application/json'],
        ]);
    }
}
