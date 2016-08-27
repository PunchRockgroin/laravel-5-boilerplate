<div class="row">
	<div class="col-xs-12">
		<h3>Idle</h3>
	</div>
</div>
<div class="row">
	<template v-for="user in Users | filterBy 'true' in 'idle'" track-by="uid">
		@include('backend.hopper.dashboard.partials.usercard')
	</template>
</div>

<div class="row">
	<div class="col-xs-12">
		<h3>In Visit</h3>
	</div>
</div>
<div class="row">
	<template v-for="user in Users | filterBy 'false' in 'idle'" track-by="uid">
		@include('backend.hopper.dashboard.partials.usercard')
	</template>
	
</div>

