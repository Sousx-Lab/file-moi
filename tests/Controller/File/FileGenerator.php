<?php
namespace App\Tests\Controller\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileGenerator {

    public function createFiles(int $num): array
    {
        $files = [];
        for ($i = 0; $i < $num; $i++) {
            file_put_contents(
                dirname(__DIR__) . "/File/testFile{$i}.txt",
                'This file is created automatically when run UploadedControllerTest'
            );
            $files[] = new UploadedFile(dirname(__DIR__) . "/File/testFile{$i}.txt", "testFile{$i}", 'text/plain');
        }
        return $files;
    }
}