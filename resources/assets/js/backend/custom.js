$( function () {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1000",
        "timeOut": "2000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
} );

Vue.transition( 'bounce', {
    enterClass: 'bounceInLeft',
    leaveClass: 'bounceOutRight'
} );
Vue.transition( 'zoom', {
    enterClass: 'zoomIn',
    leaveClass: 'zoomOut'
} );

Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector( 'meta[name="_token"]' ).getAttribute( 'content' );

if ( $( '#Hopper' ).length ) {
    var hopperVue = new Vue( {
        el: '#Hopper',
        data: {
            message: '',
            Users: { },
            Visits: { },
            Unassigned: { },
            currentUser: { },
            currentVisit: { },
            inVisit: { },
            idleUsers: { }
        },
        computed: {
            idleGraphicOperators: function () {
                var filter = Vue.filter('filterBy');
                return filter(this.Users, 'true', 'idle');
            },
            activeGraphicOperators: function () {
                var filter = Vue.filter('filterBy');
                return filter(this.Users, 'false', 'idle');
            }
        },
        filters: {
            moment: function ( date ) {
                return moment( date ).format( 'MMMM Do YYYY, h:mm:ss a' );
            },
            join: function ( elem ) {
                return elem.join();
            }
        },
        methods: {
            moment: function ( date ) {
                return moment( date );
            },
            date: function ( date ) {
                return moment( date ).format( 'MMMM Do YYYY, h:mm:ss a' );
            },
            dateFromNow: function ( date ) {
                return moment( date ).fromNow();
            },
            greet: function ( event ) {
                // `this` inside methods point to the Vue instance
                alert( 'Hello ' + window.hopper.username + '!' );
                // `event` is the native DOM event
                alert( event.target.tagName );
            },
            getHeartbeat: function ( event ) {
                // GET request
                this.$http( { url: window.hopper.heartbeat_status, method: 'GET' } ).then( function ( response ) {
                    // success callback
//                this.$set('message', response.data.message);

                }, function ( response ) {
                    // error callback
                } );
            },
            getDashboardData: function ( event ) {
                // GET request
                this.$http( { url: window.hopper.routes.heartbeat_data, method: 'GET' } ).then( function ( response ) {
                    // success callback
//                console.log(response.data.payload);
                    this.$set( 'message', response.data.message );
                    this.$set( 'inVisit', response.data.payload.inVisit );
//                console.log(response.data.payload);
//                var arr = Object.keys(response.data.payload.groups).map(function (key) {return response.data.payload.groups[key]});
                    var otherUsers = [ ];
                    _.forEach( response.data.payload.groups, function ( value ) {
                        _.forEach( value, function ( subvalue ) {
                            otherUsers.push( subvalue );
                        } );
                    } );
                    this.$set( 'idleUsers', otherUsers );

                }, function ( response ) {
                    // error callback
                } );
            },
            getUserStatusData: function ( event ) {
                this.$http( { url: window.hopper.routes.user_status, method: 'GET' } ).then( function ( response ) {
                    // success callback

                    this.Users = response.data.payload;
//                  console.log(this.Users);
//                console.log(response.data.payload);
//                var arr = Object.keys(response.data.payload.groups).map(function (key) {return response.data.payload.groups[key]});
//                  var active = [];
//                  var idle = [];
//                _.forEach(response.data.payload, function (user) {
//                    if(user.state === 'active'){
//                        active.push(user);
//                    }else{
//                        idle.push(user);
//                    }
//
////                    _.forEach(value, function (subvalue) {
////                        otherUsers.push(subvalue);
////                    });
//                });
//                this.$set('activeUsers', active);
//                this.$set('idleUsers', idle);


                }, function ( response ) {
                    // error callback
                } );
            },
            updateUserStatus: function ( user ) {
                var index = _.findIndex( this.Users, { uid: user.uid } );
                this.Users.$set( index, user );
            },
            setBehaviorData: function ( members ) {
                var elGroups = _.groupBy( members, function ( member ) {
                    return member.heartbeat.route;
                } );
                var inVisit = _.filter( members, function ( member ) {
                    return member.heartbeat.route == 'admin.visit.edit';
                } );
                var idleUsers = _.filter( members, function ( member ) {
                    return member.heartbeat.route != 'admin.visit.edit';
                } );
                this.$set( 'inVisit', inVisit );
                this.$set( 'idleUsers', idleUsers );
            },
            getMemberHeartbeat: function ( member ) {
                this.$http( { url: window.hopper.heartbeat_user + '/' + member.id, method: 'GET' } ).then( function ( response ) {
                    // success callback
//            console.log(response.data.payload);
//                    this.$set('message', response.data.message);
//                    this.$set('inVisit', response.data.payload.inVisit);
//    //                console.log(response.data.payload);
//    //                var arr = Object.keys(response.data.payload.groups).map(function (key) {return response.data.payload.groups[key]});
//                    var otherUsers = [];
//                    _.forEach(response.data.payload.groups, function (value) {
//                        _.forEach(value, function (subvalue) {
//                            otherUsers.push(subvalue);
//                        });
//                    });
//                    this.$set('idleUsers', otherUsers);

                }, function ( response ) {
                    // error callback
                    console.log( response );
                } );
            },
//        addMember: function()
            getPusherPresence: function ( event ) {
                var PresenceChannel = pusher.subscribe( "presence-test_channel" );
                PresenceChannel.bind( 'pusher:subscription_succeeded', function ( members ) {
//                console.log(members);
//                hopperVue.setBehaviorData(members.members);
                    hopperVue.getDashboardData();
                } );

                PresenceChannel.bind( 'pusher:member_added', function ( member ) {
                    // for example:
//                console.log(member);
//                console.log(hopperVue.inVisit);

//                console.log(hopperVue.idleUsers);
//                _.each(hopperVue.idleUsers, function(user){
//                    console.log(user.heartbeat.route);
//                })
//                hopperVue.setBehaviorData(members.members);
                    hopperVue.getDashboardData();
                    hopperVue.getMemberHeartbeat( member );
                } );
                PresenceChannel.bind( 'pusher:member_removed', function ( member ) {
                    // for example:
//                console.log(member);
//                console.log(hopperVue.inVisit);
//                _.each(hopperVue.idleUsers, function(user){
//                    console.log(user.heartbeat.route);
//                })
//                hopperVue.setBehaviorData(members.members);
                    hopperVue.getDashboardData();
                    hopperVue.getMemberHeartbeat( member );
                } );
            },
            toggleUserStatus: function ( id, event ) {
                this.$http( { url: window.hopper.routes.user_update + '/' + id, method: 'POST' } ).then( function ( response ) {
                    // success callback
                    if ( response.data.message === 'ok' ) {
                        var payload = response.data.payload;
//                  console.log(payload);
                        toastr.success( payload.user.name + ' state changed to: <strong>' + payload.user.state + '</strong>', 'Hopper Says' );
                    } else {
                        toastr.error( 'Something happened', 'Hopper Says' );
                        //console.log( response.data );
                    }

                }, function ( response ) {
                    // error callback
                    //console.log( response );
                } );
            },
            triggerRefresh: function () {
                this.getUserStatusData();
                $( '#dataTableBuilder' ).DataTable().ajax.reload();
            },
            getUnassigned: function ( event ) {
                this.$http( { url: window.hopper.routes.visit_unassigned, method: 'GET' } ).then( function ( response ) {
                    this.Unassigned = response.data.payload;
                } );
            },
            assignUserToVisitModal: function ( user, event ) {
                this.$http( { url: window.hopper.routes.visit_unassigned, method: 'GET' } ).then( function ( response ) {
                    this.Unassigned = response.data.payload;
                    this.currentUser = user;
//                $('#UnAssigned').
                    console.log( user.name );
                    console.log( response.data.payload );
//                this.$set('Unassigned', response.data.payload);
                    $( '.user-assignment-modal' ).modal( 'show' );
                } );
            },
            assignVisitToUserModal: function ( visit ) {
                this.currentVisit = visit;
                $( '.visit-assignment-modal' ).modal( 'show' );
            },
            assignUserToVisit: function ( user, visit, event ) {
                var $el = $( event.target );
                $el.find( '.btn-content' ).html( '<i class="fa fa-spinner fa-spin fa-fw"></i> Assigning' ).parent().addClass( 'btn-warning' );
                this.$http.post( window.hopper.routes.visit_assign + '/' + visit.id, { assignment_user_id: user.id } ).then( function ( response ) {
                    $el.find( '.btn-content' ).html( '<i class="fa fa-check"></i> Assigned' ).parent().removeClass( 'btn-warning' ).addClass( 'btn-success' );
                    this.triggerRefresh();
                } );
            }
        },
        ready: function () {
//        this.getHeartbeat();
            this.getUserStatusData();
//          this.getUnassigned();
        },
        watch: {
            Users: function () {
                //code here executes whenever the Users array changes
                //and runs AFTER the dom is updated, could use this in
                //the parent component
                $( '.user-group' ).matchHeight();

            }
        }
    } );

}

$( function () {
    if ( typeof window.hopper !== "undefined" && typeof window.hopper.heartbeat_detector_enable !== "undefined" && window.hopper.heartbeat_detector_enable === true ) {
        //hopperVue.getDashboardData();
        //hopperVue.getPusherPresence();
    }

    $( '.user-status-refresh' ).on( 'click', function () {
        hopperVue.getUserStatusData();
    } );

    var hopperChannel;

    if ( 'undefined' !== typeof pusher ) {
        hopperChannel = pusher.subscribe( 'hopper_channel' );
        hopperChannel.bind( 'user_status', function ( data ) {
            if ( data.message === 'update' ) {
                hopperVue.triggerRefresh();
            }
        } );
        hopperChannel.bind( 'visit_status', function ( data ) {
            if ( data.message === 'update' ) {
                hopperVue.triggerRefresh();
            }
        } );
        $( document ).on( "click", ".assign-visit-to-user", function () {
            var $el = $( this );
            hopperVue.assignVisitToUserModal( $el.data('id') );
        } );
    }
} );

$( function () {

    $( '.destructive-btn' ).on( 'click', function ( evt ) {
        evt.preventDefault();
        var url = $( this ).attr( 'href' );
        swal( {
            title: "Are you sure?",
            text: "You will not be able to recover this data!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "It's my funeral",
            closeOnConfirm: false
        },
            function () {
                $.get( url, function ( resp ) {
                    swal( "Deleted!", resp.payload, "error" );
                } )
                .fail( function () {
                    swal( "Error!", "Something went rong.", "error" );
                } );
            } );
    } );



    var channel;
    $( '.repeater' ).repeater( {
        defaultValues: {
        },
        show: function () {
            $( this ).show();
            $( '.repeater' ).find( '.date-range-picker' ).daterangepicker( {
                "singleDatePicker": true,
                "showWeekNumbers": false,
                "timePicker": true,
                "timePickerIncrement": 1,
                "opens": "center",
                "drops": "up",
                "locale": {
                    format: 'MM/DD/YYYY h:mm A'
                }
            } );
        },
        hide: function ( deleteElement ) {
            if ( confirm( 'Are you sure you want to delete this element?' ) ) {
                $( this ).hide( deleteElement );
            }
        },
        ready: function ( setIndexes ) {
            $( '.repeater' ).find( '.date-range-picker' ).daterangepicker( {
                "singleDatePicker": true,
                "showWeekNumbers": false,
                "timePicker": true,
                "timePickerIncrement": 1,
                "opens": "center",
                "drops": "up",
                "locale": {
                    format: 'MM/DD/YYYY h:mm A'
                }
            } );
        }
    } );
} );

var getNextVersion = function ( postdata, $target ) {
    var baseUrl = "";
    var response = false;
    $.post( baseUrl + "/admin/files/nextversion", postdata ).done( function ( resp ) {
        if ( resp.message === 'ok' ) {
            $target.val( resp.payload.newfilename );

        } else {
            console.log( resp.payload.message );
        }
        return resp.payload;
    } );
};

var setCurrentFile = function ( session_file_option, $target ) {
    if ( session_file_option.val() ) {
        $( 'input[name="currentfilename"]' ).val( session_file_option.val() );
        $( 'input[name="filename"]' ).val( session_file_option.val() );
        $( '.file-update-section, .current-file-section' ).removeClass( 'hidden' );
        $( 'select[name="next_version"]' ).val( session_file_option.data( 'nextversion' ) ).trigger( 'change' );
    }
};

$( function () {
    if ( $( 'div#file-upload' ).length ) {
        var baseUrl = "",
            token = $( 'meta[name="_token"]' ).attr( 'content' ),
            $fileNameInput = $( 'input[name="filename"]' ), fileName,
            $currentFileNameInput = $( 'input[name="currentfilename"]' ), currentFileName,
            $nextVersionInput = $( 'select[name="next_version"]' ), next_version,
            $behavior = $( 'input[name="behavior"]' ), behavior,
            $session_id = $( 'input[name="session_id"]' )
            ;
        behavior = $behavior.val() || false;
        currentFileName = $currentFileNameInput.val() || false;
        next_version = $nextVersionInput.val() || false;

        $nextVersionInput.on( 'change', function () {
//         next_version = $nextVersionInput.val() || false;
            getNextVersion( {
                _token: token,
                filename: $fileNameInput.val(),
                currentFileName: $currentFileNameInput.val() || false,
                next_version: $nextVersionInput.val() || false
            }, $fileNameInput );
        } );


        if ( $( 'input.session_file_option' ).length ) {
            setCurrentFile( $( "input.session_file_option:checked" ), $currentFileNameInput );
            $( ".multiple-file-section" ).on( "click", "input.session_file_option", function () {
                setCurrentFile( $( this ), $currentFileNameInput );
            } );
        }
        ;


        var fileEntityUploadDz = new Dropzone( "div#file-upload", {
            url: baseUrl + "/admin/files/upload",
            previewTemplate: document.querySelector( '#preview-template' ).innerHTML,
            paramName: "file", // The name that will be used to transfer the file
            maxFilesize: 500, // MB
            maxFiles: 1,
            autoDiscover: false,
            acceptedFiles: '.ppt,.pptx,.pdf,.pptm',
//                addRemoveLinks: true,
            init: function () {
                this.on( "addedfile", function ( file ) {
                    $( 'input[name="filename_uploaded"]' ).val( file.name );
//                    channel = pusher.subscribe('hopper_channel');
                    fileName = $fileNameInput.val();
                    next_version = $nextVersionInput.val() || false;
                    $( '.checkin-button' ).prop( 'disabled', true );
//                    console.log(behavior);
                } );
                this.on( "success", function ( file, response ) {
                    $( '.file-update-section' ).removeClass( 'hidden' );
                    $fileNameInput.val( response.newFileName );
                    $( 'input[name="temporaryfilename"]' ).val( response.temporaryfilename );
                    $.each( response.metadata, function ( i, value ) {
                        $( 'input[name="' + i + '"]' ).val( value );
                    } )
                    switch ( behavior ) {
                        case 'create_eventsession':
                        case 'update_eventsession':
//                            $nextVersionInput.val(response.next_version);
                            break;
                    }

                    var $el = $( file.previewElement );
                    $el.find( '.info-box-icon' )
                        .toggleClass( 'bg-aqua bg-green' )
                        .find( 'i' )
                        .toggleClass( 'fa-cog fa-spin fa-check-circle-o' );
                    $el.find( '.renamed-to' )
                        .html( 'uploaded and renamed to <strong>' + response.newFileName + '</strong>' )
                        ;
//                    $el.find('.dz-wait')
//                            .html('<div class="alert alert-info"><i class="fa fa-cog fa-spin"></i> Please wait while we transfer the file to Dropbox...</div>')
//                            ;
                    $el.append( '<input type="hidden" name="newfile" value="' + response.newFileName + '" \>' );
                    //$('#movemastertoworking-pseudo').bootstrapSwitch('state', true);
                    //$('.checkin-button').prop('disabled', false);
//                    channel.bind('dropbox_action', function(data) {
//
//                            if(data.filename === response.newFileName){
//                                $el.find('.dz-wait').html('<div class="alert alert-success"><i class="fa fa-check">'+data.message+'</div>');
//                            }
////
//
//                    });
//                        console.log($el);
                } );
                this.on( "error", function ( file, response ) {
//                        console.log(response);
                    var $el = $( file.previewElement );
                    $el.find( '.info-box-icon' )
                        .toggleClass( 'bg-aqua bg-red' )
                        .find( 'i' )
                        .toggleClass( 'fa-cog fa-spin fa-times-circle-o' );
                    $el.find( '.dz-error' )
                        .wrapInner( '<div class="alert alert-danger" />' );
//                        console.log($el);
                } );
            },
            params: {
                _token: token,
                filename: $fileNameInput.val(),
                currentFileName: currentFileName,
                next_version: $nextVersionInput.val() || false,
                behavior: $behavior.val() || false,
                session_id: $session_id.val() || false,
            }
        } );
        Dropzone.options.fileEntityUploadDz = {
            accept: function ( file, done ) {

            }
        };
    }

    $( 'input#visitorsNames' ).click( function () {
        $( "#visitorNamesEntry" ).focus();
    } );


    if ( $( 'div#visit-upload' ).length ) {
        var baseUrl = "",
            token = $( 'meta[name="_token"]' ).attr( 'content' ),
            fileName = $( 'input[name="filename"]' ).val(),
            fileName = $( 'input[name="filename"]' ).val(),
            sessionID = $( 'input[name="session_id"]' ).val(),
            lastenter
            ;


//        Dropzone.autoDiscover = true;
        var visitDropzone = new Dropzone( document.body, {
            url: baseUrl + "/admin/files/upload",
            previewsContainer: "div#visit-upload",
            previewTemplate: document.querySelector( '#preview-template' ).innerHTML,
            paramName: "file", // The name that will be used to transfer the file
            maxFilesize: 500, // MB
            maxFiles: 1,
            clickable: false,
            acceptedFiles: '.ppt,.pptx,.pdf,.pptm',
            accept: function ( file, done ) {
                var validname = $( 'input[name="filename"]' ).val();
                if ( validname === file.name ) {
                    done();
                    return;
                } else {
                    swal( "Oops...", "The filename must match to ensure you are using the right file for this visit.", "error" );
                    visitDropzone.removeAllFiles();
                }
//                file.acceptDimensions = done;

            },
//                addRemoveLinks: true,
            init: function () {
                this.on( "dragleave", function ( event ) {
                    if ( lastenter === event.target ) {
//                        console.log('dragleave');
                        $( '.dz-overtop' ).removeClass( 'dragging' );
                    }
//
                } );
                this.on( "dragenter", function ( event ) {
                    lastenter = event.target;
                    $( '.dz-overtop' ).addClass( 'dragging' );
                } );
                this.on( "dragstart", function ( event ) {
//                    console.log('dragstart');
//                    $('.dz-overtop').addClass('dragging');
                } );
                this.on( "addedfile", function ( file ) {
                    $( '.dz-overtop' ).removeClass( 'dragging' );
                    $( '#visit-upload' ).addClass( 'dz-started' );
//                    channel = pusher.subscribe('hopper_channel');
                    //$('.checkin-button').prop('disabled', true);
                } );
                this.on( "success", function ( file, response ) {
//                        console.log(response);

                    $.each( response.metadata, function ( i, value ) {
                        $( 'input[name="' + i + '"]' ).val( value );
                    } )
                    var $el = $( file.previewElement );
                    $el.find( '.info-box-icon' )
                        .toggleClass( 'bg-aqua bg-green' )
                        .find( 'i' )
                        .toggleClass( 'fa-cog fa-spin fa-check-circle-o' );
                    $el.find( '.renamed-to' )
                        .html( 'uploaded' )
                        ;
//                    $el.find('.dz-wait')
//                            .html('<div class="alert alert-info"><i class="fa fa-cog fa-spin"></i> Please wait while we transfer the file to Dropbox...</div>')
//                            ;
                    $el.append( '<input type="hidden" name="newfile" value="' + response.newFileName + '" \>' );


//                    channel.bind('dropbox_action', function(data) {
//
//                            if(data.filename === response.newFileName){
//                                $el.find('.dz-wait').html('<div class="alert alert-success"><i class="fa fa-check">'+data.message+'</div>');
//                            }
////
//
//                    });
//                        console.log($el);
                } );
                this.on( "error", function ( file, response ) {
//                        console.log(response);
                    var $el = $( file.previewElement );
                    $el.find( '.info-box-icon' )
                        .toggleClass( 'bg-aqua bg-red' )
                        .find( 'i' )
                        .toggleClass( 'fa-cog fa-spin fa-times-circle-o' );
                    $el.find( '.dz-error' )
                        .wrapInner( '<div class="alert alert-danger" />' );
//                        console.log($el);
                } );
                this.on( "reset", function () {
                    $( '#visit-upload' ).removeClass( 'dz-started' );
                } );
            },
            params: {
                _token: token,
                filename: fileName,
                currentFileName: fileName,
                next_version: false

            }
        } );
        Dropzone.options.visitDropzone = {
            accept: function ( file, done ) {

            }
        };
    }

    $( "input.bootstrap-checkbox-switch" ).bootstrapSwitch().on( 'switchChange.bootstrapSwitch', function ( event, state ) {
        var $el = $( this ),
            name = $el.attr( 'name' ),
            onValue = $el.data( 'onText' ),
            offValue = $el.data( 'offText' ),
            switchTarget = name.replace( '-pseudo', '' );
//             console.log(switchTarget);
        if ( state ) {
            $( 'input[name="' + switchTarget + '"]' ).val( onValue );
        } else {
            $( 'input[name="' + switchTarget + '"]' ).val( offValue );
        }

    } );
} );


$( function () {


//
//        //-------------
//        //- LINE CHART -
//        //--------------

    // Get context with jQuery - using jQuery's .get() method.

//        var lineChartData = {
//          labels: ["January", "February", "March", "April", "May", "June", "July"],
//          datasets: [
//            {
//              label: "Electronics",
//              fillColor: "rgb(210, 214, 222)",
//              strokeColor: "rgb(210, 214, 222)",
//              pointColor: "rgb(210, 214, 222)",
//              pointStrokeColor: "#c1c7d1",
//              pointHighlightFill: "#fff",
//              pointHighlightStroke: "rgb(220,220,220)",
//              data: [65, 59, 80, 81, 56, 55, 40]
//            },
//            {
//              label: "Digital Goods",
//              fillColor: "rgba(60,141,188,0.9)",
//              strokeColor: "rgba(60,141,188,0.8)",
//              pointColor: "#3b8bba",
//              pointStrokeColor: "rgba(60,141,188,1)",
//              pointHighlightFill: "#fff",
//              pointHighlightStroke: "rgba(60,141,188,1)",
//              data: [28, 48, 40, 19, 86, 27, 90]
//            }
//          ]
//        };

    var lineOptions = {
        //Boolean - If we should show the scale at all
        showScale: true,
        //Boolean - Whether grid lines are shown across the chart
        scaleShowGridLines: false,
        //String - Colour of the grid lines
        scaleGridLineColor: "rgba(0,0,0,.05)",
        //Number - Width of the grid lines
        scaleGridLineWidth: 1,
        //Boolean - Whether to show horizontal lines (except X axis)
        scaleShowHorizontalLines: true,
        //Boolean - Whether to show vertical lines (except Y axis)
        scaleShowVerticalLines: true,
        //Boolean - Whether the line is curved between points
        bezierCurve: true,
        //Number - Tension of the bezier curve between points
        bezierCurveTension: 0.3,
        //Boolean - Whether to show a dot for each point
        pointDot: false,
        //Number - Radius of each point dot in pixels
        pointDotRadius: 4,
        //Number - Pixel width of point dot stroke
        pointDotStrokeWidth: 1,
        //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
        pointHitDetectionRadius: 20,
        //Boolean - Whether to show a stroke for datasets
        datasetStroke: true,
        //Number - Pixel width of dataset stroke
        datasetStrokeWidth: 2,
        //Boolean - Whether to fill the dataset with a color
        datasetFill: true,
        //String - A legend template
        legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend chart-js-legend list-unstyled \"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%=datasets[i].label%></li><%}%></ul>",
        //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
        maintainAspectRatio: true,
        //Boolean - whether to make the chart responsive to window resizing
        responsive: true
    };
    $( '.lineChart' ).each( function ( index ) {
        var $el = $( this ),
            lineChartCanvas = $( this ).get( 0 ).getContext( "2d" ), lineChart,
            elvariable = $el.data( 'variable' ) || false,
            values = window[elvariable] || false,
            legend = $el.data( 'legendTarget' ),
            options = $el.data();
        var settings = $.extend( { }, lineOptions, options );
        console.log( values );
        if ( values ) {
            lineChart = new Chart( lineChartCanvas ).Line( values, settings );
            if ( $( legend ).length ) {
                $( legend ).html( lineChart.generateLegend() );
            }
        } else {
            $el.replaceWith( '<div class="callout callout-danger"><h4>No Data</h4><p>There is a problem that we need to fix. A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart.</p></div>' );
        }
    } );
    //-------------
    //- PIE CHART -
    //-------------

    var pieOptions = {
        //Boolean - Whether we should show a stroke on each segment
        segmentShowStroke: true,
        //String - The colour of each segment stroke
        segmentStrokeColor: "#fff",
        //Number - The width of each segment stroke
        segmentStrokeWidth: 2,
        //Number - The percentage of the chart that we cut out of the middle
        percentageInnerCutout: 50, // This is 0 for Pie charts
        //Number - Amount of animation steps
        animationSteps: 100,
        //String - Animation easing effect
        animationEasing: "easeOutCirc",
        //Boolean - Whether we animate the rotation of the Doughnut
        animateRotate: true,
        //Boolean - Whether we animate scaling the Doughnut from the centre
        animateScale: false,
        //Boolean - whether to make the chart responsive to window resizing
        responsive: true,
        // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
        maintainAspectRatio: true,
        //String - A legend template
        legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend chart-js-legend list-unstyled\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (<%=segments[i].value%>)<%}%></li><%}%></ul>"
    };

    $( '.pieChart' ).each( function ( index ) {
        var $el = $( this ),
            pieChartCanvas = $( this ).get( 0 ).getContext( "2d" ), pieChart,
            elvariable = $el.data( 'variable' ) || false,
            values = window[elvariable] || false,
            legend = $el.data( 'legendTarget' ),
            options = $el.data();
        var settings = $.extend( { }, pieOptions, options );
        console.log( values );
        if ( values ) {
            pieChart = new Chart( pieChartCanvas ).Doughnut( values, settings );
            if ( $( legend ).length ) {
                $( legend ).html( pieChart.generateLegend() );
            }
        } else {
            $el.replaceWith( '<div class="callout callout-danger"><h4>No Data</h4><p>There is a problem that we need to fix. A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart.</p></div>' );
        }
    } );
} );
