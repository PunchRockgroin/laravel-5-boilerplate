<?php

namespace App\Services\Hopper\Contracts;

Interface HopperFileContract
{

    public function getDriverStoragePath($disk);
    
    public function copyfile($oldFilePath, $newFilePath, $disk);

}