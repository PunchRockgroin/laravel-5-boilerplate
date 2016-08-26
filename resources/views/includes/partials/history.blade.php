<ul class="timeline">
	@foreach ($history as $historyItem)
		{!! history()->buildItem($historyItem) !!}
	@endforeach
	<li>
	  <i class="fa fa-clock-o bg-gray"></i>
    </li>
</ul>
@if($paginate)
	{{ $history->links() }}
@endif