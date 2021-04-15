<?php

use App\Services\FileServices\FileRemoverService;
use App\Tests\Controller\File\FileGenerator;
use PHPUnit\Framework\TestCase;

final class FileRemoverServieTest extends TestCase
{
    use FileGenerator;

    private $FileRemover;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->FileRemover = new FileRemoverService();

    }

    public function test_RemoveFile(): void
    {
        $file = $this->createFiles(1);
        $filePath =  $file[0]->getPath() . DIRECTORY_SEPARATOR;
        $filesName = $file[0]->getFileName();
        $this->FileRemover->removeFile($filePath . $filesName, $filesName);
        
        $this->assertFileNotExists($filePath . '/files/' . $filesName);
        $this->assertDirectoryDoesNotExist($filePath);

    }
    
}