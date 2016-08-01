<ul class="timeline">

    @foreach($History->reverse() as $date => $HistoryDay)
        <!-- timeline time label -->
        <li class="time-label">
            <span class="bg-red">
                {{ $date }}
            </span>
        </li>
        <!-- /.timeline-label -->
        @foreach($HistoryDay->reverse() as $HistoryEvent)
        <!-- timeline item -->
        <li>
            <!-- timeline icon -->
            @if($HistoryEvent['event'] == 'copy')
            <i class="fa fa-clone bg-blue"></i>
            @elseif($HistoryEvent['event'] == 'move')
            <i class="fa fa-arrow-right bg-blue"></i>
            @elseif($HistoryEvent['event'] == 'create')
            <i class="fa fa-plus bg-blue"></i>
            @else
            <i class="fa fa-refresh bg-blue"></i>
            @endif
            <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> {!! \Carbon\Carbon::parse($HistoryEvent['timestamp']['date'])->toTimeString() !!}</span>

                <h3 class="timeline-header"> <strong>{!! $HistoryEvent['user'] !!}</strong>  performed {!! strtoupper($HistoryEvent['event']) !!} {!! $HistoryEvent['filename'] !!} </h3>

                <div class="timeline-body">
                    {!! $HistoryEvent['notes'] !!}
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