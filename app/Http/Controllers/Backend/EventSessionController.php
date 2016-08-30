<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Yajra\Datatables\Html\Builder; // import class on controller

use App\Services\Hopper\HopperEventSession;
use App\Services\Hopper\HopperFileEntity;
use App\Services\Hopper\HopperVisit;

use App\Services\Hopper\Contracts\HopperFileContract;

use App\Models\Hopper\EventSession;

class EventSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlbuilder, HopperFileContract $hopperfile)
    {
        //
		$hopperfile = new \App\Services\Hopper\HopperFile;
		$hopperfile->flushMasterCache();
		
        if ($request->ajax()) {
            $eventsessions = EventSession::select(['id', 'session_id', 'checked_in', 'speakers', 'onsite_phone', 'presentation_owner']);
			
            return \Datatables::of($eventsessions)
//                    ->setRowClass(function ($eventsession) {
//                        return $eventsession->checked_in === 'YES' ? 'alert-success' : '';
//                    })
                    ->editColumn('action', function ($eventsession) {
                        $content = '';
						if( ! $eventsession->checkedInBoolean() ):
                        $content .= '<a class="btn btn-success btn-block btn-xs" href="'. route('admin.eventsession.edit', [$eventsession->session_id]).'">Check-in</a> ';
						else:
						$content .= '<a class="btn btn-primary btn-block btn-xs" href="'. route('admin.eventsession.edit', [$eventsession->session_id]).'">Update</a> ';
						endif;
//                        $content .= '<a class="btn btn-info btn-xs" href="'. route('admin.eventsession.show', [$eventsession->session_id]).'">Show</a> ';
                        return $content;
                    })
                    ->make(true);
        }

//		$eventsessions = EventSession::all();
//		
//		debugbar()->info($eventsessions);

        $html = $htmlbuilder
        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'ID'])
        ->addColumn(['data' => 'session_id', 'name' => 'session_id', 'title' => 'Session ID'])
        ->addColumn(['data' => 'speakers', 'name' => 'speakers', 'title' => 'Speakers'])
        ->addColumn(['data' => 'onsite_phone', 'name' => 'onsite_phone', 'title' => 'On-site Phone'])
        ->addColumn(['data' => 'presentation_owner', 'name' => 'presentation_owner', 'title' => 'Presenation Owner'])
        ->addAction();

        $data = [
            'html' => $html
        ];
//        event(new \App\Events\Backend\Hopper\Heartbeat(auth()->user(), request()->route(), \Carbon\Carbon::now()->toIso8601String()));
        return view('backend.eventsession.index', $data);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData()
    {

        return Datatables::of(EventSession::select('*'))
                ->addColumn('operations',
                    '<a class="btn btn-primary btn-xs" href="{{ route( \'admin.eventsession.edit\', array( $id )) }}">Edit</a> '
                  . '<a class="btn btn-info btn-xs" href="{{ route( \'admin.eventsession.show\', array( $id )) }}">Show</a> '
                )
                ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(HopperEventSession $hoppereventsession)
    {
        $data = $hoppereventsession->create([]); //Placeholder
//        event(new \App\Events\Backend\Hopper\Heartbeat(auth()->user(), request()->route(), \Carbon\Carbon::now()->toIso8601String()));
        return view('backend.eventsession.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, HopperEventSession $hoppereventsession)
    {
		$this->validate($request, [
            'session_id' => 'required',
        ],
        [
            'session_id.required' => 'A Session ID is required to create a new session',
        ]);

         $eventsession = $hoppereventsession->store($request->all());
         return redirect()->back()->withFlashSuccess('Event '. $eventsession->session_id .' Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(EventSession $eventsession, HopperEventSession $hoppereventsession)
    {

        $data = [
            'EventSession' => $eventsession
        ];

        return view('backend.eventsession.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EventSession $eventsession, HopperEventSession $hoppereventsession)
    {

        $data = $hoppereventsession->edit($eventsession);
		
        return view('backend.eventsession.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
	 * @param EventSession $eventsession Description
	 * @param HopperEventSession $hoppereventsession Description
	 * @param HopperVisit $hoppervisit Description
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EventSession $eventsession, HopperEventSession $hoppereventsession, HopperVisit $hoppervisit)
    {
		
		
		
		$blind_update = FALSE;
		if($request->blind_update === "YES"){
			$blind_update = TRUE;
		}
		$request->merge( ['blind_update' => $blind_update ] );
		
		if($request->visitor_type == 'NO'){
			$request->merge(['visitor_type' => 'none']);
		}else{
			$request->merge(['visitor_type' => 'normal']);
		}
		
		if($request->simple_checkin === "YES"){
			
		}
		
		
        $messagebag = new \Illuminate\Support\MessageBag();

		if(isset($request->temporaryfilename) && !empty($request->temporaryfilename) && isset($request->filename) && ($request->temporaryfilename !== $request->filename)){
			//Rename temporary file to the new filename before anything else
			$hopperfile = new \App\Services\Hopper\HopperFile;
			$hopperfile->updateTemporary($request->temporaryfilename, $request->filename);
		}

		//If it's a blind update and things haven't changed, notify
		if($request->blind_update && ($request->currentfilename === $request->filename)){
			$messagebag->add('file_warning', "<strong class=''>You did not attach a file to update to</strong>");
			return redirect()->back()->withFlashWarning($messagebag);
		}

		//Update checkin status for the event session
        $hoppereventsession->updateCheckInStatus($request, $messagebag, $eventsession->id);
		$eventsession = $hoppereventsession->update($request->all(), $eventsession);
        $request->merge(['event_session_id' => $eventsession->id]);
        $messagebag->add('updated', "Event Session " . $eventsession->session_id . " Updated");
		
		//If we are creating a new visit or checking in
        if($request->action === 'create_visit' || $request->action === 'check_in' ){
			
            $request->merge( ['checkin_username'=> \Auth::user()->name ] );
            $visit = $hoppereventsession->createNewVisit($request, $hoppervisit);
			//If this is not a blind update, notify of new visit created.
            if( ! $blind_update ){
                $messagebag->add('create_visit', "<strong class='lead'>Visit Created</strong>");
                $messagebag->add('create_visit', "<a href='".route('admin.visit.edit', ['id' => $visit->id] )."' target='_blank'>View this visit now</a>");
            }

			if(config('hopper.print.enable', false) && config('hopper.print.timing', false) == 'before_visit' && $request->print_form !== 'NO' ){
				return redirect()->route('admin.visit.invoice', [$visit->id, 'eventsession' => 'true'])->withFlashMessage($messagebag);
			}
			return redirect()->route('admin.eventsession.index')->withFlashMessage($messagebag);

        }else{ //We are just updating the visit
            return redirect()->back()->withFlashMessage($messagebag);
        }


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
}
