Vue.component('user-behavior', require('../backend/components/hopper/user/behavior.vue'));
Vue.component('user-card', require('../backend/components/hopper/user/card.vue'));
Vue.component('user-assignment-modal', require('../backend/components/hopper/user/assignment-modal.vue'));
Vue.component('visit-assignment-modal', require('../backend/components/hopper/visit/assignment-modal.vue')); 

var hopperVue;
var hopperElem = document.getElementById('Hopper');
 
if ( typeof(hopperElem) != 'undefined' && hopperElem != null ) { 
    hopperVue = new Vue( {
        el: '#Hopper',
        data: {
            message: '',
            Users: { }, 
            Visits: { },
            Unassigned: { },
            Currentuser: {},
            Currentvisit: {},
            inVisit: { },
            idleUsers: { },
            online: true,
            lastHeartBeatReceivedAt: moment(),
            hopperClient : false
        },
        events: {
            childIsCalling: 'handleChildCall',

        },
        computed: {
            idleGraphicOperators () {
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
            activeGraphicOperators () {
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
            },
            statusClass () {
                return 'bg-'+ this.statusclass || 'default';
            }
        },
        methods: {
            handleChildCall (payload) {
                console.log(payload);
                return true;
            },
            heartbeatListen (){
                if(! hopper.heartbeat ){
                    return;
                }
                hopper_channel.bind( 'heartbeat', function ( data ) {
                    hopperVue.$set( 'online', true );
                    hopperVue.$set( 'lastHeartBeatReceivedAt',  moment() );
                } );
                setInterval(this.determineConnectionStatus, 1000);

            },
            determineConnectionStatus () {
                var lastHeartBeatReceivedSecondsAgo = moment().diff(this.lastHeartBeatReceivedAt, 'seconds');
                this.online = lastHeartBeatReceivedSecondsAgo < 125;
                if(!this.online){
                    toastr.error('Please check your internet connection', 'It appears you are offline!', {
                        positionClass: "toast-bottom-center",
                        closeButton: false,
                        preventDuplicates: true,
                        timeOut: 60000, // Set timeOut and extendedTimeOut to 0 to make it sticky
                        extendedTimeout: 0,
                        tapToDismiss: false
                    });
                }else{
                    toastr.clear();
                }
            },
            getUserStatusData ( event ) {

                this.$http( { url: window.hopper.routes.user_status, method: 'GET' } ).then( function ( response ) {
                    // success callback
                    this.Users = response.data.payload;

                    //   console.log(this.Users)

                }, function ( response ) {
                    // error callback
                } );
            },
            // assignUserToVisitModal: function () {
            //    this.$http( { url: window.hopper.routes.visit_unassigned, method: 'GET' } ).then( function ( response ) {
            //        this.Unassigned = response.data.payload;
            //        $( '.user-assignment-modal' ).modal( 'show' );
            //    } );
            // },
            assignVisitToUserModal ( visit ) {
                this.Currentvisit = visit;
                $( '.visit-assignment-modal' ).modal( 'show' );
            },
            getUnassigned ( event ) {
                this.$http( { url: window.hopper.routes.visit_unassigned, method: 'GET' } ).then( function ( response ) {
                    this.Unassigned = response.data.payload;
                } );
            },
            userStatusClass (statusclass){
                var _elstatusclass = statusclass || 'default';
                return 'bg-'+ _elstatusclass;
            },
            triggerRefresh () {
                this.getUserStatusData();
                $( '#dataTableBuilder' ).DataTable().ajax.reload();
                console.log('Data Refreshed')
            },
            moment (...args) {
                return moment(...args);
            },
            date ( date ) {
                return moment( date ).format( 'MMMM Do YYYY, h:mm:ss a' );
            },
            dateFromNow ( date ) {
                return moment( date ).fromNow();
            }
        },
        created: function () {
            this.getUserStatusData();
            this.$on('event:triggerRefresh', function(){
                this.triggerRefresh();
            })
        },
        watch: {
//            Users: function () {
//                //code here executes whenever the Users array changes
//                //and runs AFTER the dom is updated, could use this in
//                //the parent component
////                    $( '.user-group' ).matchHeight();
//
//            }

            Unassigned: function (){
                //   console.log(this.Unassigned)
            },
            Currentuser: function (){
                //   console.log(this.Currentuser)
            }

        }
    });

}

window.hopperVue = hopperVue;