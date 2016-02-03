<?php

namespace App\Services\Hopper\Contracts;

Interface HopperContract
{

    public function blastOff();
    
    public function groupedHistory($history, $date);
    
    public function getCurrentVersion($currentFileName);
    
    public function renameFileVersion($currentFileName, $nextVersion, $currentFileExtension);
    
    public function parseDateTimeForDisplay($date, $format, $timezone);

}