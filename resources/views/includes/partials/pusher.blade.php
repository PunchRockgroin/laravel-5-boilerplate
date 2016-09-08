<script id="pusherScript">
var pusher = new Pusher("{{ env('PUSHER_MAIN_AUTH_KEY',  'your-auth-key') }}", {
				encrypted: true,
				authEndpoint: '/pusher/authorize{{ $params or '' }}',
				auth: {
					headers: {
							'X-CSRF-Token': '{{ csrf_token() }}'
						}
					}
			  });
</script>