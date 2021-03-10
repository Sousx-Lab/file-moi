<?php
namespace App\Services\Security\Password;


final class TokenGeneratorService 
{
    private const SIZE = 20;

    public function generate(int $size = null): string
    {
        $bytes = \random_bytes($size ?? self::SIZE);
        $string = \substr(
            \str_replace(['/', '+', '='], '', \base64_encode($bytes)), 0, $size ?? self::SIZE);
        
        return $string;
    }
}
