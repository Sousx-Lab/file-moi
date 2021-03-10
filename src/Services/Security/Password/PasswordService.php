<?php

namespace App\Services\Security\Password;

use App\Entity\Auth\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Auth\Password\PasswordResetToken;
use App\Repository\PasswordResetTokenRepository;
use App\Entity\Auth\Exception\UserNotFoundException;
use App\Services\Security\Password\TokenGeneratorService;
use App\Entity\Auth\Password\Data\PasswordResetRequestData;
use App\Entity\Auth\Password\Exception\OngoingPasswordResetException;
use App\Events\Auth\Password\PasswordResetTokenCreatedEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class PasswordService
{
    const EXPIRE_IN = 30;

    private UserRepository $userRepository;
    private PasswordResetTokenRepository $tokenRepository;
    private EntityManagerInterface $em;
    private TokenGeneratorService $generator;
    private UserPasswordEncoderInterface $encoder;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        UserRepository $userRepository,
        PasswordResetTokenRepository $tokenRepository,
        EntityManagerInterface $em,
        TokenGeneratorService $generator,
        UserPasswordEncoderInterface $encoder,
        EventDispatcherInterface $dispatcher
    ) {
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
        $this->em = $em;
        $this->generator = $generator;
        $this->encoder = $encoder;
        $this->dispatcher = $dispatcher;
    }

    public function resetPassword(PasswordResetRequestData $data)
    {
        $user = $this->userRepository->findOneBy(['email' => $data->getEmail()]);
        if (null === $user) {
            throw new UserNotFoundException();
        }

        $token = $this->tokenRepository->findOneBy(['user' => $user]);
        if (null !== $token && !$this->isExpired($token)) {
            throw new OngoingPasswordResetException();
        }

        if (null === $token) {
            $token = new PasswordResetToken();
            $this->em->persist($token);
        }

        $token->setUser($user)
            ->setCreatedAt(new \DateTime())
            ->setToken($this->generator->generate());
        $this->em->flush();
        $this->dispatcher->dispatch(new PasswordResetTokenCreatedEvent($token));
    }

    public function isExpired(PasswordResetToken $token): bool
    {
        $expirationDate = new \DateTime('-' . self::EXPIRE_IN .' minutes');
        return $token->getCreatedAt() < $expirationDate;
    }

    public function updatePassword(string $password, PasswordResetToken $token): void
    {
        /**@var User */
        $user = $token->getUser();
        $user->setConfirmationToken(null);
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $this->em->remove($token);
        $this->em->flush();
    }
}
