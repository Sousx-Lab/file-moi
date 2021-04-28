<?php
namespace App\Events\Security\Registration;

use App\Entity\Auth\User;

class NewUserEnrolledEvent
{
    private User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}