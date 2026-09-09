<?php

namespace App\DataFixtures;

use App\Application\Reservation\Planner\Service\PlanningSlugGenerator;
use App\Entity\Reservation\Planning;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PlanningFixtures extends Fixture
{
    public function __construct(private readonly PlanningSlugGenerator $slugGenerator)
    {
    }

    public const DISCOVERY = 'planning.discovery';
    public const PROJECT_FOLLOW_UP = 'planning.project_follow_up';
    public const TECHNICAL_WORKSHOP = 'planning.technical_workshop';

    private const PLANNINGS = [
        self::DISCOVERY => [
            'title' => 'Appels découverte',
            'description' => 'Premier échange pour découvrir le projet et définir les besoins.',
            'duration' => 30,
            'gap' => 10,
            'color' => '#6750A4',
        ],
        self::PROJECT_FOLLOW_UP => [
            'title' => 'Suivi de projet',
            'description' => 'Points réguliers de suivi, validation et priorisation des prochaines étapes.',
            'duration' => 45,
            'gap' => 15,
            'color' => '#006C4C',
        ],
        self::TECHNICAL_WORKSHOP => [
            'title' => 'Ateliers techniques',
            'description' => 'Sessions de travail consacrées à la conception et aux choix techniques.',
            'duration' => 60,
            'gap' => 20,
            'color' => '#B3261E',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::PLANNINGS as $reference => $data) {
            $planning = (new Planning())
                ->setTitle($data['title'])
                ->setSlug($this->slugGenerator->generate($data['title']))
                ->setDescription($data['description'])
                ->setDuration($data['duration'])
                ->setGap($data['gap'])
                ->setColor($data['color'])
                ->setIsActive(true);

            $manager->persist($planning);
            $this->addReference($reference, $planning);
        }

        $manager->flush();
    }
}
