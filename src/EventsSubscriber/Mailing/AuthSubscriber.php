<?php

namespace App\EventsSubscriber\Mailing;

use Symfony\Component\Messenger\MessageBusInterface;
use App\Events\Security\Password\PasswordResetTokenCreatedEvent;
use App\Messenger\NotificationMessage\UserNotificationMessage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthSubscriber implements EventSubscriberInterface
{
    const PASSWORD_RESET_MESSAGE = 'You recently made a request to change your password, 
    please follow the link below to recover your account';

    private MessageBusInterface $messageBus;
    private UrlGeneratorInterface $url;

    public function __construct(MessageBusInterface $messageBus, UrlGeneratorInterface $url)
    {
        $this->messageBus = $messageBus;
        $this->url = $url;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PasswordResetTokenCreatedEvent::class => 'onPasswordRequest',
        ];
    }

    public function onPasswordRequest(PasswordResetTokenCreatedEvent $event): void
    {
        $user = $event->getUser();
        $this->messageBus->dispatch(new UserNotificationMessage(
            $user->getId(),
            [
                'subject' => 'Password reset',
                'message' =>  self::PASSWORD_RESET_MESSAGE,
                'link'    =>  $this->url->generate('route_update_password', [
                    'id' => $user->getId(),
                    'token' => $event->getToken()->getToken()
                ])
            ]
        ));
    }
}
