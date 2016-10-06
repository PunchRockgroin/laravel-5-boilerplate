<?php

namespace App\Jobs\Hopper;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Services\Hopper\HopperFile;
use App\Services\Hopper\File\HopperFilePDF;
use Illuminate\Support\Facades\Storage;

class CreatePDF extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $hopperfilepdf;
    protected $file;
    protected $sourcedisk;
	protected $targetdisk;
	protected $temporarydisk;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file, $sourcedisk = 'hopper', $targetdisk = 'hopper', $temporarydisk = 'local')
    {
        $this->hopperfilepdf = app('hopper.file.pdf');
        $this->file = $file;
        $this->sourcedisk = $sourcedisk;
        $this->targetdisk = $targetdisk;
        $this->temporarydisk = $temporarydisk;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::error("Create PDF File Failed: ".$this->file);
            throw new \Exception; 
        }
        
		$response = $this->hopperfilepdf->generatePDF($this->file, $this->sourcedisk, $this->targetdisk, $this->temporarydisk);
        
		\Log::info('Create PDF File: Converted '.$this->file .' to ' . $response);
        
    }
}
