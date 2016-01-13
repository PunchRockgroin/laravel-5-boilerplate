<?php
namespace App\Services\Hopper;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use GrahamCampbell\Dropbox\Facades\Dropbox;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon as Carbon;
use Vinkla\Pusher\PusherManager;

use App\Models\Hopper\EventSession;
use App\Models\Hopper\Visit;
use App\Models\Hopper\FileEntity;

class HopperFile {
    
    
    public function uploadToTemporary($file, $newFileName) {
        $upload_success = Storage::disk('hopper')->put('temporary/' . $newFileName, $file);
        if ($upload_success) {
            return true;
        }
        return false;
    }
    
    public function renameFileVersion($currentFileName, $nextVersion, $currentFileExtension = null) {
		
        $currentFileParts = pathinfo($currentFileName)['filename'];
		if($currentFileExtension === null){
			$currentFileExtension = pathinfo($currentFileName)['extension'];
		}
//        
        $currentFileNameParts = explode('_', $currentFileParts);
        $currentFileNameArray = [
            'sessionID' => $currentFileNameParts[0],
            'speaker' => $currentFileNameParts[1],
            'roomIDs' => $currentFileNameParts[2],
            'version' => $currentFileNameParts[3],
            'shareStatus' => $currentFileNameParts[4]
        ];

        $newFileName = $currentFileNameArray['sessionID']
                . '_' . $currentFileNameArray['speaker']
                . '_' . $currentFileNameArray['roomIDs']
                . '_LCC' . $nextVersion
                . '_' . $currentFileNameArray['shareStatus']
                . '.' . $currentFileExtension;

        return $newFileName;
    }
}