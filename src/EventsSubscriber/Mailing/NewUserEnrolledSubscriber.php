<?php
namespace App\EventsSubscriber\Mailing;

use Symfony\Component\Messenger\MessageBusInterface;
use App\Events\Security\Registration\NewUserEnrolledEvent;
use App\Messenger\NotificationMessage\UserNotificationMessage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewUserEnrolledSubscriber implements EventSubscriberInterface
{

    const NEW_USER_MESSAGE = "Your account has been created successfully, click on the link below to connect";

    private MessageBusInterface $messageBus;
    
    private UrlGeneratorInterface $url;

    public function __construct(MessageBusInterface $messageBus, UrlGeneratorInterface $url) {
        $this->messageBus = $messageBus;
        $this->url = $url;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NewUserEnrolledEvent::class => 'onUserEnrolled',
        ];
    }

    public function onUserEnrolled(NewUserEnrolledEvent $event)
    {
        $user = $event->getUser();
        $this->messageBus->dispatch(new UserNotificationMessage(
            $user->getId(),
            [
                'subject' => 'Password reset',
                'message' =>  self::NEW_USER_MESSAGE,
                'link'    =>  $this->url->generate('route_login', [])
            ]
        ));
    }


}