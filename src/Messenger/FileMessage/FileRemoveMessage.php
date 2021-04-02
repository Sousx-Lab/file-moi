<?php
namespace App\Messenger\FileMessage;


class FileRemoveMessage 
{
    private string $uploadPath;

    private string $filePah;

    private string $fileName;

    public function __construct(string $uploadPath, string $filePath, string $fileName) 
    {
        $this->uploadPath = $uploadPath;
        $this->filePah = $filePath;
        $this->fileName = $fileName;
    }

    public function getUploadPath(): string
    {
        return $this->uploadPath;
    }

    public function getFilePath(): string
    {
        return $this->filePah;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}