<?php

namespace App\Form\Factory;

use App\Application\Reservation\Availability\Dto\AvailabilityDto;
use App\Application\Reservation\Availability\Dto\PlanningAvailabilitiesDto;
use App\Entity\Reservation\Planning;
use App\Form\PlanningAvailabilitiesType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class PlanningAvailabilitiesFormFactory
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function create(Planning $planning): FormInterface
    {
        $availabilitiesByDay = [];
        foreach ($planning->getAvailabilities() as $availability) {
            $availabilitiesByDay[$availability->getDow()] = $availability;
        }

        $availabilities = [];
        for ($day = 1; $day <= 7; ++$day) {
            $availability = $availabilitiesByDay[$day] ?? null;
            $availabilities[] = new AvailabilityDto(
                dow: $day,
                startHour: $availability?->getStartHour(),
                endHour: $availability?->getEndHour(),
                isAvailable: null !== $availability,
            );
        }

        return $this->formFactory->create(
            PlanningAvailabilitiesType::class,
            new PlanningAvailabilitiesDto($availabilities),
            [
                'action' => $this->urlGenerator->generate('app_dashboard_reservation_planning_availabilities', [
                    'id' => $planning->getId(),
                ]),
            ],
        );
    }
}
