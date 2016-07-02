@extends('backend.layouts.master')

@section('page-header')
<h1>
	{!! app_name() !!}
	<small>{{ trans('strings.backend.dashboard.title') }}</small>
</h1>
@endsection

@section('content')
<div class="row">
	<div class="col-xs-12 col-sm-6 col-md-6">
		<div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span>

            <div class="info-box-content">
				<span class="info-box-text">Days Left</span>
				<span class="info-box-number">{!! \Carbon\Carbon::createFromDate(2016, 6, 10)->diffInDays(\Carbon\Carbon::now() ) !!}</span>
            </div>
            <!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
	</div>
	<div class="col-xs-12 col-sm-6 col-md-6">
		<div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span>

            <div class="info-box-content">
				<span class="info-box-text">Top Graphic Ops</span>
				@foreach($TopVisits as $key => $TopVisit)
				<div class="small">{!! $key !!} <strong>{!! $TopVisit['count'] !!} </strong>visits | <strong>{!! $TopVisit['avg_difficulty'] !!}</strong> difficulty</div>
				@endforeach
            </div>
            <!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
	</div>
</div>
<div class="row">
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-check-circle"></i></span>

            <div class="info-box-content">
				<span class="info-box-text">Total Visits</span>
				<span class="info-box-number">{!! $VisitStats['totalvisits'] !!}</span>
            </div>
            <!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
	</div>
	<!-- /.col -->
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-question-circle"></i></span>

            <div class="info-box-content">
				<span class="info-box-text">Average Visit Difficulty</span>
				<span class="info-box-number">{!! $VisitStats['visit_avg_difficulty'] !!}</span>
            </div>
            <!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
	</div>
	<!-- /.col -->

	<!-- fix for small devices only -->
	<div class="clearfix visible-sm-block"></div>

	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>

            <div class="info-box-content">
				<span class="info-box-text">Checked In</span>
				<span class="info-box-number">{!! $EventSessionCheckin['checked_in'] !!}</span>
            </div>
            <!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
	</div>
	<!-- /.col -->
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-users"></i></span>

            <div class="info-box-content">
				<span class="info-box-text">Left to check in</span>
				<span class="info-box-number">{!! $EventSessionCheckin['not_checked_in'] !!}</span>
            </div>
            <!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
	</div>
	<!-- /.col -->
</div>
<div class='row'>
	<div class="col-sm-12">
		@include('backend.includes.partials.checkinlinechart')
	</div>
</div>

<div class='row'>
	<div class="col-sm-8">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Assignments</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div><!-- /.box tools -->
			</div><!-- /.box-header -->
			<div class="box-body">
				 {!! $AssignmentHTML->table(['class' => 'table responsive table-bordered table-striped', 'width' => '100%' ]) !!}
			</div><!-- /.box-body -->
		</div><!--box box-success-->
		
	</div>
	<div class="col-sm-4">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">User Behavior</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div><!-- /.box tools -->
			</div><!-- /.box-header -->
			<div class="box-body">
				 <button class="btn user-status-refresh"><i class="fa fa-refresh"></i> Trigger Refresh</button>
			</div><!-- /.box-body -->
		</div><!--box box-success-->
	</div>
</div>

@include('backend.includes.partials.userbehavior')


@endsection
@push('before-scripts-end')
 <div class="modal fade user-assignment-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">×</span></button>
			<h4 class="modal-title">Assign Visits to <span class="user-assignment-user">@{{ currentUser.name }}</span></h4>
	  </div>
      <div class="modal-body">
		  <div></div>		  
			<table class="table table-striped">
                <tbody>
				<tr>
                  <th style="width: 10px">#</th>
                  <th>Session ID</th>
                  <th style="width: 40px">Action</th>
                </tr>
				<template v-for="visit in Unassigned">
                <tr>
					<td class="lead"><span>@{{ visit.id }}</span></td>
					<td class="lead">@{{ visit.session_id }}</td>
					<td class="action"><button class="btn assign-user-to-visit" v-on:click="assignUserToVisit(currentUser, visit, $event)"><span class='btn-content'><i class="fa fa-refresh"></i> Assign</span></button></td>
                </tr>
                </template>
              </tbody>
			</table>			  
	  </div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
	    </div>
    </div>
  </div>
</div>
@endpush
@push('after-scripts-end')
<script>
    var pusher = new Pusher( "{{ env('PUSHER_MAIN_AUTH_KEY',  'your-auth-key') }}", {
        encrypted: true,
        authEndpoint: '/pusher/authorize',
        auth: {
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}'
            }
        }
    } );
</script>
 {!! $AssignmentHTML->scripts() !!}
@endpush