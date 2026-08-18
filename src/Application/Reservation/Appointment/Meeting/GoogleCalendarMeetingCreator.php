<?php

namespace App\Application\Reservation\Appointment\Meeting;

use App\Entity\Reservation\Appointment;
use App\Service\Http\HttpClientService;
use App\Service\UuidGeneratorService;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

final readonly class GoogleCalendarMeetingCreator
{
    private const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';
    private const CALENDAR_ENDPOINT = 'https://www.googleapis.com/calendar/v3/calendars/%s/events';

    public function __construct(
        private HttpClientService $httpClient,
        private UuidGeneratorService $uuidGenerator,
        private LoggerInterface $logger,
        private string $clientId,
        private string $clientSecret,
        private string $refreshToken,
        private string $calendarId,
    ) {
    }

    public function create(Appointment $appointment): ?string
    {
        if (null !== $appointment->getLink()) {
            return $appointment->getLink();
        }

        if (!$this->isConfigured()) {
            $this->logger->warning("L'api google n'est pas configuré");

            return null;
        }

        $startAt = $appointment->getStartAt();
        $endAt = $appointment->getEndAt();
        $contact = $appointment->getContact();

        if (null === $startAt || null === $endAt || null === $contact || null === $contact->getEmail()) {
            throw new \DomainException('Le rendez-vous ne contient pas toutes les informations nécessaires à Google Calendar.');
        }

        try {
            $token = $this->httpClient->request('POST', self::TOKEN_ENDPOINT, [
                'body' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $this->refreshToken,
                    'grant_type' => 'refresh_token',
                ],
            ])->toArray();

            if (!isset($token['access_token']) || !is_string($token['access_token'])) {
                throw new \DomainException('Google OAuth n’a retourné aucun jeton d’accès.');
            }

            $event = $this->httpClient->request(
                'POST',
                sprintf(self::CALENDAR_ENDPOINT, rawurlencode($this->calendarId)),
                [
                    'auth_bearer' => $token['access_token'],
                    'query' => ['conferenceDataVersion' => 1, 'sendUpdates' => 'all'],
                    'json' => [
                        'summary' => $appointment->getTitle(),
                        'description' => $appointment->getDescription(),
                        'start' => [
                            'dateTime' => $startAt->format(\DateTimeInterface::RFC3339),
                            'timeZone' => $startAt->getTimezone()->getName(),
                        ],
                        'end' => [
                            'dateTime' => $endAt->format(\DateTimeInterface::RFC3339),
                            'timeZone' => $endAt->getTimezone()->getName(),
                        ],
                        'attendees' => [[
                            'email' => $contact->getEmail(),
                            'displayName' => trim(sprintf('%s %s', $contact->getFirstName(), $contact->getLastName())),
                        ]],
                        'conferenceData' => [
                            'createRequest' => [
                                'requestId' => $this->uuidGenerator->v4(),
                                'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                            ],
                        ],
                    ],
                ],
            )->toArray();
        } catch (ExceptionInterface $exception) {
            throw new \DomainException('Impossible de créer le rendez-vous Google Calendar.', previous: $exception);
        }

        $meetingLink = $this->extractMeetingLink($event);
        if (null === $meetingLink) {
            throw new \DomainException('Google Calendar a créé l’événement sans retourner de lien Google Meet.');
        }

        return $meetingLink;
    }

    private function isConfigured(): bool
    {
        return '' !== $this->clientId
            && '' !== $this->clientSecret
            && '' !== $this->refreshToken
            && '' !== $this->calendarId;
    }

    /** @param array<string, mixed> $event */
    private function extractMeetingLink(array $event): ?string
    {
        if (isset($event['hangoutLink']) && is_string($event['hangoutLink'])) {
            return $event['hangoutLink'];
        }

        $entryPoints = $event['conferenceData']['entryPoints'] ?? [];
        if (!is_array($entryPoints)) {
            return null;
        }

        foreach ($entryPoints as $entryPoint) {
            if (is_array($entryPoint)
                && 'video' === ($entryPoint['entryPointType'] ?? null)
                && isset($entryPoint['uri'])
                && is_string($entryPoint['uri'])) {
                return $entryPoint['uri'];
            }
        }

        return null;
    }
}
