@extends('backend.layouts.master')

@section('title', app_name() .' | '. trans('fileentity.backend.admin.title') .' | '. trans('fileentity.backend.admin.create'))

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('fileentity.backend.admin.title') }} &raquo; {{ trans('fileentity.backend.admin.create') }}</small>
    </h1>
@endsection

@section('content')
    {!! Form::open(['route' => 'admin.fileentity.store', 'role' => 'form']) !!}
    {!! Form::hidden('behavior', 'create') !!} 
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div><!-- /.box tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            
        </div><!-- /.box-body -->
    </div><!--box box-success-->
    @include('backend.fileentity.partials.file_upload')
    
    @include('backend.fileentity.partials.form')
    
    
    
    {!! Form::close() !!}
@endsection