<?php
namespace App\Services\FileServices\Exception;

use Doctrine\ORM\EntityNotFoundException;

class UserFileListException extends EntityNotFoundException
{
    public function __construct()
    {
        parent::__construct('', 0, null);
    }
    
    public function getMessageKey()
    {
        return 'This file is not in your files list!.';
    }
}