<?php
namespace App\Entity\File\Data;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class FileData {


    private ?array $files = [];

    public function getFiles(): array
    {
        return $this->files;
    }
    
    public function setFiles(array $file): self
    {
        array_merge($this->files, $file);
        return $this;    
    }

    /**
     * @param File $file
     * @return void
     */
    public function addFiles(File $file)
    {
        $this->files[] = $file;
    }
}