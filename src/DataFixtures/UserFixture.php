<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserFixture extends Fixture
{
    //Hasher le mdp
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    //-----------------------------------

    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setEmail('wassim@mail.com');
        $user->setUsername('Wassim');

        $user->setPassword($this->passwordHasher->hashPassword($user, '123'));

        $manager->persist($user);
        $manager->flush();
    }
}
