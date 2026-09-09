<?php

namespace App\Application\Reservation\Appointment\Reminder\Notification;

use App\Application\Reservation\Appointment\Reminder\Enum\AppointmentReminderType;
use App\Entity\Reservation\Appointment;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final readonly class AppointmentReminderNotifier
{
    public function __construct(
        private MailerInterface $mailer,
        private UserRepository $userRepository,
    ) {
    }

    public function notify(Appointment $appointment, AppointmentReminderType $type): void
    {
        $contact = $appointment->getContact();
        $account = $this->userRepository->findAdministrator();
        $accountEmail = $account?->getEmail();

        if (null === $contact || null === $contact->getEmail() || null === $accountEmail || '' === $accountEmail) {
            return;
        }

        $sender = new Address($accountEmail, trim(sprintf(
            '%s %s',
            $account->getFirstName(),
            $account->getLastName(),
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
