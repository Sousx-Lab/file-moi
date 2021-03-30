<?php
namespace App\EventsSubscriber\File;

use App\Messenger\FileMessage\FileRemoveMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;


class RemoveFileEventSubscriber implements EventSubscriberInterface
{

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PRE_REMOVE => ['onPreRemoveFile'],
        ];
    }

    public function onPreRemoveFile(Event $event): void
    {
        $mapping = $event->getMapping();

        $mappingName = $mapping->getMappingName();
        
        if ('file_dl' === $mappingName) {
            
            $this->dispatch(FileRemoveMessage::class, $event);
        }
    }

    public function dispatch(string $messageClass, Event $event): void
    {
        $event->cancel();
        $mapping = $event->getMapping();

        $uploadPath = $mapping->getUploadDestination();
        $file = $event->getObject();
        $filePath = $mapping->getUploadDir($file);
        $fileName = $mapping->getFileName($file);

        $message = new $messageClass($uploadPath, $filePath, $fileName);
        $this->messageBus->dispatch($message);
    }

}
