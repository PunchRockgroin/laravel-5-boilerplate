<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
		@if(config('hopper.use_queue', false))
			<strong>Using queue driver</strong>
		@endif
        <!--<a href="http://laravel-boilerplate.com" target="_blank">{{ trans('strings.backend.general.boilerplate_link') }}</a>-->
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; {!! date('Y') !!} <a href="#">{!! app_name() !!}</a>.</strong> {{ trans('strings.backend.general.all_rights_reserved') }}
</footer>