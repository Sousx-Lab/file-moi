<?php

namespace App\Services\FileServices;

use App\Entity\Auth\User;
use App\Entity\File\File;
use App\Events\File\FileRemoveEvent;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\FileServices\Helper\FileMapping;
use Psr\EventDispatcher\EventDispatcherInterface;
use App\Services\FileServices\Exception\FileNotFoundException;
use App\Services\FileServices\Exception\UserFileListException;

final class FileManagerService
{
    private EntityManagerInterface $em;

    private EventDispatcherInterface $dispatcher;

    private FileMapping $fileMapping;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher, FileMapping $fileMapping)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->fileMapping = $fileMapping;
    }

    public function unsetFile(User $user, string $fileId): void
    {
        $file =  $this->em->getRepository(File::class)->findOneBy(['id' => $fileId]);

        if (null === $file) {

            throw new FileNotFoundException();
        }

        if (false === $user->getFiles()->exists(function ($key, $value) use ($file) {

            return $value->getId() === $file->getId();
        })) {
            throw new UserFileListException();
        }
        
        $mapping = $this->fileMapping->getMapping($file);
        
        $user->removeFile($file);
        $this->em->persist($user);
        $this->em->flush();

        $this->dispatcher->dispatch(new FileRemoveEvent($file, $mapping));
    }
}
