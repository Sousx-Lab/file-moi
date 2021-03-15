<?php

use App\Entity\Auth\Password\PasswordResetToken;
use App\Repository\PasswordResetTokenRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PasswordResetTokenRepositroyTest extends KernelTestCase
{

    use FixturesTrait;
    public function test_FindOneByToken(): void
    {
        self::bootKernel();
        $fixtures = $this->loadFixtureFiles([dirname(__DIR__, 1) . "/Controller/users.yaml"]);

        /**@var PasswordResetToken */
        $token = self::$container->get(PasswordResetTokenRepository::class)->findOneByToken($fixtures['token_token']->getToken());
        $this->assertInstanceOf(PasswordResetToken::class, $token);
    }
}