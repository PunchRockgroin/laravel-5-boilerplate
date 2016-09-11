@extends('backend.layouts.master')

@section('title', app_name() .' | '. trans('visit.backend.sidebar.title') .' | '. trans('visit.backend.sidebar.index'))

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('visit.backend.admin.title') }} &raquo; {{ trans('visit.backend.admin.index') }}</small>
    </h1>
@endsection

@section('content')

	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-exclamation"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Reminder!</span>
              <span class="info-box-number">Save and save often</span>
            </div>
            <!-- /.info-box-content -->
          </div>
        </div>
        <!-- /.col -->
	</div>
<div class="row">
	<div class="col-sm-12">
			 <div class="box">
          <div class="box-header">
            <h3 class="box-title">Find a Visit ID or Session ID</h3>
          </div><!-- /.box-header -->
			<div class="box-body">
					{!! Form::open( [ 'route' => 'admin.visit.find' ] ) !!}
					{!! Form::token() !!}
					<div class='input-group'>
						{!! Form::text('visit_id', null, $attributes = array('class'=>'form-control input-lg', 'placeholder'=>'Enter visit ID or Session ID')) !!}
						<span class="input-group-btn">
						  <button class="btn btn-lg btn-success" type="submit">Go!</button>
						</span>

					</div>
					<p class="help-block">You can find the Visit ID in the top right of the check-in sheet. You may also enter a Session ID, which will return the last visit for that Session ID.</p>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="box box-success">
				<div class="box-header with-border">
					<h3 class="box-title">{{ !empty(config('hopper.use_assignments')) ? 'My Assignments' : 'Visits Ready' }}</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<table id='assignedVisitsTable' class="table responsive table-bordered table-striped" width='100%'>
						<thead>
						<th>ID</th>
							<th width='150px'>Updated At</th>
							<th>Session ID</th>
							<th width='100px'>Action</th>
						</thead>
					</table>
				</div><!-- /.box-body -->
			</div><!--box box-success-->
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6">
		<div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span>

            <div class="info-box-content">
				<span class="info-box-text">Days Left</span>
				@if(env('HOPPER_END_DATE', false))
				<span class="info-box-number">{!! \Carbon\Carbon::parse(env('HOPPER_END_DATE', false))->diffInDays(\Carbon\Carbon::now() ) !!}</span>
				@else
				<span class="info-box-number">I Don't Know</span>
				@endif
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
              <span class="info-box-text">Your Total Visits</span>
              <span class="info-box-number">{!! $VisitStats['count'] or 'N/A' !!}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
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
            <span class="info-box-icon bg-red"><i class="fa fa-question-circle"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Your Average Visit Difficulty</span>
              <span class="info-box-number">{!! $VisitStats['avg_difficulty'] or 'N/A' !!}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
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

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-6 col-sm-6 col-xs-12">
			<div class="box box-success">
				<div class="box-header with-border">
					<h3 class="box-title">Graphic Ops</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>

				</div>
				<div class="box-body">
					<canvas id="graphicOpsPieChart" data-legend-target="#graphicOpsPieChartLegend" data-variable="graphicOpsPie" class="pieChart"></canvas>
					<div id="revRecPieChartLegend"></div>
				</div>
			</div>
        </div>
        <!-- /.col -->
      </div>

	@permission('view-access-management')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">All Visits</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div><!-- /.box tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<table id='allVisitsTable' class="table responsive table-bordered table-striped" width='100%'>
				<thead>
				<th>ID</th>
				<th>Session ID</th>
				<th>Visitors</th>
				<th>Assignment</th>
				<th>Graphic Operator</th>
				<th>Created At</th>
				<th>Updated At</th>
				<th>Action</th>
				</thead>
			</table>
        </div><!-- /.box-body -->
    </div><!--box box-success-->
	@endauth
@endsection

@section('after-scripts-end')
@include('includes/partials/pusher')
<script>
	$(function () {
    var hopperChannel;
	$('#assignedVisitsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! route('admin.visit.myassignments') !!}",
        columns: [
			{data: 'id', name: 'id'},
			{data: 'updated_at', name: 'updated_at'},
            {data: 'session_id', name: 'session_id'},
			{data: 'action', name: 'action', orderable: false, searchable: false}
        ],
		"order": [[ 0, "desc" ]]
    });
	@permission('view-access-management')
	$('#allVisitsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! route('admin.visit.datatable') !!}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'session_id', name: 'session_id'},
            {data: 'visitors', name: 'visitors'},
			      {data: 'assignment_user_id', name: 'assignment_user_id'},
            {data: 'design_username', name: 'design_username'},
            {data: 'created_at', name: 'created_at'},
            {data: 'updated_at', name: 'updated_at'},
			{data: 'action', name: 'action', orderable: false, searchable: false}
        ],
		"order": [[ 0, "desc" ]]
    });
	@endauth
	var updateTables = function(){
		$('#assignedVisitsTable').DataTable().ajax.reload();
		@permission('view-access-management')
			$('#allVisitsTable').DataTable().ajax.reload();
		@endauth
		console.log('updated');
	};
    if ('undefined' !== typeof pusher) {
        hopperChannel = pusher.subscribe('hopper_channel');
        hopperChannel.bind('user_status', function(data) {
            if(data.message === 'update'){
               updateTables();
            }
        });
        hopperChannel.bind('visit_status', function(data) {
            if(data.message === 'update'){
                updateTables();
            }
        });
    }
});
</script>
@endsection
