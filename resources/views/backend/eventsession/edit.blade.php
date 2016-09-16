@extends('backend.layouts.master')

@section('title', app_name() .' | '. trans('eventsession.backend.admin.title') .' | '. trans('eventsession.backend.admin.edit'))

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('eventsession.backend.admin.title') }} &raquo; {{ $eventsession->session_id }} &raquo; {{ trans('eventsession.backend.admin.edit') }}</small>
    </h1>
@endsection

@section('content')
    {!! Form::model($eventsession, ['route' => array('admin.eventsession.update', $eventsession->session_id), 'role' => 'form', 'method' => 'patch']) !!}
    {!! Form::hidden('behavior', 'update_eventsession') !!} 
	
	@include('backend.eventsession.partials.file_entity')

    <div class="row">
		<div class="col-sm-8">
			@include('backend.eventsession.partials.form')   
		</div>
		<div class="col-sm-4">
			<div class="box box-success">
				<div class="box-header with-border">
					<h3 class="box-title">Session visits</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					@if($visits->count() )
					<ul class="timeline">
						
					@foreach($visits as $visit)
						<?php 
							$theVisit = collect([$visit->toArray()]);
							
							$history = collect(\App\Models\History\History::with('user', 'type')->where('entity_id', $visit->id)->latest()->get()->toArray());
							//debugbar()->info($theVisit);
							$totalVisit = $history->merge($theVisit)->sortByDesc('updated_at');			
							//debugbar()->info($totalVisit);
							foreach($totalVisit as $elVisit):
								$elVisit = collect($elVisit);
								debugbar()->info($elVisit); ?>
								
								@if( !empty( $elVisit['type_id'] ) )
									<li>
										<i class="fa fa-{{ $elVisit['icon'] }} {{ $elVisit['class'] }}"></i>
										<div class="timeline-item">
											<span class="time"><i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($elVisit['created_at'])->diffForHumans() }}</span>
											<h3 class="timeline-header no-border"><strong>{{ $elVisit['user']['name'] }}</strong> {!! history()->renderDescription($elVisit['text'], $elVisit['assets']) !!}</h3>
										</div>
									</li>
								@else
									<li>
										<i class="fa fa-users bg-blue"></i>
										<div class="timeline-item">
										  <span class="time"><i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($elVisit['created_at'])->diffForHumans() }}</span>
										  <h3 class="timeline-header"><a target="_blank" href="{{ route('admin.visit.edit', $elVisit['id'] ) }}">Visit</a></h3>
										  <div class="timeline-body">
											  @if($elVisit['design_notes'])
											  {{ $elVisit['design_notes']  }}
											  @else 
											  <span class='label label-info'>No notes associated</span>
											  @endif
										  </div>
										  <div class="timeline-footer">
											  @if($elVisit['blind_update'])
											  <div><span class="label label-info">Blind Update</span></div>
											  @else
											  <div>Visited by: <strong>{{ $elVisit['visitors'] or 'Unknown' }}</strong></div>
											  @endif
											  <a href="{{ route('admin.visit.edit', $elVisit['id'] ) }}" target="_blank" class="btn btn-primary btn-xs">Learn more</a>
										  </div>
										</div>
									</li>
								@endif
								
						<?php	endforeach;  ?>
							
					@endforeach	
					<li>
						<i class="fa fa-clock-o bg-gray"></i>
					 </li>
					</ul>
					@else
					<div class="alert alert-info">There are no visits associated with this Event Session </div>
					@endif
				</div>
			</div>
		</div>s
		</div>
	</div>
    
    {!! Form::close() !!}
    
   
	
@endsection


@section('after-scripts-end')
@include('includes/partials/pusher', ['params'=>'?originator='.$eventsession->session_id])
<script>
	
	var hopper_presence_eventsession_channel = pusher.subscribe( 'presence-hopper_eventsession_channel' );
	var hopper_channel = pusher.subscribe('private-hopper_channel');
	
	var pusherData = {
		'user_id' : '{{ auth()->user()->id . '-server' }}',
		'session_id' : '{{ $eventsession->session_id }}',
		'filename' : $('input#currentfilename').val(),
		'next_version' : '{{ $next_version_filename }}'
	};
	
	var verifyID = function(id){
		if( id.replace('-client','')  === '{{ auth()->user()->id }}' ){
			return true;
		}
		return false;
	}
//	console.log(pusherData);
	
	hopper_presence_eventsession_channel.bind('pusher:subscription_succeeded', function(members) {
		members.each(function(member) {
		  // for example:
		  if( verifyID(member.id) ){
//			  console.log('Hopper Client is listening');
			  hopperVue.hopperClient = true;
			  hopperVue.notifyHopperClient('event_session', pusherData);
		  }
		});
	  });
	hopper_presence_eventsession_channel.bind('pusher:member_added', function(member) {

		if( verifyID(member.id) ){
			  //console.log('Awaken');
			  hopperVue.hopperClient = true;
			  hopperVue.notifyHopperClient('event_session', pusherData );
		  }
	  })

	  hopper_presence_eventsession_channel.bind('pusher:member_removed', function(member) {
		// for example:
		if( verifyID(member.id) ){
			  //console.log('Sleep');
			  hopperVue.hopperClient = false;
			  
		  }
	  });
		
	hopper_channel.bind('client-event', function(data){
		
		if( ! verifyID(data.id) || data.session_id !== '{{ $eventsession->session_id }}'){
			return;
		}
		
		switch(data.event){
			case 'event_session_file_removed':
				$( 'input[name="filename"]' ).val( false );
				$( 'input[name="using_hopper_client"]' ).val('false');
				$( '.file-update-section, #usingHopperClient' ).addClass( 'hidden' );
				break;
			case 'event_session_file_updated':
				swal( "Neat!", "Your Hopper Client uploaded "+data.payload.uploaded_filename+" and renamed it to "+data.payload.filename+" in working", "success" );
				$( '.file-update-section, #usingHopperClient' ).removeClass( 'hidden' );
				$.each( data.payload, function ( i, value ) {
					$( 'input[name="' + i + '"]' ).val( value );
				} );
				break;
			default: 
				break;
		}
		
			
	});
	
</script>
@endsection