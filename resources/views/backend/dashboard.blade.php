@extends('backend.layouts.master')

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('strings.backend.dashboard.title') }}</small>
    </h1>
@endsection

@section('content')
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
	
   {{-- @include('backend.includes.partials.userbehavior') --}}


@endsection

@push('after-scripts-end')
<script>
    var pusher = new Pusher("{{ env('PUSHER_MAIN_AUTH_KEY',  'your-auth-key') }}", {
                        encrypted: true,
						authEndpoint: '/pusher/authorize',
						auth: {
							headers: {
									'X-CSRF-Token': '{{ csrf_token() }}'
								}
							}
                      });
</script>
@endpush