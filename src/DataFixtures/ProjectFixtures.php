<?php

namespace App\DataFixtures;

use App\Entity\Project\Project;
use App\Entity\Project\Technology;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class ProjectFixtures extends Fixture implements FixtureGroupInterface
{
    private const TECHNOLOGIES = [
        'Symfony', 'PHP', 'Doctrine', 'Twig', 'Stimulus',
        'Turbo', 'JavaScript', 'TypeScript', 'Sass', 'Docker',
        'MySQL', 'PostgreSQL', 'Redis', 'API Platform', 'GitHub Actions',
    ];

    private const PROJECTS = [
        'Plateforme de réservation en ligne',
        'Portfolio créatif nouvelle génération',
        'Boutique de produits artisanaux',
        'Tableau de bord commercial',
        'Application de gestion de planning',
        'Catalogue immobilier interactif',
        'Espace client pour cabinet conseil',
        'Plateforme de formation numérique',
        'Site éditorial consacré au design',
        'Application de suivi de projets',
        'Portail associatif collaboratif',
        'Outil de gestion documentaire',
        'Marketplace de créateurs locaux',
        'Application de suivi sportif',
        'Plateforme de mise en relation',
        'Intranet pour équipe distribuée',
        'Configurateur de services en ligne',
        'Observatoire de données publiques',
        'Application événementielle',
        'Site vitrine pour studio indépendant',
    ];

    public function load(ObjectManager $manager): void
    {
        $technologyRepository = $manager->getRepository(Technology::class);
        $projectRepository = $manager->getRepository(Project::class);
        $technologies = [];

        foreach (self::TECHNOLOGIES as $name) {
            $technology = $technologyRepository->findOneBy(['name' => $name]) ?? (new Technology())->setName($name);
            $manager->persist($technology);
            $technologies[] = $technology;
        }

        foreach (self::PROJECTS as $index => $title) {
            $slug = sprintf('projet-demonstration-%02d', $index + 1);
            $project = $projectRepository->findOneBy(['slug' => $slug]) ?? new Project();

            foreach ($project->getTechnologies()->toArray() as $technology) {
                $project->removeTechnology($technology);
            }
            for ($offset = 0; $offset < 5; ++$offset) {
                $project->addTechnology($technologies[($index * 3 + $offset) % count($technologies)]);
            }

            $published = $index < 12;
            $project
                ->setTitle($title)
                ->setSlug($slug)
                ->setExcerpt('Une réalisation web conçue autour des besoins utilisateurs, de la performance et de la maintenabilité.')
                ->setContent(sprintf('<h2>%s</h2><p>Ce projet de démonstration présente une solution sur mesure, développée avec une architecture claire et des composants réutilisables.</p><p>Le travail couvre la conception, le développement, les tests et la mise en production.</p>', htmlspecialchars($title, ENT_QUOTES)))
                ->setWebsiteUrl(sprintf('https://projet-%02d.example.test', $index + 1))
                ->setRepositoryUrl(sprintf('https://github.com/example/projet-%02d', $index + 1))
                ->setIsFeatured($index < 6)
                ->setStatus($published ? 'published' : 'draft')
                ->setPublishedAt($published ? new \DateTimeImmutable(sprintf('-%d days', ($index + 1) * 7)) : null);

            $manager->persist($project);
            $this->addReference(sprintf('project.%d', $index), $project);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['project'];
    }
}
