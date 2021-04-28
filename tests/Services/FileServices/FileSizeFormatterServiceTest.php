<?php

namespace App\Tests\Services\FileServices;

use PHPUnit\Framework\TestCase;
use App\Services\FileServices\FileSizeFormatterService;

final class FileSizeFormatterServiceTest extends TestCase
{
    
    private $formatter;

    public function setUp(): void
    {
        parent::setUp();

        $this->formatter = $this->getMockBuilder(FileSizeFormatterService::class)->getMock();
    }

    public function test_Formatter(): void
    {    
        $this->formatter->method('format')
            ->will($this->onConsecutiveCalls("1Gb", "1Mb", "1Kb", "999 Bytes", "1B"));

        $this->assertSame("1Gb", $this->formatter->format(1073741824));
        $this->assertSame("1Mb", $this->formatter->format(1048576));
        $this->assertSame("1Kb", $this->formatter->format(1024));
        $this->assertSame("999 Bytes", $this->formatter->format(999));
        $this->assertSame("1B", $this->formatter->format(1));
    }
}
