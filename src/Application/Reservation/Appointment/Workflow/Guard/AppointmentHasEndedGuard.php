<?php

namespace App\Application\Reservation\Appointment\Workflow\Guard;

use App\Entity\Reservation\Appointment;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

final readonly class AppointmentHasEndedGuard implements EventSubscriberInterface
{
    public function __construct(private ClockInterface $clock)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.appointment.guard.mark_held' => 'guard',
            'workflow.appointment.guard.no_show' => 'guard',
        ];
    }

    public function guard(GuardEvent $event): void
    {
        $appointment = $event->getSubject();

        if (!$appointment instanceof Appointment) {
            return;
        }

        $endAt = $appointment->getEndAt();

        if (null === $endAt || $endAt > $this->clock->now()) {
            $event->setBlocked(true, 'Le rendez-vous doit être terminé avant d’appliquer cette action.');
        }
    }
}
