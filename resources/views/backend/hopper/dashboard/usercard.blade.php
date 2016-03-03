<div class="col-md-4 col-lg-3">
    <!-- Widget: user widget style 1 -->
    <div class="box box-widget widget-user-3 animated zoomIn" >
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header bg-{{ $eluser['statusclass'] }}">
            <div class="widget-user-image">

            </div>
            <!-- /.widget-user-image -->
            <h3 class="widget-user-username">{{ $eluser->name }}</h3>
            <h5 class="widget-user-desc">{{ $eluser['roles']->implode('name', ', ') }}</h5>
        </div>
        <div class="box-footer">
            <div class="padding-left">
                @if(isset($eluser['heartbeat']['timestamp']))
                <p><strong>Currently in Visit</strong></p>
                <p>
                    Visit ID: 1<br />
                    Visit ID: 1<br />
                    Visit ID: 1<br />
                </p>
                <p>{!! \Carbon\Carbon::parse($eluser['heartbeat']['timestamp'])->diffForHumans() !!}</p>
                @else
                    <p><strong>Idle or elsewhere</strong></p>
                @endif
            </div>
        </div>
        <div class="box-footer no-padding">
            <ul class="nav nav-stacked">
                <li><a href="#">Visits <span class="pull-right badge bg-blue">31</span></a></li>
            </ul>
        </div>
    </div>
    <!-- /.widget-user -->
</div>