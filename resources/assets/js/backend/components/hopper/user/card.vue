<template>
	<div class="col-md-4 col-lg-3 animated" transition="zoom">
		<!-- Widget: user widget style 2 -->
		<div class="box box-widget widget-user-2" data-mh="user-group">
			<!-- Add the bg color to the header using any of the bg-* classes -->
			<div class="widget-user-header user-group" v-bind:class="userStatusClass(user.statusclass)">
				<div class="widget-user-image">
					<img class="img-circle" v-bind:src="user.gravatar" alt="User Avatar">
				</div>
				<h4 class="widget-user-username">{{ user.name }}</h4>
				<h5 v-for="role in user.roles" class="widget-user-desc">{{ role.name }}</h5>
			</div>
			<div class="box-footer">
				<div class="padding-left">
					<!--<button class="btn btn-sm toggle-user-status" v-on:click="toggleUserStatus(user.id)"><i class="fa fa-refresh"></i> Toggle Status</button>-->
					<button class="btn btn-block btn-sm assign-user-to-visit-modal"  @click="assignThisUserToVisitModal(user)"><i class="fa fa-check"></i> Assign Visit(s)</button>
				</div>
			</div>

			<div v-if="user.assignments.length > 0" class="box-footer">
				<div class="padding-left">
					Assignments:
					<div>
						<template v-for="assignment in user.assignments">
							<span class="label label-info">{{ assignment.session_id }}</span><span> </span>
						</template>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<div class="padding-left">
					<span v-if="user.visit_count > 0">Total Visits <span class="pull-right badge bg-blue">{{ user.visit_count.aggregate }}</span></span>
				</div>
			</div>
		</div>
		<!-- /.widget-user -->
	</div>
</template>

<script>
    export default {
		data(){
			return {

			}
		}, 
		props: ['user'],
		methods: {
            addProduct() {
                this.$emit('test')
            },
			assignThisUserToVisitModal(user){
 				this.$root.Currentuser = user;
                this.$http( { url: window.hopper.routes.visit_unassigned, method: 'GET' } ).then( function ( response ) {
                   this.$root.Unassigned = response.data.payload;
                   $( '.user-assignment-modal' ).modal( 'show' );
                });                                   
			},
			userStatusClass(statusclass){
                var _elstatusclass = statusclass || 'default';
                return 'bg-'+ _elstatusclass;
            }
		},
        mounted() {
            console.log('Usercards ready.')
        }
    }
</script>
