<ul class="timeline">

    @foreach($GroupedFileHistory->reverse() as $date => $FileHistoryDay)
    <!-- timeline time label -->
    <li class="time-label">
        <span class="bg-red">
            {{ $date }}
        </span>
    </li>
    <!-- /.timeline-label -->
    @foreach($FileHistoryDay->reverse() as $FileEvent)
    <!-- timeline item -->
    <li>
        <!-- timeline icon -->
        @if($FileEvent['event'] == 'copy')
        <i class="fa fa-clone bg-blue"></i>
        @elseif($FileEvent['event'] == 'move')
        <i class="fa fa-arrow-right bg-blue"></i>
        @elseif($FileEvent['event'] == 'create')
        <i class="fa fa-plus bg-blue"></i>
        @else
        <i class="fa fa-refresh bg-blue"></i>
        @endif
        <div class="timeline-item">
            <span class="time"><i class="fa fa-clock-o"></i> {!! \Carbon\Carbon::parse($FileEvent['timestamp']['date'])->toTimeString() !!}</span>

            <h3 class="timeline-header"> <strong>{!! $FileEvent['user'] !!}</strong>  performed {!! strtoupper($FileEvent['event']) !!} {!! $FileEvent['filename'] !!} </h3>

            <div class="timeline-body">
                {!! $FileEvent['notes'] !!}
            </div>

            <div class="timeline-footer">
              
            </div>
        </div>
    </li>
    @endforeach
    <!-- END timeline item -->
    @endforeach
    <li>
        <i class="fa fa-clock-o bg-gray"></i>
    </li>
</ul>