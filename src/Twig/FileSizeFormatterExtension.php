<?php

namespace App\Twig;

use App\Services\FileServices\FileSizeFormatterService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FileSizeFormatterExtension extends AbstractExtension
{   
    private FileSizeFormatterService $formatterService;

    public function __construct(FileSizeFormatterService $formatterServiece) {
        $this->formatterService = $formatterServiece;
    }
    
    public function getFilters(): array
    {
        return [

            new TwigFilter('format_file_size', [$this, 'formatter']),
        ];
    }

    // public function getFunctions(): array
    // {
    //     return [
    //         new TwigFunction('function_name', [$this, 'doSomething']),
    //     ];
    // }

    public function formatter(int $size)
    {
        return $this->formatterService->format($size);
    }
}
