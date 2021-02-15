<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder) {
        $this->passwordEncoder = $userPasswordEncoder;
    }
    
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i <= 5; $i++)
        {
            $user = new User();
                $user->setEmail('john'. $i .'@email.fr' );
                $user->setPassword($this->passwordEncoder->encodePassword($user, "000". $i));
                $user->setFirstName('John' . $i);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
