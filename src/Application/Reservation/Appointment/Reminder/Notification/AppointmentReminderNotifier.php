<?php

namespace App\Application\Reservation\Appointment\Reminder\Notification;

use App\Application\Reservation\Appointment\Reminder\Enum\AppointmentReminderType;
use App\Application\Settings\Service\GetAccountSettingsService;
use App\Entity\Reservation\Appointment;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final readonly class AppointmentReminderNotifier
{
    public function __construct(
        private MailerInterface $mailer,
        private GetAccountSettingsService $getAccountSettingsService,
    ) {
    }

    public function notify(Appointment $appointment, AppointmentReminderType $type): void
    {
        $contact = $appointment->getContact();
        $account = $this->getAccountSettingsService->get();
        $accountEmail = $account->email;

        if (null === $contact || null === $contact->getEmail() || '' === $accountEmail) {
            return;
        }

        $sender = new Address($accountEmail, trim(sprintf(
            '%s %s',
            $account->firstName,
            $account->lastName,
        )));
        $template = match ($type) {
            AppointmentReminderType::DAY_BEFORE => 'day_before',
            AppointmentReminderType::HOUR_BEFORE => 'hour_before',
        };
        $subject = match ($type) {
            AppointmentReminderType::DAY_BEFORE => 'Notre rendez-vous est demain',
            AppointmentReminderType::HOUR_BEFORE => 'Notre meeting va bientôt commencer',
        };

        $this->mailer->send(
            (new TemplatedEmail())
                ->from($sender)
                ->to(new Address(
                    $contact->getEmail(),
                    trim(sprintf('%s %s', $contact->getFirstName(), $contact->getLastName())),
                ))
                ->replyTo($sender)
                ->subject($subject)
                ->htmlTemplate(sprintf('emails/reservation/reminder_%s.html.twig', $template))
                ->textTemplate(sprintf('emails/reservation/reminder_%s.txt.twig', $template))
                ->context(['appointment' => [
                    'title' => $appointment->getTitle(),
                    'timezone' => $appointment->getTimezone(),
                    'startAt' => $appointment->getStartAt(),
                    'endAt' => $appointment->getEndAt(),
                    'link' => $appointment->getLink(),
                    'contact' => ['firstName' => $contact->getFirstName()],
                ]]),
        );
    }
}
