<?php
namespace App\Messenger\FileMessage;


class FileRemoveMessage 
{

    private string $filePah;

    private string $fileName;

    public function __construct(string $filePath, string $fileName) 
    {
        $this->filePah = $filePath;
        $this->fileName = $fileName;
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