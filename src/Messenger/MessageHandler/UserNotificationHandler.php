<?php

namespace App\Messenger\MessageHandler;

use App\Entity\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Messenger\Message\UserNotificationMessage;
use App\Services\Notifications\UserNotifierService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Messenger\MessageHandler\Exception\UserNotificationException;

class UserNotificationHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private UserNotifierService $notifierService;

    public function __construct(EntityManagerInterface $em, UserNotifierService $notifierService)
    {
        $this->em = $em;
        $this->notifierService = $notifierService;
    }

    public function __invoke(UserNotificationMessage $message)
    {
        $user = $this->em->find(User::class, $message->getUserId());
        $emailData = $message->getEmailData();
        if (null === $user) {
            throw new UserNotificationException();
        }
        $this->notifierService->notify($user, $emailData);
    }
}
