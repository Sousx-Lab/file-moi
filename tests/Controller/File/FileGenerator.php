<?php
namespace App\Tests\Controller\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FileGenerator {

    public function createFiles(int $num): array
    {
        $files = [];
        for ($i = 0; $i < $num; $i++) {
            file_put_contents(
                dirname(__DIR__) . "/File/files/testFile{$i}.txt",
                'This file is created automatically when run UploadedControllerTest'
            );
            $files[] = new UploadedFile(dirname(__DIR__) . "/File/files/testFile{$i}.txt", "testFile{$i}", 'text/plain');
        }
        return $files;
    }

    public function cleanFiles(): void
    {
        $files = glob(dirname(__DIR__) . "/File/files/*", GLOB_BRACE);
    
        foreach ($files as $file ) {
            if(is_file($file)){
                unlink($file);
            }
        }
        
    }
}