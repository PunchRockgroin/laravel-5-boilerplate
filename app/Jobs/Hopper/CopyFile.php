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
        if ($this->attempts() > 2) {
            \Log::error("File Copy Job Failed: ".$this->oldFilePath .' to ' . $this->newFilePath);
            throw new \Exception; 
        }
        $this->hopperfile->copyfile($this->oldFilePath, $this->newFilePath);
        \Log::info('Copy File: '.$this->oldFilePath .' to ' . $this->newFilePath);
        
    }
}
