@extends('backend.layouts.master')

@section('title', app_name() .' | '. trans('visit.backend.sidebar.title') .' | '. trans('visit.backend.sidebar.index'))

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('visit.backend.admin.title') }} &raquo; {{ trans('visit.backend.admin.index') }}</small>
    </h1>
@endsection

@section('content')
	
    <div class="box">
          <div class="box-header">
            <h3 class="box-title">Enter Visit ID or Session ID</h3>
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
            <h3 class="box-title"></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div><!-- /.box tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
             {!! $html->table(['class' => 'table responsive table-bordered table-striped', 'width' => '100%' ]) !!}
        </div><!-- /.box-body -->
    </div><!--box box-success-->
	@endauth
@endsection

@push('after-scripts-end')
    {!! $html->scripts() !!}
@endpush