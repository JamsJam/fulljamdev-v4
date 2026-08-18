<?php

namespace App\Command;

use App\Application\Reservation\Planner\Provider\Interface\PlannerProviderInterface;
use App\Application\Reservation\Planner\Service\PlanningSlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:planning:reload-slugs',
    description: 'Régénère les slugs publics de tous les plannings.',
)]
final class ReloadPlanningSlugsCommand extends Command
{
    public function __construct(
        private readonly PlannerProviderInterface $plannerProvider,
        private readonly PlanningSlugGenerator $slugGenerator,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $plannings = $this->plannerProvider->provide();

        foreach ($plannings as $planning) {
            $planning->setSlug($this->slugGenerator->generate((string) $planning->getTitle()));
        }

        $this->entityManager->flush();
        $io->success(sprintf('%d slug(s) de planning ont été régénérés.', count($plannings)));

        return Command::SUCCESS;
    }
}
