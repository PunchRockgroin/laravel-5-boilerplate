 <template>
     <div>
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
            <user-card v-for="user in idleGraphicOperators" track-by="uid" :user="user"></user-card>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <h3>Assigned</h3>
            </div>
        </div>
        <div class="row">
            <user-card v-for="user in activeGraphicOperators" track-by="uid" :user="user"></user-card>
        </div>
        <user-assignment-modal v-bind:Currentuser="Currentuser" v-bind:Unassigned="Unassigned" ></user-assignment-modal> 
        <visit-assignment-modal v-bind:Currentvisit="Currentvisit" v-bind:Users="Users" ></visit-assignment-modal> 
    </div>
</template>


<script>
    export default {
		data(){
			return {
                
			}
		},
		props: ['Users', 'Currentuser', 'Currentvisit', 'Unassigned'],
        computed: {
            idleGraphicOperators: function () {
                var self = this; 
//                console.log(self.Users);
                if(_.isEmpty(self.Users)){
                    return self.Users;
                }
                
//                return self.Users;
                return self.Users.filter(function (user) {
                  return user.idle === true;
                });
            },
            activeGraphicOperators: function () {
//                var filter = Vue.filter('filterBy');
//                return filter(this.Users, 'false', 'idle');
                  var self = this;
                  if(_.isEmpty(self.Users)){
                    return self.Users;
                  };
//                  return self.Users;
                  return self.Users.filter(function (user) {
                      return user.idle === false;
                  });
            }
        },
		methods: {
			handleChildCall (payload) {
                console.log(payload);
            }

		},
        mounted() {
            console.log('Userbehavior ready.')
        }
    }
</script>