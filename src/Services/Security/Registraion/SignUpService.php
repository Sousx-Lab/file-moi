<?php
namespace App\Services\Security\Registraion;

use App\Entity\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use App\Events\Security\Registration\NewUserEnrolledEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignUpService 
{
    private EntityManagerInterface $em;

    private UserPasswordHasherInterface $encoder;

    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, 
        UserPasswordHasherInterface $encoder,
        EventDispatcherInterface $dispatcher) {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->dispatcher = $dispatcher;
    }

    public function registerUser(User $user)
    {
        $password = $this->encoder->hashPassword($user, $user->getPassword());
        $user->setPassword($password);
        $this->em->persist($user);
        $this->em->flush();

        $this->dispatcher->dispatch(new NewUserEnrolledEvent($user));
    }
}