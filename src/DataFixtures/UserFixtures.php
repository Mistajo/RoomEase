<?php

namespace App\DataFixtures;

use App\Entity\MeetingRoom;
use App\Entity\User;

use DateTimeImmutable;

use Doctrine\Persistence\ObjectManager;;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // initialisation de l'objet Faker
        $faker = Faker\Factory::create('fr_FR');
        $users = array();
        for (
            $i = 0;
            $i < 15;
            $i++
        ) {
            $users[$i] = new User();
            $passwordHashed = $this->hasher->hashPassword($users[$i], "Joan@456789*");
            $users[$i]->setlastName($faker->lastName);
            $users[$i]->setfirstName($faker->firstName);
            $users[$i]->setemail($faker->email);
            $users[$i]->setPassword($passwordHashed);
            $users[$i]->setRoles(['ROLE_USER']);
            $users[$i]->setIsVerified(true);
            $users[$i]->setVerifiedAt(new DateTimeImmutable('now'));

            $manager->persist($users[$i]);
        }


        $superAdmin = $this->createSuperAdmin();


        $manager->persist($superAdmin);


        $manager->flush();
    }

    private function createSuperAdmin(): User
    {
        $user = new User();

        $passwordHashed = $this->hasher->hashPassword($user, "Joan@456789*");

        $user->setFirstName('Kenny');
        $user->setLastName('Omega');
        $user->setEmail('roomease8@gmail.com');
        $user->setRoles(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_USER']);
        $user->setIsVerified(true);
        $user->setPassword($passwordHashed);
        $user->setVerifiedAt(new DateTimeImmutable('now'));

        return $user;
    }
}
