<?php

namespace App\DataFixtures;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Entity\Contact;
use App\Entity\Reservation\Appointment;
use App\Entity\Reservation\Planning;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppointmentFixtures extends Fixture implements DependentFixtureInterface
{
    private const APPOINTMENT_COUNTS = [
        PlanningFixtures::DISCOVERY => 15,
        PlanningFixtures::PROJECT_FOLLOW_UP => 27,
        PlanningFixtures::TECHNICAL_WORKSHOP => 40,
    ];

    public function load(ObjectManager $manager): void
    {
        $periodStart = new \DateTimeImmutable('first day of last month 00:00:00');
        $periodEnd = new \DateTimeImmutable('last day of next month 23:59:59');

        $planningIndex = 0;

        foreach (self::APPOINTMENT_COUNTS as $planningReference => $appointmentCount) {
            $planning = $this->getReference($planningReference, Planning::class);
            $this->createAppointments(
                $manager,
                $planning,
                $planningIndex,
                $appointmentCount,
                $periodStart,
                $periodEnd,
            );
            ++$planningIndex;
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [PlanningFixtures::class, ContactFixtures::class];
    }

    private function createAppointments(
        ObjectManager $manager,
        Planning $planning,
        int $planningIndex,
        int $appointmentCount,
        \DateTimeImmutable $periodStart,
        \DateTimeImmutable $periodEnd,
    ): void {
        $periodInSeconds = $periodEnd->getTimestamp() - $periodStart->getTimestamp();
        $now = new \DateTimeImmutable();

        for ($index = 0; $index < $appointmentCount; ++$index) {
            $offset = (int) floor($index * $periodInSeconds / ($appointmentCount - 1));
            $hour = 9 + (($index + $planningIndex) % 8);
            $minute = 0 === $index % 2 ? 0 : 30;
            $startAt = $periodStart
                ->modify(sprintf('+%d seconds', $offset))
                ->setTime($hour, $minute);
            $endAt = $startAt->modify(sprintf('+%d minutes', $planning->getDuration()));
            $createdAt = $startAt->modify(sprintf('-%d days', 2 + ($index % 12)));

            if ($createdAt > $now) {
                $createdAt = $now->modify(sprintf('-%d days', $index % 12));
            }

            $appointment = (new Appointment())
                ->setPlanning($planning)
                ->setContact($this->getReference(ContactFixtures::appointmentReference($index + $planningIndex), Contact::class))
                ->setCreatedAt($createdAt)
                ->setEditedAt($createdAt)
                ->setStartAt($startAt)
                ->setEndAt($endAt)
                ->setTimezone('Europe/Paris')
                ->setTitle(sprintf('%s #%02d', $planning->getTitle(), $index + 1))
                ->setDescription('Rendez-vous de démonstration généré par les fixtures.')
                ->setTranscription($startAt < $now ? 'Compte rendu de démonstration.' : null)
                ->setLink(sprintf('https://meet.example.test/planning-%d/rendez-vous-%d', $planningIndex + 1, $index + 1))
                ->setStatus($startAt < $now ? AppointmentStatus::OCCURRED : AppointmentStatus::CONFIRMED);

            $manager->persist($appointment);
            $this->addReference(self::reference($planningIndex, $index), $appointment);
        }
    }

    public static function reference(int $planningIndex, int $appointmentIndex): string
    {
        return sprintf('appointment.%d.%d', $planningIndex, $appointmentIndex);
    }

    /**
     * @return iterable<array{int, int}>
     */
    public static function references(): iterable
    {
        foreach (array_values(self::APPOINTMENT_COUNTS) as $planningIndex => $appointmentCount) {
            for ($appointmentIndex = 0; $appointmentIndex < $appointmentCount; ++$appointmentIndex) {
                yield [$planningIndex, $appointmentIndex];
            }
        }
    }
}
