<?php

use App\Entity\Auth\User;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Tests\Controller\File\FileGenerator;
use App\Services\FileServices\UploadFileService;

final class UploadFileServiceTest extends TestCase
{
    private UploadFileService $service;

    private FileGenerator $fileGenerator;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        /**@var User */
        $this->user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();

        /**@var EntityManagerInterface */
        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        
        $this->fileGenerator = new FileGenerator();
        $this->service = new UploadFileService($em);
    }

    public function test_ReturnFilesArray(): void
    {
        $this->assertIsArray($this->service->UploadFile($this->fileGenerator->createFiles(1), $this->user));
        $this->assertEquals(2, count($this->service->UploadFile($this->fileGenerator->createFiles(2), $this->user)) );
    }

}