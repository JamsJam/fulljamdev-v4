<?php

namespace App\DataFixtures;

use App\Entity\Reservation\Availability;
use App\Entity\Reservation\Planning;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AvailabilityFixtures extends Fixture implements DependentFixtureInterface
{
    private const AVAILABILITIES = [
        PlanningFixtures::DISCOVERY => [
            [1, '09:00', '12:00'],
            [1, '14:00', '17:30'],
            [2, '09:00', '12:00'],
            [3, '14:00', '18:00'],
            [4, '09:00', '12:00'],
        ],
        PlanningFixtures::PROJECT_FOLLOW_UP => [
            [1, '10:00', '13:00'],
            [2, '14:00', '18:00'],
            [3, '10:00', '13:00'],
            [4, '14:00', '18:00'],
            [5, '09:00', '12:30'],
        ],
        PlanningFixtures::TECHNICAL_WORKSHOP => [
            [2, '09:00', '12:30'],
            [2, '14:00', '18:00'],
            [3, '09:00', '12:30'],
            [4, '09:00', '12:30'],
            [4, '14:00', '18:00'],
            [5, '09:00', '12:00'],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::AVAILABILITIES as $planningReference => $slots) {
            $planning = $this->getReference($planningReference, Planning::class);

            foreach ($slots as [$dayOfWeek, $startHour, $endHour]) {
                $availability = (new Availability())
                    ->setPlanning($planning)
                    ->setDow($dayOfWeek)
                    ->setStartHour(new \DateTimeImmutable($startHour))
                    ->setEndHour(new \DateTimeImmutable($endHour));

                $manager->persist($availability);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [PlanningFixtures::class];
    }
}
