<?php

namespace App\Service\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function create(User $user): void
    {
        $password = $user->getPassword();

        if (null !== $password) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function update(User $user, ?string $password = null): void
    {
        if (null !== $password && '' !== $password) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        }

        $this->entityManager->flush();
    }
}
