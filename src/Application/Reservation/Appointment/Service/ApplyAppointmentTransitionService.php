<?php

namespace App\Application\Reservation\Appointment\Service;

use App\Application\Reservation\Appointment\Meeting\MeetingLinkCreatorInterface;
use App\Application\Reservation\Appointment\Notification\AppointmentLifecycleNotifier;
use App\Application\Reservation\Appointment\Reminder\Service\AppointmentReminderDispatcher;
use App\Entity\Reservation\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;

final readonly class ApplyAppointmentTransitionService
{
    public function __construct(
        private Registry $workflows,
        private EntityManagerInterface $entityManager,
        private MeetingLinkCreatorInterface $meetingLinkCreator,
        private AppointmentReminderDispatcher $reminderDispatcher,
        private AppointmentLifecycleNotifier $lifecycleNotifier,
    ) {
    }

    public function apply(Appointment $appointment, string $transition): void
    {
        $workflow = $this->workflows->get($appointment, 'appointment');

        if (!$workflow->can($appointment, $transition)) {
            throw new \DomainException(sprintf('L’action « %s » n’est pas autorisée pour ce rendez-vous.', $transition));
        }

        if ('confirm' === $transition && null === $appointment->getLink()) {
            $appointment->setLink($this->meetingLinkCreator->create($appointment));
        }

        $workflow->apply($appointment, $transition);
        $this->entityManager->flush();

        $this->lifecycleNotifier->notify($appointment, $transition);

        if ('confirm' === $transition) {
            $this->reminderDispatcher->dispatch($appointment);
        }
    }
}
