<?php

namespace App\Tests\Application\Reservation\Appointment;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Application\Reservation\Appointment\Notification\RequestedAppointmentNotifier;
use App\Entity\Contact;
use App\Entity\Reservation\Appointment;
use App\Entity\Reservation\Planning;
use App\Service\ConfigurationService;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;

final class RequestedAppointmentNotifierTest extends TestCase
{
    public function testItNotifiesTheContactAndTheUserForARequestedAppointment(): void
    {
        $messages = [];
        $mailer = $this->createMock(MailerInterface::class);
        $mailer
            ->expects(self::exactly(2))
            ->method('send')
            ->willReturnCallback(static function (RawMessage $message) use (&$messages): void {
                $messages[] = $message;
            });

        (new RequestedAppointmentNotifier($mailer, $this->configuration()))->notify($this->appointment());

        self::assertContainsOnlyInstancesOf(TemplatedEmail::class, $messages);
        self::assertSame('contact@example.test', $messages[0]->getTo()[0]->getAddress());
        self::assertSame('admin@example.test', $messages[1]->getTo()[0]->getAddress());
        self::assertSame('emails/reservation/appointment_requested_contact.html.twig', $messages[0]->getHtmlTemplate());
        self::assertSame('emails/reservation/appointment_requested_user.html.twig', $messages[1]->getHtmlTemplate());
        self::assertIsArray($messages[0]->getContext()['appointment']);
    }

    public function testItIgnoresAnAppointmentWhichIsNotRequested(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::never())->method('send');
        $appointment = $this->appointment()->setStatus(AppointmentStatus::CONFIRMED);

        (new RequestedAppointmentNotifier($mailer, $this->configuration()))->notify($appointment);
    }

    private function appointment(): Appointment
    {
        $contact = (new Contact())
            ->setFirstName('Ada')
            ->setLastName('Lovelace')
            ->setEmail('contact@example.test')
            ->setPhoneNumber('0102030405');
        $planning = (new Planning())->setTitle('Appel découverte');

        return (new Appointment())
            ->setContact($contact)
            ->setPlanning($planning)
            ->setTitle('Présentation du projet')
            ->setTimezone('Europe/Paris')
            ->setStartAt(new \DateTimeImmutable('2026-08-20 10:00:00'))
            ->setEndAt(new \DateTimeImmutable('2026-08-20 10:30:00'))
            ->setStatus(AppointmentStatus::REQUESTED);
    }

    private function configuration(): ConfigurationService
    {
        return new ConfigurationService(__DIR__.'/../../../Fixtures/config/account.yaml');
    }
}
