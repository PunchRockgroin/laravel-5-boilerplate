@extends('backend.layouts.master')

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('strings.backend.dashboard.title') }}</small>
    </h1>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('strings.backend.dashboard.welcome') }} {!! access()->user()->name !!}!</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div><!-- /.box tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			 

            
        </div><!-- /.box-body -->
    </div><!--box box-success-->
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