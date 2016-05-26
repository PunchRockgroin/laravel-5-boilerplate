<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WatchDirectories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watch:directories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $elWatcher = app('watcher');
        $elListener = $elWatcher->watch(env('WATCHER_DEFAULT_PATH',  storage_path().'/app/watch'));

        $elListener->onAnything(function($event, $resource, $path) {
            switch ($event->getCode()) {
                case \JasonLewis\ResourceWatcher\Event::RESOURCE_DELETED:
                    echo "{$path} was deleted (from anything listener)." . PHP_EOL;
                    break;
                case \JasonLewis\ResourceWatcher\Event::RESOURCE_MODIFIED:
                    echo "{$path} was modified (from anything listener)." . PHP_EOL;
                    break;
                case \JasonLewis\ResourceWatcher\Event::RESOURCE_CREATED:
                    $filename = basename($path);
//                    \Slack::send($filename.' was added to Done.');
//                    \Slack::to('@davidalberts')->send($filename.' was added to Done. Are we rich yet?');
                    \Slack::to(env('WATCHER_DEFAULT_PING',  '@davidalberts'))->attach([
                        'fallback' => $filename . ' was added to ' . $path,
                        'text' => $filename . ' was added to ' . $path,
                        'color' => 'good',
                        'mrkdwn_in' => ['pretext', 'text']
                    ])->send();
                    echo "{$path} was triggered." . PHP_EOL;
                    break;
            }
                });
         $elWatcher->start();
    }
}
