<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Yajra\Datatables\Html\Builder;

use Validator;
use Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Hopper\FileEntity;
use Yajra\Datatables\Datatables;
use App\Services\Hopper\Contracts\HopperContract as Hopper;
use App\Services\Hopper\HopperFile;
use App\Services\Hopper\HopperFileEntity;

class FileEntityController extends Controller {

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
    public function index(Request $request, Builder $htmlbuilder) {        
                //   
        if ($request->ajax()) {
            $FileEntities = FileEntity::select(['id', 'filename', 'session_id', 'storage_disk', 'created_at', 'updated_at']);
            return \Datatables::of($FileEntities)
                    ->editColumn('created_at', '{!! $created_at->diffForHumans() !!}')
                    ->editColumn('updated_at', '{!! $updated_at->diffForHumans() !!}')
                    ->editColumn('action', function ($fileentity) {
                        $content = '';
                        $content .= '<a class="btn btn-primary btn-xs" href="'. route('admin.fileentity.edit', [$fileentity->id]).'">Edit</a> ';
                        $content .= '<a class="btn btn-info btn-xs" href="'. route('admin.fileentity.show', [$fileentity->id]).'">Show</a> ';
                        return $content;
                    })
                    ->make(true);
        }
        
        $html = $htmlbuilder
        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'ID'])
        ->addColumn(['data' => 'filename', 'name' => 'filename', 'title' => 'Filename'])
        ->addColumn(['data' => 'session_id', 'name' => 'session_id', 'title' => 'Session ID', 'defaultContent' => 'None'])
        ->addColumn(['data' => 'storage_disk', 'name' => 'storage_disk', 'title' => 'Storage Disk'])
        ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'])
        ->addColumn(['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Updated At'])
        ->addAction();
        
        $data = [
            'html' => $html
        ];
        event(new \App\Events\Backend\Hopper\Heartbeat(auth()->user(), request()->route(), \Carbon\Carbon::now()->toIso8601String()));
        return view('backend.fileentity.index', $data);
    }
    
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData()
    {
        
        return Datatables::of(FileEntity::select('*'))
                ->addColumn('operations',
                    '<a class="btn btn-primary btn-xs" href="{{ route( \'admin.fileentity.edit\', array( $id )) }}">Edit</a> '
                  . '<a class="btn btn-info btn-xs" href="{{ route( \'admin.fileentity.show\', array( $id )) }}">Show</a> '
                )
                ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $data = [];
        event(new \App\Events\Backend\Hopper\Heartbeat(auth()->user(), request()->route(), \Carbon\Carbon::now()->toIso8601String()));
        return view('backend.fileentity.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, HopperFileEntity $hopperfileentity) {
        //
        
        //debugbar()->info($request->all());
        
        $this->validate($request, [
            'newfile' => 'required',
            'filename' => 'required',
        ],
        [
            'newfile.required' => 'A file is required to create a new file',
            'filename.required' => 'A filename is required to create a new file'
        ]);

        
        $FileEntity = $hopperfileentity->store($request->all());
            
        
        return redirect()->back()->withFlashSuccess('File '.$FileEntity->filename.' Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $FileEntity = FileEntity::with('event_session')->findOrFail($id);
        $data = [
            'FileEntity' => $FileEntity
        ];
        event(new \App\Events\Backend\Hopper\Heartbeat(auth()->user(), request()->route(), \Carbon\Carbon::now()->toIso8601String()));
        return view('backend.fileentity.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(HopperFileEntity $hopperfileentity, $id) {
        
        $FileEntity = FileEntity::with('event_session')->findOrFail($id);                
        $data = $hopperfileentity->edit($FileEntity);
        
        event(new \App\Events\Backend\Hopper\Heartbeat(auth()->user(), request()->route(), \Carbon\Carbon::now()->toIso8601String()));
        
        return view('backend.fileentity.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HopperFile $hopperfile, HopperFileEntity $hopperfileentity, $id) {
        //debugbar()->info($request->all());
        
        $FileEntity = FileEntity::findOrFail($id);
        $hopperfileentity->update($request, $FileEntity);
        
        return redirect()->back()->with('flash_info','File '.$FileEntity->filename.' Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
        return redirect()->with('flash_danger','File Deleted');
    }

    public function upload(Request $request, HopperFile $hopperFile) {
        $next_version = 00; //Set initial next version
		$extension = '';
        //debugbar()->info($request->all());
        
        $dropboxData = [];
        $file = $request->file('file');
        if (!$file->isValid()) {
            return response()->json(
                            [
                        'success' => false,
                        'reason' => 'Invalid File Upload',
                            ], 400);
        }

        $validate = $hopperFile->validateFile($request); // Validate File
        if ($validate instanceof Illuminate\Support\MessageBag) {
            return response()->json($validate->first(), 400);
        }

        $extension = $file->getClientOriginalExtension(); // Get File Extension
        $uploadedFileName = $file->getClientOriginalName(); // Get File Name
		
        if (!empty($request->filename)) {
            $newFileName = $request->filename;
        } else {
            $newFileName = $uploadedFileName;
        }
		
        $next_version = HopperFile::getCurrentVersion($newFileName) + 1;
        if ($request->currentFileName !== 'false' && $request->next_version !== 'false'){
            $newFileName = $hopperFile->renameFileVersion($request->currentFileName, $request->next_version, $file->getClientOriginalExtension());
            $next_version = HopperFile::getCurrentVersion($newFileName) + 1;
        }
//        $eventsession = false;
//		if(!empty($request->session_id)){
//			$eventsession = \App\Models\Hopper\EventSession::where('session_id', $request->session_id)->first();
//		}
        $upload = $hopperFile->uploadToTemporary(File::get($file), $newFileName);
        
        if ($upload instanceof \Exception) {
            return response()->json([
                        'success' => false,
                        'message' => $upload->getMessage(),
                            ], 400);
        } else {
//			if($eventsession){
//				//$filename, $type, $entity, $event, $icon = 'file', $class = 'blue', $links = []
//				event(new \App\Events\Backend\Hopper\FileOperation(
//						$uploadedFileName, 
//						'Event Session',
//						$eventsession,
//						'uploaded file <strong>'.$uploadedFileName.'</strong> for <strong>$1</strong>',
//						'cloud-upload',
//						'green',
//						[
//							
//							'link' => ['admin.eventsession.edit', $eventsession->session_id, [ $eventsession->session_id ] ]
//						]
//					)
//				);	
//			}

            return response()->json([
                        'success' => true,
                        'newFileName' => $newFileName,
                        'temporaryfilename' => $newFileName,
                        'next_version' => str_pad($next_version, 2, '0', STR_PAD_LEFT),
                        'metadata' => $upload,
                            ], 200);
        }
    }
	
	public function nextVersion(Request $request, HopperFile $hopperFile) {
		$data = [];
//		if($request->currenFileName){
			try {
				$extension = pathinfo($request->currentFileName, PATHINFO_EXTENSION);
				$newFileName = $hopperFile->renameFileVersion($request->currentFileName, $request->next_version, $extension);
				$data['newfilename'] = $newFileName;
				return response()->json(['message' => 'ok', 'payload' => $data]);
			} catch ( Exception $exc ) {
				$data['message'] = $exc->getTraceAsString();
				return response()->json(['message' => 'error', 'payload' => $data]);
			}
//		}
//		return response()->json(['message' => 'error', 'payload' => 'No Data']);
	}

}
