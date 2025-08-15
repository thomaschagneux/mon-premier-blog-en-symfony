<?php

namespace App\DataFixtures;

use App\Entity\Picture;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->loadUsers(manager: $manager);
        $this->loadPictures(manager: $manager, users: $users);

        $manager->flush();
    }

    /**
     * @return array<User>
     */
    private function loadUsers(ObjectManager $manager): array
    {
        $faker = Factory::create('fr_FR');

        $users = [];

        $user1 = new User();
        $user1->setEmail('admin@example.com');
        $user1->setFirstName('Admin');
        $user1->setLastName('System');
        $user1->setPassword($this->passwordHasher->hashPassword(user: $user1, plainPassword: 'pass123'));
        $user1->setRoles(['ROLE_ADMIN']);
        $users[] = $user1;
        $manager->persist($user1);
        unset($user1);

        $user2 = new User();
        $user2->setEmail('user@example.com');
        $user2->setFirstName('John');
        $user2->setLastName('Doe');
        $user2->setPassword($this->passwordHasher->hashPassword(user: $user2, plainPassword: 'pass123'));
        $user2->setRoles(['ROLE_USER']);
        $users[] = $user2;
        $manager->persist($user2);
        unset($user2);

        for ($i = 0; $i < 5; ++$i) {
            $user = new User();
            $user->setEmail($faker->unique()->safeEmail);
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setPassword($this->passwordHasher->hashPassword(user: $user, plainPassword: 'pass123'));
            $users[] = $user;

            $manager->persist($user);
            unset($user);
        }

        return $users;
    }

    /**
     * @param array<User> $users
     */
    private function loadPictures(ObjectManager $manager, array $users): void
    {
        $faker = Factory::create('fr_FR');

        foreach ($users as $user) {
            $picture = new Picture();
            $picture->setFileName($faker->word.'.jpg');
            $picture->setPathName('/uploads/'.$faker->word.'.jpg');
            $picture->setMimeType('image/jpeg');
            $user->setPicture($picture);

            $manager->persist($picture);
        }
    }
}
