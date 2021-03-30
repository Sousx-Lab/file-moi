<?php

use App\Entity\Auth\User;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Tests\Controller\File\FileGenerator;
use App\Services\FileServices\UploadFileService;

final class UploadFileServiceTest extends TestCase
{
    use FileGenerator;
    private UploadFileService $service;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        /**@var User */
        $this->user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();

        /**@var EntityManagerInterface */
        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        
        $this->service = new UploadFileService($em);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cleanFiles();
    }
    
    public function test_ReturnFilesArray(): void
    {
        $this->assertIsArray($this->service->UploadFile($this->createFiles(1), $this->user));
        $this->assertEquals(2, count($this->service->UploadFile($this->createFiles(2), $this->user)) );
    }

}