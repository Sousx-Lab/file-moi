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
            ->will($this->onConsecutiveCalls("1 Gb", "1 Mb", "1 Kb", "999 Bytes", "1 B"));

        $this->assertSame("1 Gb", $this->formatter->format(1073741824));
        $this->assertSame("1 Mb", $this->formatter->format(1048576));
        $this->assertSame("1 Kb", $this->formatter->format(1024));
        $this->assertSame("999 Bytes", $this->formatter->format(999));
        $this->assertSame("1 B", $this->formatter->format(1));
    }
}
