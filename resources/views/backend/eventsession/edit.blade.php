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

    @include('backend.eventsession.partials.form')   
    
    {!! Form::close() !!}
    
    @if(isset($History))
        @include('backend.eventsession.partials.timeline')    
    @endif
	
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