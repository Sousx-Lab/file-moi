<?php

namespace App\Services\FileServices;

class FileRemoverService
{
    public function removeFile(string $uploadPath, string $filePath, string $fileName, bool $rmFileDir = true): void
    {
        $dir = $uploadPath . DIRECTORY_SEPARATOR . $filePath;
        $file = $dir . DIRECTORY_SEPARATOR . $fileName;

        if (!file_exists($dir)) {
            return;
        }
        if (file_exists($file)) {
            unlink($file);
        }

        if ($rmFileDir) {
            $this->removeDirFile($dir);
        }
    }

    public function removeDirFile(string $pathDir): void
    {
        rmdir($pathDir);
    }
}
