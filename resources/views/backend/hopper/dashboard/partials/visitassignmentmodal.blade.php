 <div class="modal fade visit-assignment-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">×</span></button>
			<h4 class="modal-title">Assign User to <span class="user-assignment-user">@{{ currentVisit.sessionId }}</span></h4>
	  </div>
      <div class="modal-body">
		  <div></div>		  
			<table class="table table-striped">
                <tbody>
				<tr>
                  <th style="width: 10px">#</th>
                  <th>User</th>
                  <th style="width: 40px">Action</th>
                </tr>
				<template v-for="user in Users">
                <tr>
					<td class="lead"><span>@{{ user.id }}</span></td>
					<td class="lead">@{{ user.name }}</td>
					<td class="action"><button class="btn assign-user-to-visit" v-on:click="assignUserToVisit(user, currentVisit, $event)"><span class='btn-content'><i class="fa fa-refresh"></i> Assign</span></button></td>
                </tr>
                </template>
              </tbody>
			</table>			  
	  </div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
	    </div>
    </div>
  </div>
</div>