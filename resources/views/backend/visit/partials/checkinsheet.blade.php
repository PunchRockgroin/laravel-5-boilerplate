<section class="">
    <!-- title row -->
    <div class="row">
        <div class="col-xs-12 col-sm-8">
            <img src="{!! config('hopper.print.logo.dataURI') !!}" class="img-responsive" alt="" style='width:175px;'/>
        </div><!-- /.col -->
        <div class="col-xs-12 col-sm-4">
            Visit Date/Time: {!! $visit->created_at !!}
            <h4>Visit ID: <strong>{!! $visit->id !!}</strong></h4>
            <h4>Session ID: <strong>{!! $visit->event_session->session_id !!}</strong></h4>
        </div>
    </div>
    <hr>

    <div class="row" style='height:130px'>
        <div class="col-xs-12 col-sm-8">
            <h4><strong>Speaker check-in form</strong></h4>
            <div class="">Session ID: <strong>{!! $visit->event_session->session_id !!}</strong></div>
			@if($visit->working_filename)
            <div class="">Visit filename: <strong>{!! $visit->working_filename !!}</strong></div>
			@endif
			@if($visit->filename_uploaded)
            <div class="">Name of file brought in: <strong>{!! $visit->filename_uploaded !!}</strong></div>
			@endif
			@if($visit->visitor_type)
			<div class="">Visit Type: <strong>{!! ucwords($visit->visitor_type) !!}</strong></div>
			@endif
        </div>
        <div class="col-xs-12 col-sm-4">
           
            
        </div>
    </div>
    <h4>Graphics Notes</h4>
    <div class="row">
        <div class="col-xs-12 table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 80px">Day</th>
                        <th style="width: 80px">Time</th>
                        <th style="width: 200px">Who visited (client name)</th>
                        <th style="width: 180px">Designer</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="height: 200px">
                        <td>{{ $visit->created_at->format('d-m-y') }}</td>
                        <td>{{ $visit->created_at->format('h:m:s') }}</td>
                        <td>
							@if($visit->visitor_type !== 'none')
							{{ $visit->visitors }}
							@else
							{!! ucwords($visit->visitor_type) !!}
							@endif
						</td>
                        <td>{{ $visit->design_username }}</td>
                        <td>{{ $visit->design_notes }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <hr style="border-style: dashed;">
    <br />
    <div class="row" style='margin-bottom: 20px;'>
        <div class="col-xs-3">
            <img src="{!! config('hopper.print.logo.dataURI') !!}" class="img-responsive" alt="" style='width:175px; '/>
        </div><!-- /.col -->
    </div>
    <div class="row" style='margin-bottom: 20px; height: 130px;'>
        <div class="col-xs-12 col-sm-6">
            <div class="">Session ID: <strong>{!! $visit->event_session->session_id !!}</strong></div>
            
        </div>
        <div class="col-xs-12 col-sm-6">
            <h5 style='margin-top:0;'>To Find your Presentation</h5>
			<div class='small'>	
			<ol>
                <li>Open the folder Presentations on the PC Desktop.</li>
                <li>Search for your presentation by using your Session ID #.</li>
                <li>If you cannot find your presentation in the Presentations folder, perform the same search in the Backup presentations folder on the Desktop.</li>
            </ol>
            <p>If you still cannot find your presentation in either folder or you are having computer issues, please see the room attendant.</p>
			</div>
        </div>
    </div>
</section><!-- /.content -->