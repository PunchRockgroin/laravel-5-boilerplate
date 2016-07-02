<div class="col-md-4 col-lg-3 " class="animated" transition="zoom">
	<!-- Widget: user widget style 2 -->
	<div class="box box-widget widget-user-2" data-mh="user-group">
		<!-- Add the bg color to the header using any of the bg-* classes -->
		<div class="widget-user-header user-group bg-@{{ user.statusclass }}">
			<div class="widget-user-image">
				<img class="img-circle" v-bind:src="user.gravatar" alt="User Avatar">
			</div>
			<h4 class="widget-user-username">@{{ user.name }}</h4>
			<h5 v-for="role in user.roles" class="widget-user-desc">@{{ role.name }}</h5>
		</div>
		<div class="box-footer">
			<div class="padding-left">
				<button class="btn btn-sm toggle-user-status" v-on:click="toggleUserStatus(user.id)"><i class="fa fa-refresh"></i> Toggle Status</button>
				<button class="btn btn-sm assign-user-to-visit-modal" v-on:click="assignUserToVisitModal(user)"><i class="fa fa-refresh"></i> Assign Visit(s)</button>
			</div>
		</div>
		<div class="box-footer">
			<div class="padding-left">
				Assignments:
				<div v-if="user.assignments.length > 0">
					  <template v-for="assignment in user.assignments">
						  <span class="label label-info">@{{ assignment.session_id }}</span> 
					  </template>
				</div>
				<div v-else>
					<span class="label label-default">No Assignments</span>
				</div>
			</div>
		</div>
		<div class="box-footer">
			<div class="padding-left">
				Total Visits <span class="pull-right badge bg-blue">@{{ user.visit_count.aggregate }}</span>
			</div>
		</div>
	</div>
	<!-- /.widget-user -->
</div>