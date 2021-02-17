<?php
namespace App\Messenger\Message;

class UserNotificationMessage
{
    private string $userId;

    private array $emailData;

    public function __construct(string $userId, array $emailData) {
        $this->userId = $userId;
        $this->emailData = $emailData;
    }

    public function getEmailData(): array
    {
        return $this->emailData;
    }
    
    public function getUserId(): string
    {
        return $this->userId;
    }
}
