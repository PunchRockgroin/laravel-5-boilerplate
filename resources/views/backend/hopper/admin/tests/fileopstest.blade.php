@extends('backend.layouts.app')

@section ('title',  app_name() . ' | Administration' )

@section('page-header')
    <h1>
        {{ app_name() }}
        <small>Administration</small>
    </h1>
@endsection

@section('breadcrumbs')
    <li><a href="{!!route('admin.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li class="active">File Ops Testing</li>
@endsection

@section('content')
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{ app_name() }} Administration</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
           
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    
@endsection