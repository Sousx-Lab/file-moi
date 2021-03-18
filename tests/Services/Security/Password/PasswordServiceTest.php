<?php
namespace App\Tests\Services\Security\Password;

use App\Entity\Auth\Password\PasswordResetToken;
use App\Repository\Auth\PasswordResetTokenRepository;
use App\Repository\Auth\UserRepository;
use App\Services\Security\Password\PasswordService;
use App\Services\Security\Password\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class PasswordServiceTest extends TestCase
{
    private PasswordService $service;

    public function setUp(): void
    {
        parent::setUp();
        /**@var UserRepository */
        $userRepository = $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()->getMock();

        /**@var PasswordResetTokenRepository */
        $tokenRespository = $this->getMockBuilder(PasswordResetTokenRepository::class)->disableOriginalConstructor()->getMock();

        /**@var TokenGeneratorService */
        $generator = $this->getMockBuilder(TokenGeneratorService::class)->disableOriginalConstructor()->getMock();

        /**@var EntityManagerInterface */
        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();

        /**@var EventDispatcherInterface */
        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        /**@var UserPasswordEncoderInterface */
        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();

        $this->service = new PasswordService(
            $userRepository,
            $tokenRespository,
            $em,
            $generator,
            $encoder,
            $dispatcher
        );
        parent::setUp();
    }

    public function test_IsExpired(): void
    {
        $this->assertTrue($this->service->isExpired((new PasswordResetToken())->setCreatedAt(new \DateTime('-40 minutes'))));
        $this->assertFalse($this->service->isExpired((new PasswordResetToken())->setCreatedAt(new \DateTime('-10 minutes'))));
    }
    
}