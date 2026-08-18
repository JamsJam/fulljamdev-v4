<?php

namespace App\Application\Reservation\Appointment\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class PublicAppointmentDto
{
    #[Assert\Valid]
    public AppointmentDateDto $date;

    #[Assert\Valid]
    public AppointmentTimeDto $time;

    #[Assert\Valid]
    public AppointmentContactDto $contact;

    public function __construct()
    {
        $this->date = new AppointmentDateDto();
        $this->time = new AppointmentTimeDto();
        $this->contact = new AppointmentContactDto();
    }

    #[Assert\Callback]
    public function validateChronology(ExecutionContextInterface $context): void
    {
        if (null !== $this->time->value && null === $this->date->value) {
            $context->buildViolation('Sélectionnez d’abord une date.')
                ->atPath('time.value')
                ->addViolation();
        }
    }
}
