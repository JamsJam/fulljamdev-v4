<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'app:create-admin', description: 'Crée le compte administrateur de l’application.')]
final class CreateAdminUserCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = mb_strtolower(trim((string) $io->ask('Adresse email')));

        if (null !== $this->userRepository->findOneBy(['email' => $email])) {
            $io->error('Un compte utilise déjà cette adresse email.');

            return Command::FAILURE;
        }

        $firstName = trim((string) $io->ask('Prénom'));
        $lastName = trim((string) $io->ask('Nom'));
        $phoneNumber = trim((string) $io->ask('Téléphone'));
        $company = trim((string) $io->ask('Entreprise'));
        $jobTitle = trim((string) $io->ask('Poste'));
        $plainPassword = (string) $io->askHidden('Mot de passe (12 caractères minimum)');
        $passwordConfirmation = (string) $io->askHidden('Confirmez le mot de passe');

        if (mb_strlen($plainPassword) < 12) {
            $io->error('Le mot de passe doit contenir au moins 12 caractères.');

            return Command::INVALID;
        }

        if (!hash_equals($plainPassword, $passwordConfirmation)) {
            $io->error('Les mots de passe ne correspondent pas.');

            return Command::INVALID;
        }

        $user = (new User())
            ->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setPhoneNumber($phoneNumber)
            ->setCompany($company)
            ->setJobTitle($jobTitle)
            ->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        $violations = $this->validator->validate($user);
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $io->error($violation->getMessage());
            }

            return Command::INVALID;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $io->success(sprintf('Le compte administrateur %s a été créé.', $email));

        return Command::SUCCESS;
    }
}
