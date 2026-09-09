<?php

namespace App\Application\Reservation\Appointment\Meeting;

use App\Entity\Reservation\Appointment;
use App\Service\Google\GoogleOAuthService;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GoogleCalendarMeetingCreator implements MeetingLinkCreatorInterface
{
    private const EVENTS_ENDPOINT = 'https://www.googleapis.com/calendar/v3/calendars/primary/events';

    public function __construct(
        private HttpClientInterface $httpClient,
        private GoogleOAuthService $googleOAuth,
        #[Autowire(service: 'monolog.logger.google')]
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function create(Appointment $appointment): string
    {
        $startAt = $appointment->getStartAt();
        $endAt = $appointment->getEndAt();
        $title = trim((string) $appointment->getTitle());

        if (null === $startAt || null === $endAt || $startAt >= $endAt || '' === $title) {
            $this->logger->error('google.calendar.invalid_appointment', ['appointment_id' => $appointment->getId()]);
            throw new \DomainException('Le rendez-vous ne contient pas les informations nécessaires à la création du Google Meet.');
        }

        $this->logger->info('google.calendar.meeting_creation_started', ['appointment_id' => $appointment->getId()]);
        $accessToken = $this->googleOAuth->getAccessTokenFromRefreshToken();
        $event = $this->request('POST', self::EVENTS_ENDPOINT.'?conferenceDataVersion=1&sendUpdates=none', $accessToken, [
            'summary' => $title,
            'description' => $appointment->getDescription(),
            'start' => [
                'dateTime' => $startAt->format(\DateTimeInterface::RFC3339),
                'timeZone' => $appointment->getTimezone(),
            ],
            'end' => [
                'dateTime' => $endAt->format(\DateTimeInterface::RFC3339),
                'timeZone' => $appointment->getTimezone(),
            ],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => bin2hex(random_bytes(16)),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                ],
            ],
        ]);

        $meetingLink = $this->extractMeetingLink($event);
        $eventId = $event['id'] ?? null;

        for ($attempt = 0; null === $meetingLink && is_string($eventId) && '' !== $eventId && $attempt < 4; ++$attempt) {
            usleep(250_000);
            $event = $this->request('GET', self::EVENTS_ENDPOINT.'/'.rawurlencode($eventId), $accessToken);
            $meetingLink = $this->extractMeetingLink($event);
        }

        if (null === $meetingLink) {
            $this->logger->error('google.calendar.meeting_link_missing', ['appointment_id' => $appointment->getId()]);
            throw new \DomainException('Google Calendar a créé l’événement sans retourner de lien Meet.');
        }

        $this->logger->info('google.calendar.meeting_created', ['appointment_id' => $appointment->getId()]);

        return $meetingLink;
    }

    /**
     * @param array<string, mixed>|null $json
     *
     * @return array<string, mixed>
     */
    private function request(string $method, string $url, string $accessToken, ?array $json = null): array
    {
        $started = microtime(true);
        $context = ['request_id' => bin2hex(random_bytes(8)), 'operation' => 'POST' === $method ? 'create_event' : 'fetch_event'];
        $this->logger->info('google.calendar.request_started', $context);
        $statusCode = null;
        $options = [
            'auth_bearer' => $accessToken,
            'headers' => ['Accept' => 'application/json'],
        ];
        if (null !== $json) {
            $options['json'] = $json;
        }

        try {
            $response = $this->httpClient->request($method, $url, $options);
            $statusCode = $response->getStatusCode();
            $data = $response->toArray(false);
        } catch (ExceptionInterface $exception) {
            $this->logger->error('google.calendar.request_failed', $context + [
                'status_code' => $statusCode, 'duration_ms' => round((microtime(true) - $started) * 1000),
                'exception_class' => $exception::class,
            ]);
            throw new \DomainException('Impossible de contacter Google Calendar.', previous: $exception);
        }

        $context += ['status_code' => $statusCode, 'duration_ms' => round((microtime(true) - $started) * 1000)];
        if ($statusCode < 200 || $statusCode >= 300) {
            $reason = $data['error']['errors'][0]['reason'] ?? null;
            $this->logger->error('google.calendar.request_rejected', $context + [
                'error_code' => in_array($reason, ['authError', 'forbidden', 'insufficientPermissions', 'accessNotConfigured', 'rateLimitExceeded', 'userRateLimitExceeded', 'quotaExceeded', 'notFound', 'invalid', 'badRequest', 'backendError'], true) ? $reason : 'unknown',
            ]);
            $message = $data['error']['message'] ?? null;

            throw new \DomainException(is_string($message) && '' !== $message ? $message : 'Google Calendar a refusé la création du rendez-vous.');
        }

        $this->logger->info('google.calendar.request_succeeded', $context);

        return $data;
    }

    /** @param array<string, mixed> $event */
    private function extractMeetingLink(array $event): ?string
    {
        $link = $event['hangoutLink'] ?? null;
        if (!is_string($link) || 'https' !== parse_url($link, PHP_URL_SCHEME) || 'meet.google.com' !== parse_url($link, PHP_URL_HOST)) {
            return null;
        }

        return $link;
    }
}
