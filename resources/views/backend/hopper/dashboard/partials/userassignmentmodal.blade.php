 <div class="modal fade user-assignment-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">×</span></button>
			<h4 class="modal-title">Assign Visits to <span class="user-assignment-user">@{{ currentUser.name }}</span></h4>
	  </div>
      <div class="modal-body">
		  <div></div>		  
			<table class="table table-striped">
                <tbody>
				<tr>
                  <th style="width: 10px">#</th>
                  <th>Session ID</th>
                  <th style="width: 40px">Action</th>
                </tr>
				<template v-for="visit in Unassigned">
                <tr>
					<td class="lead"><span>@{{ visit.id }}</span></td>
					<td class="lead">@{{ visit.session_id }}</td>
					<td class="action"><button class="btn assign-user-to-visit" v-on:click="assignUserToVisit(currentUser, visit, $event)"><span class='btn-content'><i class="fa fa-refresh"></i> Assign</span></button></td>
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