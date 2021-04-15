<?php

namespace App\Services\FileServices;


class FileRemoverService
{
    public function removeFile(string $filePath, string $fileName, bool $rmFileDir = true): void
    {
        if (!file_exists($filePath)) {
            return;
        } else {
            unlink($filePath);
        }

        if ($rmFileDir) {
            $this->removeDir(str_replace(DIRECTORY_SEPARATOR . $fileName, "", $filePath));
        }
    }

    public function removeDir(string $pathDir): void
    {
        rmdir($pathDir);
    }
}
