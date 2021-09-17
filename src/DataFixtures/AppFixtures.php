<?php

namespace App\DataFixtures;

use App\Entity\Auth\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $userPasswordEncoder) {
        $this->passwordEncoder = $userPasswordEncoder;
    }
    
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i <= 5; $i++)
        {
            $user = new User();
                $user->setEmail('john'. $i .'@email.fr' );
                $user->setPassword($this->passwordEncoder->hashPassword($user, "000". $i));
                $user->setFirstName('John' . $i);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
