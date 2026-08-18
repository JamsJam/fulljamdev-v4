<?php

namespace App\Application\Reservation\Appointment\Notification;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Entity\Reservation\Appointment;
use App\Service\ConfigurationService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final readonly class RequestedAppointmentNotifier
{
    public function __construct(
        private MailerInterface $mailer,
        private ConfigurationService $configuration,
    ) {
    }

    public function notify(Appointment $appointment): void
    {
        if (AppointmentStatus::REQUESTED !== $appointment->getStatus()) {
            return;
        }

        $contact = $appointment->getContact();
        $accountEmail = (string) $this->configuration->get('account.email', '');

        if (null === $contact || null === $contact->getEmail() || '' === $accountEmail) {
            return;
        }

        $senderAddress = new Address(
            $accountEmail,
            trim(sprintf(
                '%s %s',
                $this->configuration->get('account.first_name', ''),
                $this->configuration->get('account.last_name', ''),
            )),
        );
        $context = [
            'appointment' => [
                'title' => $appointment->getTitle(),
                'timezone' => $appointment->getTimezone(),
                'startAt' => $appointment->getStartAt(),
                'endAt' => $appointment->getEndAt(),
                'planning' => [
                    'title' => $appointment->getPlanning()?->getTitle(),
                ],
                'contact' => [
                    'firstName' => $contact->getFirstName(),
                    'lastName' => $contact->getLastName(),
                    'email' => $contact->getEmail(),
                    'phoneNumber' => $contact->getPhoneNumber(),
                ],
            ],
        ];

        $this->mailer->send(
            (new TemplatedEmail())
                ->from($senderAddress)
                ->to(new Address(
                    $contact->getEmail(),
                    trim(sprintf('%s %s', $contact->getFirstName(), $contact->getLastName())),
                ))
                ->replyTo($senderAddress)
                ->subject(sprintf('Votre demande de rendez-vous — %s', $appointment->getPlanning()?->getTitle()))
                ->htmlTemplate('emails/reservation/appointment_requested_contact.html.twig')
                ->textTemplate('emails/reservation/appointment_requested_contact.txt.twig')
                ->context($context),
        );

        $this->mailer->send(
            (new TemplatedEmail())
                ->from($senderAddress)
                ->to($senderAddress)
                ->replyTo(new Address(
                    $contact->getEmail(),
                    trim(sprintf('%s %s', $contact->getFirstName(), $contact->getLastName())),
                ))
                ->subject(sprintf('Nouvelle demande de rendez-vous — %s', $appointment->getTitle()))
                ->htmlTemplate('emails/reservation/appointment_requested_user.html.twig')
                ->textTemplate('emails/reservation/appointment_requested_user.txt.twig')
                ->context($context),
        );
    }
}
