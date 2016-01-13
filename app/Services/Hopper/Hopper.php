<?php

namespace App\Services\Hopper;

use App\Services\Hopper\Contracts\HopperContract;

use Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use GrahamCampbell\Dropbox\Facades\Dropbox;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon as Carbon;

use App\Services\Hopper\HopperFile;

use Vinkla\Pusher\PusherManager;

use App\Models\Hopper\EventSession;
use App\Models\Hopper\Visit;
use App\Models\Hopper\FileEntity;

class Hopper implements HopperContract
{

    public function blastOff()
    {

        return 'Houston, we have ignition';

    }

}