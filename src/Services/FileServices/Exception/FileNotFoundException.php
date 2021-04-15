<?php
namespace App\Services\FileServices\Exception;

use Doctrine\ORM\EntityNotFoundException;

class FileNotFoundException extends EntityNotFoundException
{
    public function __construct()
    {
        parent::__construct('', 0, null);
    }
    
    public function getMessageKey()
    {
        return 'This file not exist!.';
    }
}