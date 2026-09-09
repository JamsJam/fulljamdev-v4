<?php

namespace App\Tests\Reservation\Integration\Scenario;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Application\Reservation\Appointment\Meeting\MeetingLinkCreatorInterface;
use App\Application\Reservation\Appointment\Notification\AppointmentLifecycleNotifier;
use App\Application\Reservation\Appointment\Notification\RequestedAppointmentNotifier;
use App\Application\Reservation\Appointment\Reminder\Service\AppointmentReminderDispatcher;
use App\Application\Reservation\Appointment\Service\ApplyAppointmentTransitionService;
use App\Entity\Contact;
use App\Entity\Reservation\Appointment;
use App\Entity\Reservation\Planning;
use App\Entity\Reservation\Summary;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Workflow\Registry;

final class AppointmentLifecycleScenarioTest extends KernelTestCase
{
    /** @var list<TemplatedEmail> */
    private array $emails;

    private MailerInterface $mailer;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->emails = [];
        $this->mailer = $this->createStub(MailerInterface::class);
        $this->mailer
            ->method('send')
            ->willReturnCallback(function (RawMessage $message): void {
                self::assertInstanceOf(TemplatedEmail::class, $message);
                $this->emails[] = $message;
            });
    }

    public function testContactBooksUserConfirmsMeetingOccursAndSummaryIsSent(): void
    {
        $appointment = $this->contactBooksAnAppointment();

        $this->transitionService()->apply($appointment, 'confirm');
        self::assertSame(AppointmentStatus::CONFIRMED, $appointment->getStatus());
        self::assertSame('https://meet.google.com/test-meeting', $appointment->getLink());

        $this->transitionService()->apply($appointment, 'mark_held');
        self::assertSame(AppointmentStatus::OCCURRED, $appointment->getStatus());

        $appointment->setSummary(
            (new Summary())
                ->setAppointment($appointment)
                ->setContent('Le client valide la proposition et transmettra les contenus.'),
        );
        $this->transitionService()->apply($appointment, 'complete');

        self::assertSame(AppointmentStatus::COMPLETE, $appointment->getStatus());
        self::assertSame([
            'Votre demande de rendez-vous — Appel découverte',
            'Nouvelle demande de rendez-vous — Présentation du projet',
            'Votre rendez-vous est confirmé',
            'Compte rendu de notre rendez-vous',
        ], $this->subjects());
        self::assertSame(
            'Le client valide la proposition et transmettra les contenus.',
            $this->emails[3]->getContext()['appointment']['summary'],
        );
    }

    public function testContactBooksUserConfirmsAndNoShowEmailInvitesContactToBookAgain(): void
    {
        $appointment = $this->contactBooksAnAppointment();
        $service = $this->transitionService();

        $service->apply($appointment, 'confirm');
        $service->apply($appointment, 'no_show');

        self::assertSame(AppointmentStatus::NO_SHOW, $appointment->getStatus());
        self::assertSame('Planifions un nouveau rendez-vous', $this->emails[3]->getSubject());
        self::assertSame('book', $this->emails[3]->getContext()['notification']['action']);
        self::assertSame('appel-decouverte', $this->emails[3]->getContext()['appointment']['planningSlug']);
    }

    public function testContactBooksAndUserRejectsTheRequest(): void
    {
        $appointment = $this->contactBooksAnAppointment();

        $this->transitionService()->apply($appointment, 'reject');

        self::assertSame(AppointmentStatus::REJECTED, $appointment->getStatus());
        self::assertSame('Votre demande de rendez-vous ne peut pas être acceptée', $this->emails[2]->getSubject());
        self::assertSame('book', $this->emails[2]->getContext()['notification']['action']);
    }

    public function testConfirmedAppointmentIsCancelledAndContactCanBookAgain(): void
    {
        $appointment = $this->contactBooksAnAppointment();
        $service = $this->transitionService();

        $service->apply($appointment, 'confirm');
        $service->apply($appointment, 'cancel');

        self::assertSame(AppointmentStatus::CANCELLED, $appointment->getStatus());
        self::assertSame('Votre rendez-vous est annulé', $this->emails[3]->getSubject());
        self::assertSame('book', $this->emails[3]->getContext()['notification']['action']);
    }

    private function contactBooksAnAppointment(): Appointment
    {
        $contact = (new Contact())
            ->setFirstName('Ada')
            ->setLastName('Lovelace')
            ->setEmail('contact@example.test')
            ->setPhoneNumber('0102030405');
        $planning = (new Planning())
            ->setTitle('Appel découverte')
            ->setSlug('appel-decouverte');
        $appointment = (new Appointment())
            ->setContact($contact)
            ->setPlanning($planning)
            ->setTitle('Présentation du projet')
            ->setTimezone('Europe/Paris')
            ->setStartAt(new \DateTimeImmutable('2026-08-20 10:00:00 Europe/Paris'))
            ->setEndAt(new \DateTimeImmutable('2026-08-20 10:30:00 Europe/Paris'))
            ->setStatus(AppointmentStatus::REQUESTED);

        (new RequestedAppointmentNotifier($this->mailer, $this->userRepository()))->notify($appointment);

        return $appointment;
    }

    private function transitionService(): ApplyAppointmentTransitionService
    {
        $entityManager = $this->createStub(EntityManagerInterface::class);
        $messageBus = $this->createStub(MessageBusInterface::class);
        $messageBus->method('dispatch')->willReturnCallback(
            static fn (object $message): Envelope => new Envelope($message),
        );
        $meetingCreator = new class implements MeetingLinkCreatorInterface {
            public function create(Appointment $appointment): string
            {
                return 'https://meet.google.com/test-meeting';
            }
        };

        return new ApplyAppointmentTransitionService(
            static::getContainer()->get(Registry::class),
            $entityManager,
            $meetingCreator,
            new AppointmentReminderDispatcher($messageBus, new MockClock('2026-08-20 12:00:00 Europe/Paris')),
            new AppointmentLifecycleNotifier($this->mailer, $this->userRepository()),
        );
    }

    /** @return list<string|null> */
    private function subjects(): array
    {
        return array_map(
            static fn (TemplatedEmail $email): ?string => $email->getSubject(),
            $this->emails,
        );
    }

    private function userRepository(): UserRepository
    {
        $user = (new User())
            ->setEmail('admin@example.test')
            ->setFirstName('Grace')
            ->setLastName('Hopper')
            ->setPhoneNumber('0102030405')
            ->setCompany('Fulljam Dev')
            ->setJobTitle('Administratrice')
            ->setPassword('hash')
            ->setRoles(['ROLE_ADMIN']);
        $repository = $this->createStub(UserRepository::class);
        $repository->method('findAdministrator')->willReturn($user);

        return $repository;
    }
}
