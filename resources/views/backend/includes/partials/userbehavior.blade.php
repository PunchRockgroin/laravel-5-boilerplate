<div class="box box-success">
	<div class="box-header with-border">
		<h3 class="box-title">User Behavior</h3>
		<div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div><!-- /.box tools -->
	</div><!-- /.box-header -->
	<div class="box-body">
		<div class="row">
			<div class="col-xs-12">
				<h3>In Visit</h3>
			</div>
		</div>
		<div class="row">

			<template v-for="user in inVisit">
				<div class="col-md-4 col-lg-3" class="animated" transition="zoom">
					<!-- Widget: user widget style 1 -->
					<div class="box box-widget widget-user-3" >
						<!-- Add the bg color to the header using any of the bg-* classes -->
						<div class="widget-user-header bg-@{{ user.statusclass }}">
							<h3 class="widget-user-username">@{{ user.name }}</h3>
							<h5 class="widget-user-desc"><span v-for="role in user.roles">@{{ role.name }}</span> </h5>
						</div>
						<div class="box-footer">
							<div class="padding-left">
								<p>In visit with <strong>@{{ user.visit.session_id }}</strong></p>
								<p>Last pinged: @{{ dateFromNow(user.heartbeat.timestamp) }}</p>
							</div>
						</div>
						<div class="box-footer no-padding">
							<ul class="nav nav-stacked">
								<li><a href="#">Total Visits <span class="pull-right badge bg-blue">31</span></a></li>
							</ul>
						</div>
					</div>
					<!-- /.widget-user -->
				</div>
			</template>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<h3>Idle/Offline</h3>
			</div>
		</div>
		<div class="row">

			<template v-for="user in idleUsers">
				<div class="col-md-4 col-lg-3" class="animated" transition="zoom">
					<!-- Widget: user widget style 1 -->
					<div class="box box-widget widget-user-3" >
						<!-- Add the bg color to the header using any of the bg-* classes -->
						<div class="widget-user-header bg-@{{ user.statusclass }}">
							<h3 class="widget-user-username">@{{ user.name }}</h3>
							<h5 class="widget-user-desc"><span v-for="role in user.roles">@{{ role.name }}</span> </h5>
						</div>
						<div class="box-footer">
							<div class="padding-left">
								<p>Last pinged: @{{ dateFromNow(user.heartbeat.timestamp) }}</p>
							</div>
						</div>
						<div class="box-footer no-padding">
							<ul class="nav nav-stacked">
								<li><a href="#">Total Visits <span class="pull-right badge bg-blue">31</span></a></li>
							</ul>
						</div>
					</div>
					<!-- /.widget-user -->
				</div>
			</template>
		</div>
	</div><!-- /.box-body -->
</div><!--box box-success-->