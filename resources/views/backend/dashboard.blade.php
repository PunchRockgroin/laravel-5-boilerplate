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
	<div class="col-sm-9">
		@include('backend.includes.partials.checkinlinechart')
	</div>
  <div class="col-sm-3">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('history.backend.recent_history') }}</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div><!-- /.box tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            {!! history()->render() !!}
        </div><!-- /.box-body -->
    </div><!--box box-success-->
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

@include('backend.hopper.dashboard.partials.userbehavior')

@endsection
@section('before-scripts-end')
@include('backend.hopper.dashboard.partials.userassignmentmodal')
@include('backend.hopper.dashboard.partials.visitassignmentmodal')
@endsection
@section('after-scripts-end')
@include('includes/partials/pusher')
 {!! $AssignmentHTML->scripts() !!}
@endsection
