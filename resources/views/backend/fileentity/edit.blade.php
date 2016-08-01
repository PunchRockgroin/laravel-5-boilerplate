@extends('backend.layouts.master')

@section('title', app_name() .' | '. trans('fileentity.backend.admin.title') .' | '. trans('fileentity.backend.admin.edit') .' '. $FileEntity->filename)

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('fileentity.backend.admin.title') }} &raquo; {{ trans('fileentity.backend.admin.edit') }} {{ $FileEntity->filename }}</small>
    </h1>
@endsection

@section('content')
    {!! Form::model($FileEntity, ['route' => array('admin.fileentity.update', $FileEntity->id),  'role' => 'form', 'method' => 'patch']) !!}
    {!! Form::hidden('behavior', 'edit') !!} 
    
    

    @include('backend.fileentity.partials.file_upload')
    @include('backend.fileentity.partials.form')
    {!! Form::close() !!}
    <div class="row">
        <div class="col-sm-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Event Session</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box tools -->
                </div><!-- /.box-header -->
                <div class="box-body">
                    @if(isset($EventSession))
                     {!! $EventSession->session_id !!}
                    @else
                        <div class="alert alert-warning">Not linked to any Event Session</div>
                    @endif 
                </div><!-- /.box-body -->
            </div><!--box box-success-->
        </div>
        <div class="col-sm-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Visits</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box tools -->
                </div><!-- /.box-header -->
                <div class="box-body">
                    @if(isset($Visit))
                     {!! $Visit->id !!}
                    @else
                        <div class="alert alert-warning">Not linked to any Visit</div>
                    @endif 
                </div><!-- /.box-body -->
            </div><!--box box-success-->
        </div>
    </div>
    @if($FileEntity->history)
        
        @include('backend.fileentity.partials.timeline', $FileEntity->history)
    
    @endif
    
@endsection