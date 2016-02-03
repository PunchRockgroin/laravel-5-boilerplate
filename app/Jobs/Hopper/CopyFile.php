<?php

namespace App\Jobs\Hopper;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Services\Hopper\HopperFile;
use Illuminate\Support\Facades\Storage;

class CopyFile extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $hopperFile;
    protected $driver_storage_path;
    protected $oldFilePath;
    protected $newFilePath;
    protected $fileEntity;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($oldFilePath, $newFilePath, $fileEntity = null)
    {
        $this->hopperFile = new HopperFile();
        $this->driver_storage_path = Storage::disk('hopper')->getDriver()->getAdapter()->getPathPrefix();
        $this->oldFilePath = $oldFilePath;
        $this->newFilePath = $newFilePath;
        $this->fileEntity = $fileEntity;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::error("File Copy Job Failed: ");
            throw new \Exception; 
        }
        $this->hopperFile->copyfile($this->oldFilePath, $this->newFilePath);
        \Log::info('Copy File: '.$this->oldFilePath .' to ' . $this->newFilePath);
        
//        if(!empty($this->fileEntity)){
//            event(new \App\Events\Backend\Hopper\FileEntityUpdated($this->fileEntity->id, 'update', 'Moved to master', null,  basename($this->newFilePath)));
//        }
    }
}
