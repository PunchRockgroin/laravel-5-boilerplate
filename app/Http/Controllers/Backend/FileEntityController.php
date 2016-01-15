<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Hopper\FileEntity;
use App\Services\Hopper\Contracts\HopperContract as Hopper;
use App\Services\Hopper\HopperFile;

class FileEntityController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $data = [];
        return view('backend.fileentity.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $data = [];
        return view('backend.fileentity.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, HopperFile $hopperfile) {
        //
        
        debugbar()->info($request->all());
        
        $this->validate($request, [
            'newfile' => 'required',
        ],
        [
            'newfile.required' => 'A file is required to create a new file'
        ]);

            
        $hopperfile->copyTemporaryNewFileToMaster($request->filename);
        
        return redirect()->back()->withFlashSuccess('File Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $data = [];
        return view('backend.fileentity.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $data = [];
        return view('backend.fileentity.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
        return redirect()->back()->with('flash_info','File Updated');
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
//        \Debugbar::disable();
        $dropboxData = [];
        $file = $request->file('file');
        if (!$file->isValid()) {
            return response()->json(
                            [
                        'success' => false,
                        'reason' => 'Invalid File Upload',
                            ], 400);
        }

        $validate = $hopperFile->validateFile($request);
        if ($validate instanceof Illuminate\Support\MessageBag) {
            return response()->json($validate->first(), 400);
        }

        $extension = $file->getClientOriginalExtension(); // getting file extension




        $uploadedFileName = $file->getClientOriginalName();
        if (!empty($request->currentFileName)) {
            $newFileName = $request->currentFileName;
            \Debugbar::info($request->currentFileName);
        } else {
            $newFileName = $uploadedFileName;
        }

        //$newFileName = $hopperFile->renameFileVersion($currentFileName, $request->nextVersion, $file->getClientOriginalExtension());
        //$upload_success = true;       
        $upload = $hopperFile->uploadToTemporary(File::get($file), $newFileName);
        debugbar()->info($upload);
        if ($upload instanceof \Exception) {
            return response()->json([
                        'success' => false,
                        'message' => $upload->getMessage()
//                        'oldfilename' => $request->currentFileName,
//                        'sessionID' => $request->sessionID,
                            ], 400);
        } else {
            // Event::fire(new \App\Events\Backend\Hopper\FileUploaded($request->except('file'), $newFileName));
            //Event::fire(new \App\Events\Backend\Hopper\FileUploaded($request->except('file'), $newFileName));
            return response()->json([
                        'success' => true,
//                        'oldFileName' => $request->currentFileName,
                        'newFileName' => $newFileName,
                        'metadata' => $upload
//                        'dropboxData' => $dropboxData,
//                        'sessionID' => $request->sessionID,
                            ], 200);
        }
    }

}
