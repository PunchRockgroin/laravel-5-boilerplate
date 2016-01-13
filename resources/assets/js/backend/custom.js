$(function() {
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

$(function() {
    if ($('div#file-upload').length) {
        var baseUrl = "",
                token = $('meta[name="_token"]').attr('content')
//                currentFile = $('input[name="_currentfile"]').val(),
//                nextVersion = $('select[name="_nextVersion"]').val(),
//                sessionID = $('input[name="session_id"]').val()
                ;
        
                        
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
                    $('.checkin-button').prop('disabled', true);
                });
                this.on("success", function (file, response) {
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
                _token: token
            }
        });
        Dropzone.options.fileEntityUploadDz = {
            accept: function (file, done) {

            }
        };
    }
});