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
    
    protected $hopperfile;
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
        $this->hopperfile = app('hopper.file');
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
            \Log::error("File Move Job Failed: ".$this->oldFilePath .' to ' . $this->newFilePath);
            throw new \Exception; 
        }
        $this->hopperfile->movefile($this->oldFilePath, $this->newFilePath);
        \Log::info('Move File: '.$this->oldFilePath .' to ' . $this->newFilePath);
        
    }
}
