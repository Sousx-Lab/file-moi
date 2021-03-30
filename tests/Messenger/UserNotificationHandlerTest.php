<?php

namespace App\Tests\Messenger;

use App\Entity\User;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use App\Services\Notifications\UserNotifierService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Messenger\MessageHandler\UserNotificationHandler;
use App\Messenger\NotificationMessage\UserNotificationMessage;

final class UserNotificationHandlerTest extends KernelTestCase
{
    use FixturesTrait;

    /**@var User */
    private $user;

    private array $emailData;

    private UserNotificationMessage $message;
    protected function setUp(): void
    {
        self::bootKernel();

        $this->user = $this->loadFixtureFiles([dirname(__DIR__, 1) . '/Controller/users.yaml']);
        $this->emailData = ['object' => 'Test Object', 'message' => 'Test Message'];
        $this->message = new UserNotificationMessage($this->user['user_user']->getId(), $this->emailData);

    }

    public function test_UserNotificationHandler(): void
    {   

        $handler = $this->createStub(UserNotificationHandler::class);

        $handler->method('__invoke')
            ->willReturn(UserNotifierService::class);

        $this->assertSame(UserNotifierService::class, $handler->__invoke($this->message));
        $this->assertNotNull($handler->__invoke($this->message));
    }

}
