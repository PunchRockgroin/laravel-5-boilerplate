@extends('backend.layouts.master')

@section('title', app_name() .' | '. trans('visit.backend.sidebar.title') .' | '. trans('visit.backend.admin.invoice' . ' | ' . trans('visit.backend.name') .' '. $visit->id ))

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('visit.backend.admin.title') }} &raquo; {{ trans('visit.backend.admin.invoice') }} &raquo; {{ trans('visit.backend.name') }} {{ $visit->id }}</small>
    </h1>
@endsection

@section('content')
<div class="pad margin no-print">
	
	<div class="row">
        <div class="col-xs-12 col-sm-6">
			<a href="{!! route('admin.visit.print', $visit->id) !!}" target="_blank" class="btn btn-default btn-lg btn-block"><i class="fa fa-print"></i> Print</a>
        </div>
        <div class="col-xs-12 col-sm-6">
			<a href="{!! route('admin.visit.index') !!}" class="btn btn-info btn-lg btn-block"><i class="fa fa-tachometer"></i> Back to Visit Dashboard</a>
        </div>
</div>
	
</div>
<section class='invoice'>
		@include('backend.visit.partials.checkinsheet') 
<div class="row no-print">
        <div class="col-xs-12">
			<a href="{!! route('admin.visit.print', $visit->id) !!}" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
        </div>
</div>
</section>
@endsection

