<?php

namespace App\Messenger\FileMessage;

use App\Messenger\FileMessage\FileRemoveMessage;
use App\Services\FileServices\FileRemoverService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FileRemoveMessageHandler implements MessageHandlerInterface
{

    private FileRemoverService $service;

    public function __construct(FileRemoverService $service)
    {
        $this->service = $service;
    }

    public function __invoke(FileRemoveMessage $message)
    {
        $this->service->removeFile(
            $message->getUploadPath(),
            $message->getFilePath(),
            $message->getFileName()
        );
    }
}
