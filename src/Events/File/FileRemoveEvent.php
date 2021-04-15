<?php

namespace App\Events\File;

use App\Entity\File\File;
use Symfony\Component\Finder\Finder;

final class FileRemoveEvent
{
    private File $file;

    private array $fileMapping;

    public function __construct(File $file, array $fileMapping)
    {
        $this->file = $file;
        $this->fileMapping = $fileMapping;
    }

    public function getObject(): File
    {
        return $this->file;
    }

    public function getFileName(): string
    {
        return $this->file->getFileName();
    }

    public function getMappingName(): string
    {
        return $this->fileMapping['mapping'];
    }

    public function getUploadDestination(): string
    {
        return $this->fileMapping['upload_destination'];
    }

    public function getRelativePath(): string
    {
        return str_replace($this->fileMapping['uri_prefix'], '', $this->fileMapping['relative_path']);
    }
}
