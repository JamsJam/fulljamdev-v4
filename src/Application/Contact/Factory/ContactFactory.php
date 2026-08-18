<?php

namespace App\Application\Contact\Factory;

use App\Application\Reservation\Appointment\Dto\PublicAppointmentDto;
use App\Entity\Contact;

final class ContactFactory
{
    public function createFromAppointment(PublicAppointmentDto $dto): Contact
    {
        $now = new \DateTimeImmutable();

        return (new Contact())
            ->setFirstName((string) $dto->contact->firstName)
            ->setLastName((string) $dto->contact->lastName)
            ->setEmail((string) $dto->contact->email)
            ->setPhoneNumber((string) $dto->contact->phoneNumber)
            ->setSource('Réservation publique')
            ->setCreatedAt($now)
            ->setUpdatedAt($now);
    }
}
