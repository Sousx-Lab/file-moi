<?php
namespace App\Services\FileServices;

class FileSizeFormatterService
{
    
    public function format(int $size): string
    {

        if($size >= 1073741824){
            $size = number_format($size / 1073741824, 0) . 'Gb';
        }
        elseif($size >= 1048576){
            $size = number_format($size / 1048576, 0) . 'Mb';
        }
        elseif($size >= 1024)
        {
            $size = number_format($size / 1024, 0) . 'Kb';
        }
        elseif($size >= 1)
        {
            $size = $size . 'Byte';
        }
        else
        {
            $size = '0 Bytes';
        }
        return $size;
    }
}