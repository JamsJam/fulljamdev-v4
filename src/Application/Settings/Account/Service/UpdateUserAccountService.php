<?php

namespace App\Application\Settings\Account\Service;

use App\Application\Settings\Account\Dto\UserAccountDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UpdateUserAccountService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function update(User $user, UserAccountDto $dto): void
    {
        if (!$this->passwordHasher->isPasswordValid($user, (string) $dto->currentPassword)) {
            throw new \DomainException('Le mot de passe actuel est incorrect.');
        }

        $normalizedEmail = mb_strtolower(trim($dto->email));
        $existingUser = $this->userRepository->findOneBy(['email' => $normalizedEmail]);
        if (null !== $existingUser && $existingUser->getId() !== $user->getId()) {
            throw new \DomainException('Un compte utilise déjà cette adresse email.');
        }

        $user
            ->setFirstName(trim($dto->firstName))
            ->setLastName(trim($dto->lastName))
            ->setEmail($normalizedEmail)
            ->setPhoneNumber(trim($dto->phoneNumber))
            ->setCompany(trim($dto->company))
            ->setJobTitle(trim($dto->jobTitle));

        if (null !== $dto->newPassword && '' !== $dto->newPassword) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $dto->newPassword));
        }

        $this->entityManager->flush();
    }
}
