<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Hopper\Contracts\HopperContract as Hopper;
use App\Services\Hopper\Contracts\HopperUserContract;

use App\Services\Hopper\HopperUser;

use Vinkla\Pusher\PusherManager;
use Vinkla\Pusher\Facades\Pusher;

use App\Models\Hopper\EventSession;
use App\Models\Hopper\Visit;

use Yajra\Datatables\Html\Builder; // import class on controller

/**
 * Class DashboardController
 * @package App\Http\Controllers\Backend
 */
class DashboardController extends Controller
{
	
	private $hopperstats;
	private $hopperuser;
	private $pusher;
	


	public function __construct(PusherManager $pusher, HopperUserContract $hopperuser) {
		$this->pusher = $pusher;
		$this->hopperstats = new \App\Services\Hopper\HopperStats();
		$this->hopperuser = $hopperuser;
	}
	
	/**
     * @return \Illuminate\View\View
     */
    public function index(Hopper $hopper, Builder $htmlbuilder)
    {		
		$graphicops = \App\Models\Access\User\User::with('assignments')
													->with('visitCount')
													->whereHas('roles', function ($query) {
														$query->where('name', '=', 'Graphic Operator');
													})
													->get();
		foreach($graphicops as $op){
			debugbar()->info($op->visitsCount);
		}
		
//		$usersByStatus = $this->hopperuser->users_by_status();
//		
//		$usersByStatus = $this->hopperuser->visitsToCount($usersByStatus);
		
//		foreach($usersByStatus as $op){
//			debugbar()->info($op->name . ' ' . $op->visits->count());
//		}	
		
		$EventSessions = EventSession::all();
		$checkinsovertime = $this->hopperstats->check_ins_over_time(\Carbon\Carbon::now()->subDays(4), \Carbon\Carbon::now());

        $visitsovertime = $this->hopperstats->visits_over_time(\Carbon\Carbon::now()->subDays(4), \Carbon\Carbon::now());

		javascript()->put([
            'checkedInData' => $this->hopperstats->js_get_checked_in($EventSessions),
            'checkInByDay' => $this->hopperstats->js_visits_and_checkins_over_time($checkinsovertime, $visitsovertime, \Carbon\Carbon::now()->subDays(4), \Carbon\Carbon::now()),
        ]);
		
		$EventSessionCheckin = $this->hopperstats->get_checked_in($EventSessions);
		
		$VisitStats = $this->hopperstats->visit_stats();
		
		$TopVisits = collect( $this->hopperstats->top_user_visits() );
		
		$assignment_html = $htmlbuilder
		->addColumn(['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Updated At'])
        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'ID'])
        ->addColumn(['data' => 'session_id', 'name' => 'session_id', 'title' => 'Session ID'])
        ->addColumn(['data' => 'assignment_user_id', 'name' => 'assignment_user_id', 'title' => 'Assignment'])
     
		->parameters(
			['order' => [[0, 'asc']]]
		)
		->ajax(route('admin.visit.assignments'))
        ->addAction();
		
		
        $data = [
			'VisitStats' => $VisitStats,
			'EventSessionCheckin' => $EventSessionCheckin,
			'TopVisits' => $TopVisits,
			'AssignmentHTML' => $assignment_html,
		];
		
//		debugbar()->info($this->pusher->get_channel_info('presence-test_channel',array('info' => 'members')));
		
//		\Pusher::get_channel_info('presence-test_channel');
//        $filtered = [];
//        $grouped = [];
//               
//        $elusers = [];
//        
////        $admins = \App\Models\Access\Role\Role::all();
//          $users = \App\Models\Access\User\User::all();
//          
////        debugbar()->info($users->all());
//        
////        
//          
//        $users->transform(function ($item, $key) {
//            $value = \Cache::get('heartbeat-'.md5($item['email']));
//            if($value){
//                $item['statusclass'] = 'yellow';
//                $item['heartbeat'] = json_decode($value, TRUE);
//            }else{
//                $item['statusclass'] = 'gray';
//                $item['heartbeat'] = ['route'=>'offline'];
//            }
//            $item['roles'] = $item->roles;
//            return $item;
//        });  
//        //debugbar()->info($users);
//        
//        $grouped = $users->groupBy(function ($item, $key) {
//            return $item['heartbeat']['route'];
//        });
//        
//          debugbar()->info($grouped);
//       
//        
////        $filtered = $grouped->only(['admin.filentity.edit']);
//        
//        
//        
////        $filtered = $grouped->only('admin.fileentity.edit');
//        if(isset($grouped['admin.visit.edit'])){
//            $filtered = $grouped['admin.visit.edit'];
//            unset($grouped['admin.visit.edit']);
//        }
//        
//        
////        $filtered->transform(function ($item, $key) {
//////            $item['blah'] = 'foo';
////            return $item;
////        });
//             
//        
//        
//         debugbar()->info($filtered);
//          debugbar()->info($grouped);
//        
//        $data['inVisit'] = $filtered;
//        $data['other'] = $grouped;
//        

       
        
        
        return view('backend.dashboard', $data);
    }
    
    public function data(\Illuminate\Http\Request $request){
        $filtered = [];
        $grouped = [];
        $users = \App\Models\Access\User\User::all();

        $users->transform(function ($item, $key) {
            $value = \Cache::get('heartbeat-'.md5($item['email']));
            if($value){
                $item['statusclass'] = 'yellow';
                $item['heartbeat'] = json_decode($value, TRUE);
            }else{
                $item['statusclass'] = 'gray';
                $item['heartbeat'] = ['route'=>'offline'];
            }
            $item['roles'] = $item->roles;
            return $item;
        });  
        //debugbar()->info($users);
        
        $data['groups'] = $users->groupBy(function ($item, $key) {
            return $item['heartbeat']['route'];
        });

        if(isset($data['groups']['admin.visit.edit'])){
            $filtered = $data['groups']['admin.visit.edit'];
            unset($data['groups']['admin.visit.edit']);
            $filtered->transform(function ($item, $key) {
                $visit = \App\Models\Hopper\Visit::find($item['heartbeat']['parameters'][2]);
                if($visit){
                    $item['visit'] = $visit ;
                }
                return $item;
            });
        }
        

        $data['inVisit'] = $filtered;
        
        return response()->json(['message' => 'ok', 'payload' => $data]);
        
    }
	
	public function userHeartbeat($id){
        $heartbeat = [];
        $user = \App\Models\Access\User\User::find($id);
		$value = \Cache::get('heartbeat-'.md5($user->email));
		if($value){
			$heartbeat['statusclass'] = 'yellow';
			$heartbeat['heartbeat'] = json_decode($value, TRUE);
		}else{
			$heartbeat['statusclass'] = 'gray';
			$heartbeat['heartbeat'] = ['route'=>'offline'];
		}
        
		//debugbar()->info($users);
                
        return response()->json(['message' => 'ok', 'payload' => $heartbeat]);
        
    }
	
	public function userStatusData(){		
		$usersByStatus = $this->hopperuser->users_by_status();
		return response()->json(['message' => 'ok', 'payload' => $usersByStatus]);
	}
	
	public function userStatusUpdate($id){	
		try {
			$user = $this->hopperuser->toggle_state($id);
			
			$userCollection = $this->hopperuser->sanitizeUserCollection( collect($user) );
			
			Pusher::trigger('hopper_channel', 'user_status', ['message' => 'update', 'user'=> $userCollection ]);
			
			return response()->json(['message' => 'ok', 'payload' => ['user' => $userCollection]]);
		} catch ( Exception $ex ) {
			return response()->json(['message' => 'error', 'payload' => $ex]);
		}	
	}
}