<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Yajra\Datatables\Html\Builder; // import class on controller

use App\Services\Hopper\HopperEventSession;

use App\Models\Hopper\EventSession;

class EventSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlbuilder)
    {
        //
        
        if ($request->ajax()) {
            $eventsessions = EventSession::select(['id', 'session_id', 'speakers', 'created_at', 'updated_at']);
            return \Datatables::of($eventsessions)
                    ->editColumn('created_at', '{!! $created_at->diffForHumans() !!}')
                    ->editColumn('updated_at', function ($eventsession) {
                        return $eventsession->updated_at->format('Y/m/d');
                    })
                    ->editColumn('action', function ($eventsession) {
                        $content = '';
                        $content .= '<a class="btn btn-primary btn-xs" href="'. route('admin.eventsession.edit', [$eventsession->session_id]).'">Edit</a> ';
                        $content .= '<a class="btn btn-info btn-xs" href="'. route('admin.eventsession.show', [$eventsession->session_id]).'">Show</a> ';
                        return $content;
                    })
                    ->make(true);
        }
        
        
        
        $html = $htmlbuilder
        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'ID'])
        ->addColumn(['data' => 'session_id', 'name' => 'session_id', 'title' => 'Session ID'])
        ->addColumn(['data' => 'speakers', 'name' => 'speakers', 'title' => 'Speakers'])
        ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'])
        ->addColumn(['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Updated At'])
        ->addAction();
        
        $data = [
            'html' => $html
        ];
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
        //
         debugbar()->info($request->all());
         
         $this->validate($request, [
            'session_id' => 'required',
//            'filename' => 'required',
        ],
        [
            'session_id.required' => 'A Session ID is required to create a new session',
        ]);
         
//         $eventsession = EventSession::create($request->all());
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
//        $eventsession = EventSession::findOrFail($id);
        debugbar()->info($eventsession);
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
//        debugbar()->info($data);
        
        return view('backend.eventsession.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EventSession $eventsession, HopperEventSession $hoppereventsession)
    {
        $messagebag = new \Illuminate\Support\MessageBag();
        
        $hoppereventsession->updateCheckInStatus($request, $messagebag);
        
//        debugbar()->info($request->all());
        
        $eventsession = $hoppereventsession->update($request->all(), $eventsession);
        $request->merge(['event_session_id' => $eventsession->id]);
        
        $messagebag->add('updated', "Event Session " . $eventsession->session_id . " Updated");
        //If Has Filename, but no current file entity, create a new file enity
        if($request->filename && empty($request->primary_file_entity_id)){
            //Create New File Entity
            $fileentity = $hoppereventsession->createNewFileEntity($request, $eventsession);
            //Notify
            $messagebag->add('created', "File  " . $fileentity->filename . " Created");
        }elseif($request->filename && $request->primary_file_entity_id && ($request->currentfilename !== $request->filename)){
            //If Has Filename, and file entity id, and the current file name does not match the new file name (i.e. new upload)
//           debugbar()->info($eventsession->file_entity);
            //Update File Entity Referenced
            $fileentity = $hoppereventsession->updateNewFileEntity($request, $eventsession);
            //Notify
            $messagebag->add('updated', "File  " . $fileentity->filename . " Updated");
//            debugbar()->info($updated_fileentity);
        }else{
            //Do nothing
            $request->merge(['file_entity_id' => $request->primary_file_entity_id]);
        }
        
        if($request->action === 'create_visit' || $request->action === 'check_in' ){
            
            $request->merge( ['checkin_username'=> \Auth::user()->name ] );
            $visit = $hoppereventsession->createNewVisit($request);
            
            $messagebag->add('create_visit', "<strong class='lead'>Visit Created</strong>");
            $messagebag->add('create_visit', "View this visit now: " . route('admin.visit.edit', ['id' => $visit->id] ));
            
            return redirect()->route('admin.eventsession.index')->withFlashMessage($messagebag);
        }else{
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
