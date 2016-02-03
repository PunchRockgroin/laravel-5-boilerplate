<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Yajra\Datatables\Html\Builder; // import class on controller

use App\Models\Hopper\Visit;
use App\Services\Hopper\HopperVisit;

use App\Services\Hopper\Contracts\HopperContract;

class VisitController extends Controller
{
    
    private $messagebag;
    
    public function __construct(\Illuminate\Support\MessageBag $messagebag)
    {
        $this->messagebag = $messagebag;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlbuilder)
    {
        
        
        if ($request->ajax()) {
            $Visits = Visit::select(['id', 'session_id', 'visitors', 'design_username', 'created_at', 'updated_at']);
            return \Datatables::of($Visits)
                    ->editColumn('created_at', '{!! $created_at->diffForHumans() !!}')
                    ->editColumn('updated_at', function ($visit) {
                        return $visit->updated_at->format('Y/m/d');
                    })
                     ->editColumn('action', function ($visit) {
                        $content = '';
                        $content .= '<a class="btn btn-primary btn-xs" href="'. route('admin.visit.edit', [$visit->id]).'">Edit</a> ';
                        $content .= '<a class="btn btn-info btn-xs" href="'. route('admin.visit.show', [$visit->id]).'">Show</a> ';
                        return $content;
                    })
                    ->make(true);
        }
        
        
        
        $html = $htmlbuilder
        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'ID'])
        ->addColumn(['data' => 'session_id', 'name' => 'session_id', 'title' => 'Session ID'])
        ->addColumn(['data' => 'visitors', 'name' => 'visitors', 'title' => 'Visitors'])
        ->addColumn(['data' => 'design_username', 'name' => 'design_username', 'title' => 'Graphic Operator'])
        ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'])
        ->addColumn(['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Updated At'])
        ->addAction();
        
        $data = [
            'html' => $html
        ];
        return view('backend.visit.index', $data);
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
        //debugbar()->info($visit->event_session->approval_brand);
        debugbar()->info($data);
        
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
            'visitors' => 'required',
            'difficulty' => 'required',
        ],
        [
            'visitors.required' => 'Please ensure you are logging your visitor',
            'difficulty.required' => 'You must choose a difficulty',
        ]);
        
        
        debugbar()->info($request->all());
        
        $hoppervisit->update($request->all(), $visit);
        
        $this->messagebag->add('updated', "Visit " . $visit->id . " for ". $visit->session_id ." Updated");
        
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
}
