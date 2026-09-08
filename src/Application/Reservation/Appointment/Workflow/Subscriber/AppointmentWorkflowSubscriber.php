<?php

namespace App\Application\Reservation\Appointment\Workflow\Subscriber;

use App\Entity\Reservation\Appointment;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;

final readonly class AppointmentWorkflowSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ClockInterface $clock,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.appointment.completed' => 'onCompleted',
        ];
    }

    public function onCompleted(CompletedEvent $event): void
    {
        $appointment = $event->getSubject();

        if ($appointment instanceof Appointment) {
            $appointment->setEditedAt(\DateTimeImmutable::createFromInterface($this->clock->now()));
        }
    }
}
