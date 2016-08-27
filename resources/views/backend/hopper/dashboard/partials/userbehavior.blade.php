<div class="row">
	<div class="col-xs-12">
		<h3>Idle</h3>
	</div>
</div>
<div class="row">
	<div v-if="idleGraphicOperators.length == 0">
		<div class="col-xs-12">
			<div class="alert alert-info">
			<h4><i class="icon fa fa-info"></i> Alert!</h4>
			There are no idle graphic operators
		  </div>
		</div>
	</div>
	<template v-for="user in idleGraphicOperators" track-by="uid">
		@include('backend.hopper.dashboard.partials.usercard')
	</template>
</div>

<div class="row">
	<div class="col-xs-12">
		<h3>Assigned</h3>
	</div>
</div>
<div class="row">
	<template v-for="user in activeGraphicOperators" track-by="uid">
		@include('backend.hopper.dashboard.partials.usercard')
	</template>
</div>

