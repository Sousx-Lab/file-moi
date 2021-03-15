<?php

use App\Services\Security\Password\TokenGeneratorService;
use PHPUnit\Framework\TestCase;

final class PasswordTokenGeneratorTest extends TestCase
{
    
    public function test_TokenGenerator(): void
    {
        $service = new TokenGeneratorService();
        for($i = 1; $i <= 20; $i++){
            $this->assertEquals($i, \mb_strlen($service->generate($i)));
        }
        
        $this->assertEquals($service::SIZE, \mb_strlen($service->generate()));
    }
}
