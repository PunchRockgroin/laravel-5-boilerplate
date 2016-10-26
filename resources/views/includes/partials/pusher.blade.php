<script id="pusherScript">
var pusher = new Pusher("{{ env('PUSHER_MAIN_AUTH_KEY',  'your-auth-key') }}", {
				encrypted: true,
				authEndpoint: '/pusher/authorize?route={{ \Request::route()->getName() }}',
				auth: {
					headers: {
							'X-CSRF-Token': '{{ csrf_token() }}'
						}
					}
			  });

var verifyID = function(id){
	if( id.replace('-client','')  === '{{ auth()->user()->id }}' ){
		return true;
	}
	return false;
}

var hopper_presence_channel = pusher.subscribe( 'presence-hopper_channel' );
var hopper_channel = pusher.subscribe('private-hopper_channel');

hopperVue.heartbeatListen();

hopper_presence_channel.bind('pusher:subscription_succeeded', function(members) {
		
		console.log(members);
		
		members.each(function(member) {
		  // for example:
		  if( verifyID(member.id) ){
			  
			  hopperVue.hopperClient = true;
			  hopperVue.notifyHopperClient(pusherData);
		  }
		});
	  });
hopper_presence_channel.bind('pusher:member_added', function(member) {

	console.log(member);
	
	if( verifyID(member.id) ){
		  //console.log('Awaken');
		  hopperVue.hopperClient = true;
		  hopperVue.notifyHopperClient(pusherData);
	  }
  })

hopper_presence_channel.bind('pusher:member_removed', function(member) {
  console.log(member);
  // for example:
  if( verifyID(member.id) ){
		//console.log('Sleep');
		hopperVue.hopperClient = false;
//  	    hopperVue.notifyHopperClient(pusherData);
	}
});
	

</script>