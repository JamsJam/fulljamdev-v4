<?php

namespace App\Application\Settings\Account\Notification;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

final readonly class AdminAccountCreatedNotifier
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function notify(User $user): void
    {
        $this->mailer->send(
            (new Email())
                ->from(new Address('contact@fulljamdev.fr', 'FullJamDev'))
                ->to(new Address(
                    $user->getEmail(),
                    trim(sprintf('%s %s', $user->getFirstName(), $user->getLastName())),
                ))
                ->subject('Votre compte administrateur FullJamDev a été créé')
                ->text(sprintf(
                    "Bonjour %s,\n\nVotre compte administrateur FullJamDev a été créé avec l’adresse %s.\n\nVous pouvez vous connecter avec cette adresse et le mot de passe choisi lors de la création du compte.\n\nFullJamDev",
                    $user->getFirstName(),
                    $user->getEmail(),
                )),
        );
    }
}
