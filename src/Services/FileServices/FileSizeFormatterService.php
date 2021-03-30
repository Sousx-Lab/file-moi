<?php
namespace App\Services\FileServices;

class FileSizeFormatterService
{
    
    public function format(int $size)
    {

        if($size >= 1073741824){
            $size = number_format($size / 1073741824, 2) . ' Gb';
        }
        elseif($size >= 1048576){
            $size = number_format($size / 1048576, 2) . ' Mb';
        }
        elseif($size >= 1024)
        {
            $size = number_format($size / 1024, 2) . ' Kb';
        }
        elseif($size > 1)
        {
            $size = $size . ' Bytes';
        }
        else
        {
            $size = '0 B';
        }
        return $size;
    }
}