Vue.transition('bounce', {
    enterClass: 'bounceInLeft',
    leaveClass: 'bounceOutRight'
});
Vue.transition('zoom', {
    enterClass: 'zoomIn',
    leaveClass: 'zoomOut'
});
var hopperVue = new Vue({
    el: '#Hopper',
    data: {
        message: '',
        inVisit: '',
        idleUsers: ''
    },
    filters: {
        moment: function (date) {
            return moment(date).format('MMMM Do YYYY, h:mm:ss a');
        },
        join: function (elem) {
            return elem.join();
        }
    },
    methods: {
        moment: function (date) {
            return moment(date);
        },
        date: function (date) {
            return moment(date).format('MMMM Do YYYY, h:mm:ss a');
        },
        dateFromNow: function (date) {
            return moment(date).fromNow();
        },
        greet: function (event) {
            // `this` inside methods point to the Vue instance
            alert('Hello ' + window.hopper.username + '!');
            // `event` is the native DOM event
            alert(event.target.tagName);
        },
        getHeartbeat: function (event) {
            // GET request
            this.$http({url: window.hopper.heartbeat_status, method: 'GET'}).then(function (response) {
                // success callback
//                this.$set('message', response.data.message);

            }, function (response) {
                // error callback
            });
        },
        getDashboardData: function (event) {
            // GET request
            this.$http({url: window.hopper.heartbeat_data, method: 'GET'}).then(function (response) {
                // success callback
                this.$set('message', response.data.message);
                this.$set('inVisit', response.data.payload.inVisit);
//                var arr = Object.keys(response.data.payload.groups).map(function (key) {return response.data.payload.groups[key]});
                var otherUsers = [];
                _.forEach(response.data.payload.groups, function(value) {
                    _.forEach(value, function(subvalue){
                        otherUsers.push(subvalue);
                    });
                });
                this.$set('idleUsers', otherUsers);

            }, function (response) {
                // error callback
            });
        }
    },
    ready: function () {
        this.getHeartbeat();


    }
});


if (typeof window.hopper !== "undefined" && typeof window.hopper.heartbeat_detector_enable !== "undefined" && window.hopper.heartbeat_detector_enable === true) {
    hopperVue.getDashboardData();
    setInterval(function(){
        hopperVue.getDashboardData();
    }, 10000);
}





$(function () {
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
});


$(function () {








//    $('.repeat').each(function() {
//        var obj = $(this),
//            objD = obj.data(),
//            options = {
//                wrapper : '.repeater-wrapper',
//                container : '.repeater-container',
//                row_count_placeholder : '__row-count-placeholder__',
//                after_add : function(container, new_row, default_after_add) {
//                $(new_row).find('.date-range-picker').daterangepicker({
//                        "singleDatePicker": true,
//                        "showWeekNumbers": false,
//                        "timePicker": true,
//                        "timePickerIncrement": 1,
//                        "opens": "center",
//                        "drops": "up",
//                        "locale": {
//                            format: 'MM/DD/YYYY h:mm A'
//                        }
//                    });
//                default_after_add(container, new_row);
//                }
//            },
//            o = $.extend(true, {}, options, objD );
//        $(this).repeatable_fields(o);
//    });


    $('.repeater').repeater({
        defaultValues: {
        },
        show: function () {
            $(this).show();
            $('.repeater').find('.date-range-picker').daterangepicker({
                "singleDatePicker": true,
                "showWeekNumbers": false,
                "timePicker": true,
                "timePickerIncrement": 1,
                "opens": "center",
                "drops": "up",
                "locale": {
                    format: 'MM/DD/YYYY h:mm A'
                }
            });
        },
        hide: function (deleteElement) {
            if (confirm('Are you sure you want to delete this element?')) {
                $(this).hide(deleteElement);
            }
        },
        ready: function (setIndexes) {
            $('.repeater').find('.date-range-picker').daterangepicker({
                "singleDatePicker": true,
                "showWeekNumbers": false,
                "timePicker": true,
                "timePickerIncrement": 1,
                "opens": "center",
                "drops": "up",
                "locale": {
                    format: 'MM/DD/YYYY h:mm A'
                }
            });
        }
    });
});

$(function () {
    if ($('div#file-upload').length) {
        var baseUrl = "",
                token = $('meta[name="_token"]').attr('content'),
                $fileNameInput = $('input[name="filename"]'), fileName,
                $currentFileNameInput = $('input[name="currentfilename"]'), currentFileName,
                $behavior = $('input[name="behavior"]'), behavior,
                $nextVersionInput = $('select[name="next_version"]'), next_version
//                currentFile = $('input[name="_currentfile"]').val(),
//                nextVersion = $('select[name="_nextVersion"]').val(),
//                sessionID = $('input[name="session_id"]').val()
                ;

        behavior = $behavior.val() || false;
        currentFileName = $currentFileNameInput.val() || false;
        next_version = $nextVersionInput.val() || false;

        Dropzone.autoDiscover = false;
        var fileEntityUploadDz = new Dropzone("div#file-upload", {
            url: baseUrl + "/admin/files/upload",
            previewTemplate: document.querySelector('#preview-template').innerHTML,
            paramName: "file", // The name that will be used to transfer the file
            maxFilesize: 500, // MB
            maxFiles: 1,
            acceptedFiles: '.ppt,.pptx,.pdf,.pptm',
//                addRemoveLinks: true, 
            init: function () {
                this.on("addedfile", function (file) {
//                    channel = pusher.subscribe('hopper_channel');
                    fileName = $fileNameInput.val();
                    next_version = $nextVersionInput.val() || false;
                    $('.checkin-button').prop('disabled', true);
                    console.log(behavior);
                });
                this.on("success", function (file, response) {

                    $fileNameInput.val(response.newFileName);
                    $.each(response.metadata, function (i, value) {
                        $('input[name="' + i + '"]').val(value);
                    })
                    switch (behavior) {
                        case 'create_eventsession':
                        case 'update_eventsession':
                            $nextVersionInput.val(response.next_version);
                            break;
                    }

//                    $('input[name="mime"]').val(response.metadata.mime);
//                    $('input[name="path"]').val(response.metadata.path);
//                    $('input[name="storage_disk"]').val(response.metadata.storage_disk);
//                        console.log(response);
//                  
//                    $('label[for="movemastertoworking-pseudo"]').html('Copy Current Master to Working?');
                    var $el = $(file.previewElement);
                    $el.find('.info-box-icon')
                            .toggleClass('bg-aqua bg-green')
                            .find('i')
                            .toggleClass('fa-cog fa-spin fa-check-circle-o');
                    $el.find('.renamed-to')
                            .html('uploaded and renamed to <strong>' + response.newFileName + '</strong>')
                            ;
//                    $el.find('.dz-wait')
//                            .html('<div class="alert alert-info"><i class="fa fa-cog fa-spin"></i> Please wait while we transfer the file to Dropbox...</div>')
//                            ;
                    $el.append('<input type="hidden" name="newfile" value="' + response.newFileName + '" \>');
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
                });
                this.on("error", function (file, response) {
//                        console.log(response);
                    var $el = $(file.previewElement);
                    $el.find('.info-box-icon')
                            .toggleClass('bg-aqua bg-red')
                            .find('i')
                            .toggleClass('fa-cog fa-spin fa-times-circle-o');
                    $el.find('.dz-error')
                            .wrapInner('<div class="alert alert-danger" />');
//                        console.log($el);
                });
            },
            params: {
                _token: token,
                filename: $fileNameInput.val(),
                currentFileName: currentFileName,
                next_version: $nextVersionInput.val() || false,
                behavior: $behavior.val() || false,
            }
        });
        Dropzone.options.fileEntityUploadDz = {
            accept: function (file, done) {

            }
        };
    }


    if ($('div#visit-upload').length) {
        var baseUrl = "",
                token = $('meta[name="_token"]').attr('content'),
                fileName = $('input[name="filename"]').val(),
                fileName = $('input[name="filename"]').val(),
                sessionID = $('input[name="session_id"]').val(),
                lastenter
                ;


        Dropzone.autoDiscover = false;
        var visitDropzone = new Dropzone(document.body, {
            url: baseUrl + "/admin/files/upload",
            previewsContainer: "div#visit-upload",
            previewTemplate: document.querySelector('#preview-template').innerHTML,
            paramName: "file", // The name that will be used to transfer the file
            maxFilesize: 500, // MB
            maxFiles: 1,
            clickable: false,
            acceptedFiles: '.ppt,.pptx,.pdf,.pptm',
//                addRemoveLinks: true, 
            init: function () {
                this.on("dragleave", function (event) {
                    if (lastenter === event.target) {
//                        console.log('dragleave');
                        $('.dz-overtop').removeClass('dragging');
                    }
//                    
                });
                this.on("dragenter", function (event) {
                    lastenter = event.target;
                    $('.dz-overtop').addClass('dragging');
                });
                this.on("dragstart", function (event) {
//                    console.log('dragstart');
//                    $('.dz-overtop').addClass('dragging');
                });
                this.on("addedfile", function (file) {
                    $('.dz-overtop').removeClass('dragging');
                    $('#visit-upload').addClass('dz-started');
//                    channel = pusher.subscribe('hopper_channel');
                    //$('.checkin-button').prop('disabled', true);
                });
                this.on("success", function (file, response) {
//                        console.log(response);

                    $.each(response.metadata, function (i, value) {
                        $('input[name="' + i + '"]').val(value);
                    })
                    var $el = $(file.previewElement);
                    $el.find('.info-box-icon')
                            .toggleClass('bg-aqua bg-green')
                            .find('i')
                            .toggleClass('fa-cog fa-spin fa-check-circle-o');
                    $el.find('.renamed-to')
                            .html('uploaded')
                            ;
//                    $el.find('.dz-wait')
//                            .html('<div class="alert alert-info"><i class="fa fa-cog fa-spin"></i> Please wait while we transfer the file to Dropbox...</div>')
//                            ;
                    $el.append('<input type="hidden" name="newfile" value="' + response.newFileName + '" \>');


//                    channel.bind('dropbox_action', function(data) {
//                             
//                            if(data.filename === response.newFileName){
//                                $el.find('.dz-wait').html('<div class="alert alert-success"><i class="fa fa-check">'+data.message+'</div>');
//                            }
////                            
//
//                    });
//                        console.log($el);
                });
                this.on("error", function (file, response) {
//                        console.log(response);
                    var $el = $(file.previewElement);
                    $el.find('.info-box-icon')
                            .toggleClass('bg-aqua bg-red')
                            .find('i')
                            .toggleClass('fa-cog fa-spin fa-times-circle-o');
                    $el.find('.dz-error')
                            .wrapInner('<div class="alert alert-danger" />');
//                        console.log($el);
                });
                this.on("reset", function () {
                    $('#visit-upload').removeClass('dz-started');
                });
            },
            params: {
                _token: token,
                filename: fileName,
                currentFileName: fileName,
                next_version: false

            }
        });
        Dropzone.options.visitDropzone = {
            accept: function (file, done) {

            }
        };
    }


});