<?php
namespace App\Events\Security\Password;

use App\Entity\Auth\Password\PasswordResetToken;
use App\Entity\Auth\User;

final class PasswordResetTokenCreatedEvent
{
    private PasswordResetToken $token;

    public function __construct(PasswordResetToken $token) {

        $this->token = $token;
    }

    public function getUser(): User
    {
        return $this->token->getUser();
    }

    public function getToken(): PasswordResetToken
    {
        return $this->token;
    }
}

