<?php
namespace App\Services\Security\Registraion;

use App\Entity\Auth\User;
use App\Events\Security\Registration\NewUserEnrolledEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SignUpService 
{
    private EntityManagerInterface $em;

    private UserPasswordEncoderInterface $encoder;

    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, 
        UserPasswordEncoderInterface $encoder,
        EventDispatcherInterface $dispatcher) {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->dispatcher = $dispatcher;
    }

    public function registerUser(User $user)
    {
        $password = $this->encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        $this->em->persist($user);
        $this->em->flush();

        $this->dispatcher->dispatch(new NewUserEnrolledEvent($user));
    }
}