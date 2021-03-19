<?php
namespace App\Entity\File\Data;

use Symfony\Component\HttpFoundation\File\File;

class FileData {

    private array $files;

    public function getFiles(): array
    {
        return $this->files;
    }

    public function addFiles(File $file)
    {
        $this->files[] = $file;
    }
}