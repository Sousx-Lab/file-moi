<?php

namespace App\Services\FileServices;

use App\Entity\File\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UploadFileService
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function UploadFile(array $uploadedFiles, ?UserInterface $user): array
    {
        $files = [];
        foreach ($uploadedFiles as $upFile) {
            $file = new File();
            $file->setUploadedFile($upFile);
            if (null !== $user) {
                $file->addUser($user);
            }
            $files[] = $file;
            $this->em->persist($file);
        }
        $this->em->flush();
        return $files;
    }
}
