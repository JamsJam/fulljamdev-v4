<?php

namespace App\Application\Reservation\Appointment\Service;

use App\Entity\Reservation\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;

final readonly class ApplyAppointmentTransitionService
{
    public function __construct(
        private Registry $workflows,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function apply(Appointment $appointment, string $transition): void
    {
        $workflow = $this->workflows->get($appointment, 'appointment');

        if (!$workflow->can($appointment, $transition)) {
            throw new \DomainException(sprintf('L’action « %s » n’est pas autorisée pour ce rendez-vous.', $transition));
        }

        $workflow->apply($appointment, $transition);
        $this->entityManager->flush();
    }
}
