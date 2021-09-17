<?php
namespace App\Tests\Controller\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FileGenerator {

    public function createFiles(int $num): array
    {
        if(!is_dir(dirname(__DIR__) . "/File/files/"))
        {
            mkdir(dirname(__DIR__) . "/File/files", 0777, true);
        }
        
        $files = [];
        for ($i = 0; $i < $num; $i++) {
            file_put_contents(
                dirname(__DIR__) . "/File/files/testFile{$i}.txt",
                'This file is created automatically when run HomeControllerTest'
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