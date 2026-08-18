<?php

namespace App\Application\Reservation\Appointment\Workflow\Subscriber;

use App\Application\Reservation\Appointment\Meeting\GoogleCalendarMeetingCreator;
use App\Application\Reservation\Appointment\Reminder\Service\AppointmentReminderDispatcher;
use App\Entity\Reservation\Appointment;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;

final readonly class AppointmentWorkflowSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ClockInterface $clock,
        private GoogleCalendarMeetingCreator $meetingCreator,
        private AppointmentReminderDispatcher $reminderDispatcher,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.appointment.completed' => 'onCompleted',
            'workflow.appointment.completed.confirm' => 'onConfirmed',
        ];
    }

    public function onCompleted(CompletedEvent $event): void
    {
        $appointment = $event->getSubject();

        if ($appointment instanceof Appointment) {
            $appointment->setEditedAt(\DateTimeImmutable::createFromInterface($this->clock->now()));
        }
    }

    public function onConfirmed(CompletedEvent $event): void
    {
        $appointment = $event->getSubject();

        if (!$appointment instanceof Appointment) {
            return;
        }

        $meetingLink = $this->meetingCreator->create($appointment);
        if (null !== $meetingLink) {
            $appointment->setLink($meetingLink);
            $this->reminderDispatcher->dispatch($appointment);
        }
    }
}
