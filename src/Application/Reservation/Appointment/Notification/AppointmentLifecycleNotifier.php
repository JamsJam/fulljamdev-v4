<?php

namespace App\Application\Reservation\Appointment\Notification;

use App\Entity\Reservation\Appointment;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final readonly class AppointmentLifecycleNotifier
{
    public function __construct(
        private MailerInterface $mailer,
        private UserRepository $userRepository,
    ) {
    }

    public function notify(Appointment $appointment, string $transition): void
    {
        $notification = $this->notificationFor($transition);
        $contact = $appointment->getContact();
        $account = $this->userRepository->findAdministrator();

        if (null === $notification || null === $contact?->getEmail() || null === $account?->getEmail()) {
            return;
        }

        $sender = new Address(
            $account->getEmail(),
            trim(sprintf('%s %s', $account->getFirstName(), $account->getLastName())),
        );

        $this->mailer->send(
            (new TemplatedEmail())
                ->from($sender)
                ->to(new Address(
                    $contact->getEmail(),
                    trim(sprintf('%s %s', $contact->getFirstName(), $contact->getLastName())),
                ))
                ->replyTo($sender)
                ->subject($notification['subject'])
                ->htmlTemplate('emails/reservation/appointment_lifecycle.html.twig')
                ->textTemplate('emails/reservation/appointment_lifecycle.txt.twig')
                ->context([
                    'appointment' => [
                        'title' => $appointment->getTitle(),
                        'timezone' => $appointment->getTimezone(),
                        'startAt' => $appointment->getStartAt(),
                        'endAt' => $appointment->getEndAt(),
                        'link' => $appointment->getLink(),
                        'planningSlug' => $appointment->getPlanning()?->getSlug(),
                        'contactFirstName' => $contact->getFirstName(),
                        'summary' => $appointment->getSummary()?->getContent(),
                    ],
                    'notification' => $notification,
                ]),
        );
    }

    /** @return array{subject: string, heading: string, message: string, action: string|null}|null */
    private function notificationFor(string $transition): ?array
    {
        return match ($transition) {
            'confirm' => [
                'subject' => 'Votre rendez-vous est confirmé',
                'heading' => 'Rendez-vous confirmé',
                'message' => 'Votre rendez-vous est confirmé. Vous trouverez le lien de connexion ci-dessous.',
                'action' => 'join',
            ],
            'reject' => [
                'subject' => 'Votre demande de rendez-vous ne peut pas être acceptée',
                'heading' => 'Demande de rendez-vous refusée',
                'message' => 'Le créneau demandé ne peut malheureusement pas être accepté.',
                'action' => 'book',
            ],
            'cancel' => [
                'subject' => 'Votre rendez-vous est annulé',
                'heading' => 'Rendez-vous annulé',
                'message' => 'Votre rendez-vous a été annulé. Vous pouvez choisir un nouveau créneau.',
                'action' => 'book',
            ],
            'no_show' => [
                'subject' => 'Planifions un nouveau rendez-vous',
                'heading' => 'Nous vous avons attendu',
                'message' => 'Vous n’avez pas pu assister au rendez-vous. Vous pouvez choisir une nouvelle date.',
                'action' => 'book',
            ],
            'complete' => [
                'subject' => 'Compte rendu de notre rendez-vous',
                'heading' => 'Compte rendu du rendez-vous',
                'message' => 'Merci pour notre échange. Voici le compte rendu du rendez-vous.',
                'action' => null,
            ],
            default => null,
        };
    }
}
