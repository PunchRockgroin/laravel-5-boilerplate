<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\IssueAlert;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Maknz\Slack\Facades\Slack;


class IssueAlertHandler
{
	
	protected $client;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct( \Maknz\Slack\Client $client )
    {
		$this->client = $client;
        //
    }

    /**
     * Handle the event.
     *
     * @param  IssueAlert  $event
     * @return void
     */
    public function handle(IssueAlert $event)
    {
		
		if(!config('hopper.alert.enable', false)){
			return;
		}
		
		$target = config('hopper.alert.target');
		if( $this->validateString($event->target) ){
			$target = $event->target;
		}
		
		$message = 'Hopper has a new alert!';
		if( $this->validateString($event->message) ){
			$message = $event->message;
		}
		
		$slack = $this->client->createMessage();

		$slack->to($target)->setText($message);
		
		$slack->setAttachments( $event->attachments );
		
		$slack->send();
		
//		Slack::to('@davidalberts')->attach([
//			'fallback' => 'Current server stats',
//			'text' => 'Current server stats',
//			'color' => 'danger',
//			'fields' => [
//				[
//					'title' => $level,
//					'value' => $message
//				]
//			]
//		])->send('New alert from the monitoring system');
		
		
    }
	
	private function validateString($string){
		if(!empty($string) && is_string($string)){
			return true;
		}
		return false;
	}
	
	private function validateArray($array){
		if(!empty($array) && is_array($array)){
			return true;
		}
		return false;
	}
}
