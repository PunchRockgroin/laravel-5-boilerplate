<?php

namespace App\Console\Commands;

use App\Events\Backend\Hopper\Heartbeat;
use Illuminate\Console\Command;

class SendHeartbeat extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'hopper:heartbeat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a heartbeat to help the client verify if it is connected to the internet.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        event(new Heartbeat());
    }
}