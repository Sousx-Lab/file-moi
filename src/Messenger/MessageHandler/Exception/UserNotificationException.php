<?php
namespace App\Messenger\MessageHandler\Exception;

use App\Entity\Auth\Exception\UserNotFoundException;

class UserNotificationException extends UserNotFoundException {
    
}