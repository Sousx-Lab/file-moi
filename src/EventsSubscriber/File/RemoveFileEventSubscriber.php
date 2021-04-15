<?php

namespace App\EventsSubscriber\File;

use App\Entity\File\File;
use App\Events\File\FileRemoveEvent;
use App\Messenger\FileMessage\FileRemoveMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Vich\UploaderBundle\Event\Event;


class RemoveFileEventSubscriber implements EventSubscriberInterface
{

    private MessageBusInterface $messageBus;

    private EntityManagerInterface $em;

    public function __construct(MessageBusInterface $messageBus, EntityManagerInterface $em)
    {
        $this->messageBus = $messageBus;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            FileRemoveEvent::class => 'onPreRemoveFile',
        ];
    }

    public function onPreRemoveFile(FileRemoveEvent $event): void
    {
        $file = $event->getObject();
        if (null !== $file) {
            if (false !== $file->getUser()->isEmpty()) {
                $this->dispatch(FileRemoveMessage::class, $event);
            }
        }
        
    }

    public function dispatch(string $messageClass, FileRemoveEvent $event): void
    {
        $relativeFilePath = $event->getRelativePath();
        $uploadDestination = $event->getUploadDestination();
        $fileName = $event->getObject()->getFileName();

        $message = new $messageClass($uploadDestination . $relativeFilePath, $fileName);
        $this->messageBus->dispatch($message);
    }
}
