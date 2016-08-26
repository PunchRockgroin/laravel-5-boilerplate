<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Yajra\Datatables\Html\Builder; // import class on controller

use App\Models\Hopper\Visit;
use App\Services\Hopper\HopperVisit;

use App\Services\Hopper\Contracts\HopperContract;

use Vinkla\Pusher\PusherManager;

class VisitController extends Controller
{
    
    private $messagebag;
    private $pusher;
	
    
    public function __construct(\Illuminate\Support\MessageBag $messagebag, PusherManager $pusher)
    {
        $this->messagebag = $messagebag;        
		$this->pusher = $pusher;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlbuilder)
    {		
        
		$hopperstats = new \App\Services\Hopper\HopperStats();
		
		$EventSessions = \App\Models\Hopper\EventSession::all();
		
		$EventSessionCheckin = $hopperstats->get_checked_in($EventSessions);
		
		$MyVisitStats = collect( $hopperstats->visits_by_self() );
		
		$TopVisits = collect( $hopperstats->top_user_visits() );
		
		
		javascript()->put([
            'graphicOpsPie' => $hopperstats->js_chart_user_visits(),
        ]);

        $data = [
//            'html' => $html,
			'VisitStats' => $MyVisitStats,
			'EventSessionCheckin' => $EventSessionCheckin,
			'TopVisits' => $TopVisits,
//			'assignment_html' => $assignment_html,
        ];
        		
        return view('backend.visit.index', $data);
    }
	
	public function datatable(Request $request){
		if ($request->ajax()) {
            $Visits = Visit::select(['id', 'session_id', 'visitors', 'assignment_user_id', 'design_username',  'created_at', 'updated_at']);
            return \Datatables::of($Visits)
					->editColumn('assignment_user_id', function ($visit) {
						if($visit->user){
							return $visit->user->name;
						}
						elseif($visit->assignment_user_id === 0){
							return "<div class='label label-success'>Complete</div>";
						}
						else{
							return "<div class='label label-warning'>Unassigned</div>";
						}
                    })
                    ->editColumn('created_at', '{!! Carbon\Carbon::parse($created_at)->diffForHumans() !!}')
                    ->editColumn('updated_at', '{!! Carbon\Carbon::parse($updated_at)->diffForHumans() !!}')
                     ->editColumn('action', function ($visit) {
                        $content = '';
						if(config('hopper.print.enable', false)){
							$content .= '<a class="btn btn-primary btn-xs" href="'. route('admin.visit.edit', [$visit->id]).'">Edit</a> ';
							$content .= '<a class="btn btn-info btn-xs" href="'. route('admin.visit.invoice', [$visit->id]).'">Invoice</a> ';
							$content .= '<a target="_blank" class="btn btn-info btn-xs" href="'. route('admin.visit.print', [$visit->id]).'">Print</a> ';
						}else{
							$content .= '<a class="btn btn-primary btn-block btn-xs" href="'. route('admin.visit.edit', [$visit->id]).'">Edit</a> ';
						}
                        return $content;
                    })
                    ->make(true);
        }
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function create()
//    {
//        //
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    public function store(Request $request)
//    {
//        //
//    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
		$Visit = Visit::find($id);
		if ($request->ajax()) {
            return response()->json(['message' => 'ok', 'payload' => $Visit]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Visit $visit, HopperVisit $hoppervisit)
    {
        $data = $hoppervisit->edit($visit);       
		
        event(new \App\Events\Backend\Hopper\Heartbeat(auth()->user(), request()->route(), \Carbon\Carbon::now()->toIso8601String()));		
		
        return view('backend.visit.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Visit $visit, HopperVisit $hoppervisit)
    {			
        $this->validate($request, [
            'visitor_type' => 'required',
            'difficulty' => 'required',
        ],
        [
            'visitor_type.required' => 'Please ensure you are logging your visitor',
            'difficulty.required' => 'You must choose a difficulty',
        ]);
        
        $hoppervisit->update($request->all(), $visit);
        
        $this->messagebag->add('updated', "Visit " . $visit->id . " for ". $visit->session_id ." Updated");
		
		if(config('hopper.print.enable', false) && config('hopper.print.timing', false) === 'after_visit'){
//			return redirect()->route('admin.visit.invoice', $visit->id)->withFlashSuccess($this->messagebag);	
			return redirect()->route('admin.visit.index')->withFlashSuccess($this->messagebag);
		}
        
        return redirect()->route('admin.visit.index')->withFlashSuccess($this->messagebag);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    /**
     * Find Visit by ID from form.
     *
     * 
     * @return \Illuminate\Http\Response
     */
    public function find(Request $request)
    {
        $visit_id = $request->get('visit_id');
        
        if(empty($visit_id)){
            return redirect(route('admin.visit.index'))
                    ->withErrors(['Alert', 'Please enter a valid visit ID']);
        }
        $visit = Visit::find($visit_id);
        if(count($visit)){
            return redirect( route('admin.visit.edit', ['id' => $visit_id]) );
        }
        $sessionid_visit = Visit::where('session_id', '=' , strtoupper($visit_id))->get();
        if(count($sessionid_visit)){
            return redirect(route('admin.visit.edit', ['id'=> $sessionid_visit->last()->id]));
        }
        
        return redirect(route('admin.visit.index'))
                    ->withErrors(['Alert', 'Could not find Visit ID: '. $visit_id]);
    }
	
	public function assignments(Request $request){
		if ($request->ajax()) {
            $Visits = Visit::select(['id', 'session_id', 'updated_at', 'assignment_user_id'])
							 ->where('assignment_user_id', '>', 0)
							 ->orWhereNull('assignment_user_id');
            return \Datatables::of($Visits)
//                    ->editColumn('created_at', '{!! $created_at->diffForHumans() !!}')
                    ->editColumn('updated_at', function ($visit) {
                        return $visit->updated_at->format('h:m:s');
                    })
                    ->editColumn('assignment_user_id', function ($visit) {
						if($visit->user){
							return $visit->user->name;
						}
						else{
							return "<div class='label label-warning'>Unassigned</div>";
						}
                    })
                     ->editColumn('action', function ($visit) {
                        $content = '';
						if($visit->assignment_user_id){
							$content .= '<a class="btn btn-info btn-xs" href="'. route('admin.visit.edit', [$visit->id]).'">Update</a> ';
						}else{
							$content .= '<button class="btn btn-success btn-block btn-xs assign-visit-to-user" data-id="'.$visit->id.'" data-session-id="'.$visit->session_id.'" >Assign</button> ';
						}
//                        $content .= '<a class="btn btn-success btn-xs" href="'. route('admin.visit.edit', [$visit->id]).'">Assign</a> ';
//                        $content .= '<a class="btn btn-info btn-xs" href="'. route('admin.visit.show', [$visit->id]).'">Show</a> ';
                        return $content;
                    })
                    ->make(true);
        }
	}
	
	public function myassignments(Request $request){
		if ($request->ajax()) {
            $Visits = Visit::select(['id', 'session_id', 'updated_at'])
							 ->where('assignment_user_id', '=', auth()->user()->id );
            return \Datatables::of($Visits)
					->editColumn('updated_at', '<span class="lead">{!! $updated_at->diffForHumans() !!}</span>')
                    ->editColumn('session_id', '<span class="lead">{!! $session_id !!}</span>')
                    ->editColumn('action', function ($visit) {
                        $content = '';
						$content .= '<a class="btn btn-success btn-block" href="'. route('admin.visit.edit', [$visit->id]).'">Begin Visit</a> ';
                        return $content;
                    })
                    ->make(true);
        }
	}
	
	public function unassigned(Request $request){
		$Visits = Visit::select(['id', 'session_id', 'updated_at', 'assignment_user_id'])
							 ->whereNull('assignment_user_id')->get();
		if($Visits->count()){
			return response()->json(['message' => 'ok', 'payload' => $Visits]);
		}
		return response()->json(['message' => 'No Unassigned Visits', 'payload' => $Visits]);
	}
	
	
	public function assignUser(Request $request, $id, HopperVisit $hoppervisit){
		
		$visit = Visit::find($id);
		$visit->update(['assignment_user_id' => $request->assignment_user_id]);
//		$visit->assignment_user_id = $request->assignment_user_id;
//		$visit->save();		
		
		$User = $visit->user();
		$User->update(['state'=>'active']);
		
		return response()->json(['message' => 'ok', 'assignment_user_id' => $request->assignment_user_id, 'payload' => $visit]);
	}
	
	
	public function stats(Request $request){
		$hopperstats = new \App\Services\Hopper\HopperStats;
		
		$visit_stats = $hopperstats->visit_stats($request);
		
		return response()->json($visit_stats, 200);
	}
	
	public function invoice(Request $request, $id) {
        \Debugbar::disable();
		
		$visit = Visit::findOrFail($id);
		
        $data = [
			'visit' => $visit
		];
        $view = view('backend.visit.invoice', $data);

        return $view;
    }
	
	public function sheetprint(Request $request, $id) {
        \Debugbar::disable();
		
		$visit = Visit::findOrFail($id);
		
        $data = [
			'visit' => $visit
		];
        $view = view('backend.visit.print', $data);

        return $view;
    }
	
}
