<?php

namespace App\Services\Hopper;


use Illuminate\Http\Request;

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

class HopperStats {
	
	
	

	public function get_checked_in($EventSessions = null){
        
        if(!empty($EventSessions)){
            $EventSessions = EventSession::all();
        }
        
        $session_data = [];

        $session_data['checked_in'] = $EventSessions->filter(function ($EventSession) {
            return $EventSession->checkedInBoolean();
        })->count();
        $session_data['not_checked_in'] = $EventSessions->filter(function ($EventSession) {
            return !$EventSession->checkedInBoolean();
        })->count();  
        
        return $session_data;
    }


    public function js_get_checked_in($EventSessions) {
        $data = [];
        
        $session_data = $this->get_checked_in($EventSessions);      
        
        $data = array(
            array(
                'value' => $session_data['checked_in'],
                'color' => "#00B388",
                'highlight' => "#425563",
                'label' => "Checked In"
            ),
            array(
                'value' => $session_data['not_checked_in'],
                'color' => "#FF8D6D",
                'highlight' => "#425563",
                'label' => "Not Checked In"
            )
        );
        return $data;
    }
    
    public function get_brand_approval($EventSessions = null){
        
        if(!empty($EventSessions)){
            $EventSessions = EventSession::all();
        }
        
        $session_data = [];
        
        $session_data['approval_brand'] = $EventSessions->filter(function ($EventSession) {
            return ($EventSession->approval_brand === 'YES' || $EventSession->approval_brand === 'N/A');
        })->count();
        $session_data['not_approval_brand'] = $EventSessions->filter(function ($EventSession) {
            return $EventSession->approval_brand === 'NO';
        })->count();
        
        return $session_data;
    }


    public function js_get_brand_approval($EventSessions) {
        $data = [];
        
        $session_data = $this->get_brand_approval($EventSessions);      
        
        $data = array(
            array(
                'value' => $session_data['approval_brand'],
                'color' => "#00B388",
                'highlight' => "#425563",
                'label' => "Approved"
            ),
            array(
                'value' => $session_data['not_approval_brand'],
                'color' => "#FF8D6D",
                'highlight' => "#425563",
                'label' => "Not Approved"
            )
        );
        return $data;
    }
    
    public function get_legal_revrec($EventSessions){
        $session_data = [];
        
        if(!empty($EventSessions)){
            $EventSessions = EventSession::all();
        }
        
        $session_data['none'] = $EventSessions->filter(function ($EventSession) {
            return ($EventSession->approval_revrec === 'YES' || $EventSession->approval_revrec === 'N/A') &&  ($EventSession->approval_legal === 'YES' || $EventSession->approval_legal === 'N/A');
        })->count();
        $session_data['legal_only'] = $EventSessions->filter(function ($EventSession) {
            return ($EventSession->approval_revrec === 'YES' || $EventSession->approval_revrec === 'N/A') &&  ($EventSession->approval_legal === 'NO');
        })->count();  
        $session_data['revrec_only'] = $EventSessions->filter(function ($EventSession) {
            return ($EventSession->approval_revrec === 'NO') &&  ($EventSession->approval_legal === 'YES' || $EventSession->approval_legal === 'N/A');
        })->count();
        $session_data['both'] = $EventSessions->filter(function ($EventSession) {
            return ($EventSession->approval_revrec === 'NO') && ($EventSession->approval_legal === 'NO');
        })->count();  
        
        return $session_data;
    }
    
    public function js_get_legal_revrec($EventSessions) {
        $data = [];
        $session_data = $this->get_legal_revrec($EventSessions);
        $data = array(
            array(
                'value' => $session_data['none'],
                'color' => "#00B388",
                'highlight' => "#425563",
                'label' => "Approved"
            ),
            array(
                'value' => $session_data['revrec_only'],
                'color' => "#FF8D6D",
                'highlight' => "#425563",
                'label' => "Needs RevRec Review Only"
            ),
            array(
                'value' => $session_data['legal_only'],
                'color' => "#5F7A76",
                'highlight' => "#425563",
                'label' => "Needs Legal Review Only"
            ),
            array(
                'value' => $session_data['both'],
                'color' => "#80746E",
                'highlight' => "#425563",
                'label' => "Needs Both Reviews"
            ),
        );
        return $data;
    }
    
    
    public function check_ins_over_time($first, $last){
        $data = EventSession::latest()
                ->whereDate('check_in_datetime', '>=', $first->toDateString())
                ->whereDate('check_in_datetime', '<=', $last->toDateString())
                ->get()->groupBy(function($item)
        {
          return $item->check_in_datetime->format('Y-m-d');
        });

          $check_ins_array = $this->arrayFlipAndZero($this->buildDateRangeArray($first, $last));
          
          foreach($data as $key => $entry) {
              $check_ins_array[$key] = $entry->count();
          }
          
        return $check_ins_array;

    }
    
    
    public function visits_over_time($first, $last, $Visit = null){
        $data = Visit::latest()
                ->whereDate('created_at', '>=', $first->toDateString())
                ->whereDate('created_at', '<=', $last->toDateString())
                ->get()->groupBy(function($item)
        {
          return $item->created_at->format('Y-m-d');
        });

          $visits_array = $this->arrayFlipAndZero($this->buildDateRangeArray($first, $last));
          
          foreach($data as $key => $entry) {
              $visits_array[$key] = $entry->count();
          }
          
        return $visits_array;
    }
	
	public function get_visits(){
		return Visit::select(['id', 'session_id', 'visitors', 'design_username', 'difficulty', 'created_at', 'updated_at']);
	}
	
	public function get_visits_by_user($design_username){
		return Visit::select(['id', 'session_id', 'visitors', 'design_username', 'difficulty', 'created_at', 'updated_at'])
				->where('design_username', '=', $design_username);
	}
	
	public function parse_visit_data($visitdata){
		$parsed = [];
		
		$parsed['count'] = count($visitdata);
		$parsed['avg_difficulty'] = $visitdata->avg('difficulty');
		
		return $parsed;
	}

	public function visits_by_user($Visits = null){
		if( empty( $Visits ) && ! $Visits instanceof \Illuminate\Support\Collection ){
			$Visits = $this->get_visits()->get();
		}
		$visitsbyuser = [];
		foreach($Visits->groupBy('design_username') as $key => $visitbyuser){
			$visitsbyuser[$key] = $this->parse_visit_data($visitbyuser);
		}
		return $visitsbyuser;
	}
	
	public function visits_by_self($Visits = null){
		if( empty( $Visits ) && ! $Visits instanceof \Illuminate\Support\Collection ){
			$MyUsername =  auth()->user()->name;
			$Visits = $this->get_visits_by_user($MyUsername)->get();
		}
		if($Visits->count()){
			return $this->parse_visit_data($Visits);
		}
		return null;
	}
	
	
	public function visit_stats(){
		$Visits = $this->get_visits()->get();

		return [
			'totalvisits' => $Visits->count(),
			'visitsbyuser' => $this->visits_by_user( $Visits ),
			'visitsovertime' => $this->visits_over_time( \Carbon\Carbon::now()->subWeeks(1), \Carbon\Carbon::now() ),
			'visit_avg_difficulty' => $Visits->avg('difficulty'),
		];
		
	}
    
    
    public function js_visits_and_checkins_over_time($checkinsovertime, $visitsovertime, $first, $last) {


        $labels = $this->makeDayLabels( $this->arrayFlipAndZero( $this->buildDateRangeArray($first, $last) ), 'D m/d' );
        
        $data = array(
            'labels' => $labels,
            'datasets' => [
                array(
                    'label' => "Visits",
                    'fillColor' => "rgba(198,201,202,0.3)",
                    'strokeColor' => "rgba(198,201,202,0.3)",
                    'pointColor' => "rgba(198,201,202,.7)",
                    'pointStrokeColor' => "rgba(198,201,202,1)",
                    'pointHighlightFill' => "#fff",
                    'pointHighlightStroke' => "#00B388",
                        'data' => array_values($visitsovertime)
                ),
                array(
                    'label' => "Checked In",
                    'fillColor' => "rgba(0,179,136, 1)",
                    'strokeColor' => "rgba(0,179,136, 1)",
                    'pointColor' => "rgba(0,179,136, 1)",
                    'pointStrokeColor' => "#00B388",
                    'pointHighlightFill' => "#fff",
                    'pointHighlightStroke' => "#00B388",
                        'data' => array_values($checkinsovertime)
                ),
                
            ],
        );
        return $data;
    }
    
    public function buildDateRangeArray($first, $last)
    {
        $dates = [];
        while ($first <= $last)
        {
            $dates[] = $first->toDateString();

            $first->addDay();
        }

        return $dates;
    }
    
    public function makeDayLabels($date_array, $format = 'm/d')
    {
        $dateKeys = array_keys($date_array);
        
        foreach($dateKeys as $key => $value){
            $dateKeys[$key] = \Carbon\Carbon::parse($value)->format($format);
        }
        return $dateKeys;
    }
    
    public function arrayFlipAndZero($array)
    {
        $newarray = array_flip($array);
        
        foreach($newarray as $key => $value){
            $newarray[$key] = 0;
        }

        return $newarray;
    }
	
}