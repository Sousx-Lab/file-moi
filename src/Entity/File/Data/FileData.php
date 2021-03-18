<?php
namespace App\Entity\File\Data;

use Symfony\Component\HttpFoundation\File\File;

class FileData {
    
    private File $file;

    public function getFile(File $file): File
    {
        return $this->file = $file;
    }

    public function setFile(File $file): self
    {
        $this->file = $file;
        return $this;
    }
}