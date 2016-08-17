<?php

namespace App\Jobs\Hopper;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Services\Hopper\HopperFile;
use Illuminate\Support\Facades\Storage;

class MoveFile extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $hopperfile;
    protected $oldFilePath;
    protected $newFilePath;
    protected $sourcedisk;
	protected $targetdisk;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($oldFilePath, $newFilePath, $sourcedisk = 'hopper', $targetdisk = 'hopper')
    {
        $this->hopperfile = app('hopper.file');
        $this->oldFilePath = $oldFilePath;
        $this->newFilePath = $newFilePath;
        $this->sourcedisk = $sourcedisk;
        $this->targetdisk = $targetdisk;
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
        $this->hopperfile->movefile($this->oldFilePath, $this->newFilePath, $this->sourcedisk, $this->targetdisk);
        \Log::info('Move File: '.$this->oldFilePath .' to ' . $this->newFilePath);
        
    }
}
