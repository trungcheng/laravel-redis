/**
 * Created by hoanghung on 03/03/2016.
 */
var LiveModule = function () {
    // validate and submit profile form

    function alertErrorLive(lang) {
        $("input, select, button, textarea").removeAttr("disabled");
        $('.status-ajax').html('');
        swal({
            title: lang,
            type: 'error'
        });
    }

    function validateFormLive() {
        var formData = {
            live_id: $('input[name=live_id]').val() === '' ? null : $('input[name=live_id]').val(),
            league: $('input[name=league]').val() === '' ? alertErrorLive(AdminCPLang.lang_17) : $('input[name=league]').val(),
            matchday: $('input[name=matchday]').val() === '' ? alertErrorLive(AdminCPLang.lang_18) : $('input[name=matchday]').val(),
            status: $('select[name=status]').val(),
            have_report: $('select[name=have_report]').val() == 'true' ? true : false,
            result_home: $('input[name=result_home]').val(),
            result_away: $('input[name=result_away]').val(),
            date: $('input[name=date]').val() === '' ? alertErrorLive(AdminCPLang.lang_19) : $('input[name=date]').val(),
            description: tinyMCE.get('editor').getContent({format : 'text'}),
            homeTeamName: $('input[name=homeTeamName]').val() === '' ? alertErrorLive(AdminCPLang.lang_20) : $('input[name=homeTeamName]').val(),
            homeTeamLogo: $('input[name=homeTeamLogo]').val() === '' ? alertErrorLive(AdminCPLang.lang_21) : $('input[name=homeTeamLogo]').val(),
            awayTeamName: $('input[name=awayTeamName]').val() === '' ? alertErrorLive(AdminCPLang.lang_22) : $('input[name=awayTeamName]').val(),
            awayTeamLogo: $('input[name=awayTeamLogo]').val() === '' ? alertErrorLive(AdminCPLang.lang_23) : $('input[name=awayTeamLogo]').val(),
        }
        if (!formData.league || !formData.matchday || !formData.date || !formData.homeTeamName || !formData.homeTeamLogo || !formData.awayTeamName || !formData.awayTeamLogo) {
            return;
        }
        return formData;
    }

    var a = function () {       // Create live
        $(".form-create-live").submit(function (event) {
            event.preventDefault(); // Ngan ko gui form
            $("html, body").animate({ scrollTop: 0 }, "slow");  // scroll man hinh len top
            $(".form-create-live input, .form-create-live select, .form-create-live button, .form-create-live textarea").prop("disabled", true);    // Khoa input
            $('.status-ajax').html('<div class="container"><button class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Creating...</button></div>'); // Hien nut loading
            var formData = validateFormLive();  // Lay gia tri trong form
            if(formData.matchday == '' || formData.date == '' || formData.homeTeamName == '' || formData.homeTeamLogo == '' || formData.awayTeamName == '' || formData.awayTeamLogo == '') alert('error'); // Ngan gui form khi validate sai
            $.ajax({
                    type: "POST",
                    url: '/media/fixture/create',
                    dataType: 'json',
                    data: formData, // serializes the form's elements.
                    success: function (resp) {
                        $('.status-ajax').html('');
                        $(".form-create-live input, .form-create-live select, .form-create-live button, .form-create-live textarea").removeAttr("disabled");
                        if (resp.status == 'success') {
                            window.location.assign('http://' + window.location.hostname + '/media/fixture');
                        } else {
                            swal({title: resp.status,text: resp.msg, type: resp.status});
                        }
                    },
                    error: function(resp) {
                        swal({title:'error', text: 'Send data to create error', type: 'error'});
                    }
                }
            );
        });
    };

    var b = function () {                      // Edit live
        $(".form-edit-live").submit(function (event) {
            event.preventDefault();
            $("html, body").animate({ scrollTop: 0 }, "slow");
            $(".form-edit-live input, .form-edit-live select, .form-edit-live button, .form-edit-live textarea").prop("disabled", true);
            $('.status-ajax').html('<div class="container"><button class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Updating...</button></div>');
            var formData = validateFormLive();
            if(formData.matchday == '' || formData.date == '' || formData.homeTeamName == '' || formData.homeTeamLogo == '' || formData.awayTeamName == '' || formData.awayTeamLogo == '') alert('error'); // Ngan gui form khi validate sai
            console.log(formData);
            $.ajax({
                    type: "POST",
                    url: '/media/fixture/edit',
                    dataType: 'json',
                    data: formData, // serializes the form's elements.
                    success: function (resp) { console.log(resp);
                        $('.status-ajax').html('');
                        $(".form-edit-live input, .form-edit-live select, .form-edit-live button, .form-edit-live textarea").removeAttr("disabled");
                        if (resp.status == 'success') {
                            swal({title: resp.status,text: resp.msg, type: resp.status}, function() {
                                location.reload();
                            });
                        } else {
                            swal({title: resp.status,text: resp.msg, type: resp.status});
                        }
                    },
                    error: function(resp) {
                        swal({title: 'error', text: 'Send data to edit error', type: 'error'});
                    }
                }
            );
        });
    };

    function validateFormCreateEvent() {
        var formData = {
            live_id: $('.form-create-event input[name=live_id]').val() === '' ? null : $('.form-create-event input[name=live_id]').val(),
            event_key: $('.form-create-event input[name=event_id]').val() === '' ? null : $('.form-create-event input[name=event_id]').val(),
            type: $('.form-create-event select[name=type]').val(),
            event_of_team: $('.form-create-event input[name=event_of_team]').val(),
            player: $('.form-create-event input[name=player]').val() === undefined ? test = 1 : $('.form-create-event input[name=player]').val() === '' ? alertErrorLive(AdminCPLang.lang_24) : $('.form-create-event input[name=player]').val(),
            minutes: $('.form-create-event input[name=minutes]').val() === '' ? alertErrorLive(AdminCPLang.lang_25) : $('.form-create-event input[name=minutes]').val(),
            content: tinyMCE.activeEditor.getContent(),
        }
        if (!formData.player || !formData.minutes) {
            return;
        }
        return formData;
    }

    var ce = function () {                      // Create event
        $(".form-create-event").submit(function (event) {
            event.preventDefault();

            var formData = validateFormCreateEvent();
            if(formData.minutes == '') alert('error'); // Ngan gui form khi validate sai
            $.ajax({
                    type: "POST",
                    url: '/media/fixture/events',
                    dataType: 'json',
                    data: formData, // serializes the form's elements.
                    success: function (resp) {
                        if (resp.status == 'success') {
                            //swal({title: resp.status,text: resp.msg, type: resp.msg});
                            //$('input[name=league]').val(''); $('input[name=matchday]').val('');
                            //$('select[name=status]').val('TIMED'); $('input[name=result_home]').val(0);
                            //$('input[name=result_away]').val(0); $('input[name=date]').val(''); $('input[name=homeTeamName]').val('');
                            //$('input[name=homeTeamLogo]').val(''); $('input[name=awayTeamName]').val(''); $('input[name=awayTeamLogo]').val('');
                            swal({title: resp.status,text: resp.msg, type: resp.status}, function() {
                                location.reload();
                            });
                        } else {
                            swal({title: resp.status,text: resp.msg, type: resp.status});
                        }
                    },
                    error: function(resp) {
                        swal({title: 'error', text: 'Create error', type: 'error'});
                    }
                }
            );
        });
    };

    function validateFormEditEvent() {
        var formData = {
            live_id: $('.form-edit-event input[name=live_id]').val() === '' ? null : $('.form-edit-event input[name=live_id]').val(),
            event_key: $('.form-edit-event input[name=event_key]').val() === '' ? null : $('.form-edit-event input[name=event_key]').val(),
            type: $('.form-edit-event select[name=type]').val(),
            event_of_team: $('.form-edit-event input[name=event_of_team]').val(),
            player: $('.form-edit-event input[name=player]').val() === undefined ? test = 1 : $('.form-edit-event input[name=player]').val() === '' ? alertErrorLive(AdminCPLang.lang_24) : $('.form-edit-event input[name=player]').val(),
            minutes: $('.form-edit-event input[name=minutes]').val() === '' ? alertErrorLive(AdminCPLang.lang_25) : $('.form-edit-event input[name=minutes]').val(),
            content: tinyMCE.activeEditor.getContent().split("<body>")[1].split("</body>")[0],      // Lay content event (cat cac the <html>,<body>)
        }
        if (!formData.player || !formData.minutes) {
            return;
        }
        return formData;
    }

    var ee = function() {       // Edit event
        $(".form-edit-event").submit(function (event) {
            event.preventDefault();
            var formData = validateFormEditEvent();
            if(formData.minutes == '') alert('error'); // Ngan gui form khi validate sai
            $.ajax({
                    type: "POST",
                    url: '/media/fixture/edit-event',
                    dataType: 'json',
                    data: formData, // serializes the form's elements.
                    success: function (resp) {
                        if (resp.status == 'success') {
                            //swal({title: resp.status,text: resp.msg, type: resp.msg});
                            //$('input[name=league]').val(''); $('input[name=matchday]').val('');
                            //$('select[name=status]').val('TIMED'); $('input[name=result_home]').val(0);
                            //$('input[name=result_away]').val(0); $('input[name=date]').val(''); $('input[name=homeTeamName]').val('');
                            //$('input[name=homeTeamLogo]').val(''); $('input[name=awayTeamName]').val(''); $('input[name=awayTeamLogo]').val('');
                            swal({title: resp.status,text: resp.msg, type: resp.status}, function() {
                                window.location.assign('http://' + window.location.hostname + '/media/fixture/events/' + formData.live_id);
                            });
                        } else {
                            swal({title: resp.status,text: resp.msg, type: resp.status});
                        }
                    },
                    error: function(resp) {
                        swal({title: 'error', text: 'Send data to edit error', type: 'error'});
                    }
                }
            );
        });
    }

    var pl = function() {
        $(".publish-live").submit(function (event) {
            event.preventDefault();
            var formData = validateFormEditEvent();
            if(formData.minutes == '') alert('error'); // Ngan gui form khi validate sai
            $.ajax({
                    type: "POST",
                    url: '/media/fixture/edit-event',
                    dataType: 'json',
                    data: formData, // serializes the form's elements.
                    success: function (resp) {
                        if (resp.status == 'success') {
                            //swal({title: resp.status,text: resp.msg, type: resp.msg});
                            //$('input[name=league]').val(''); $('input[name=matchday]').val('');
                            //$('select[name=status]').val('TIMED'); $('input[name=result_home]').val(0);
                            //$('input[name=result_away]').val(0); $('input[name=date]').val(''); $('input[name=homeTeamName]').val('');
                            //$('input[name=homeTeamLogo]').val(''); $('input[name=awayTeamName]').val(''); $('input[name=awayTeamLogo]').val('');
                            swal({title: resp.status,text: resp.msg, type: resp.status}, function() {
                                window.location.assign('http://' + window.location.hostname + '/media/fixture/events/' + formData.live_id);
                            });
                        } else {
                            swal({title: resp.status,text: resp.msg, type: resp.status});
                        }
                    },
                    error: function(resp) {
                        swal({title: 'error', text: 'Send data to edit error', type: 'error'});
                    }
                }
            );
        });
    }

    var pe = function() {       // Dong mo popup edit event
        $('#editEvent .popup-edit-event .close').click(function(){       // click vao nut dong cua so thi xoa html
            $('#editEvent .modal-body').html('');
        });
        $("html").click(function (e) {                          // click ra ngoai dong cua so thi xoa html
            if ($('#editEvent').is(":visible")){

            }else {
                $('#editEvent .modal-body').html('');
            }
        });
    }

    var cb = function() {           // set js cho nut checkbox | radio
        $('input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
        $('input[type="checkbox"].minimal').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    }

    // check value input number
    var c = function() {
        $('.create-event .minutes, .box-create-event .minutes').on('change', function(){
            if($(this).val() < 0) {
                $(this).val(0);
                alert('Minutes error!');
            }
        });

        $('.create-live, .edit-live').on('change', '.result' , function(){
            if($(this).val() < 0) {
                $(this).val(0);
                alert('Minutes error!');
            }
        });
    }

    // get image
    var g = function() {
        $('.create-live .getimageurl, .edit-live .getimageurl').click(function(){       // Them anh khi click nut url
            var thumb = $('.imagepr_wrap.imagepr_wrap-' + $(this).data('logo'));
            var team = $(this).data('logo');
            return swal({
                    title: "Image url",
                    text: "Enter the images URL!",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    inputPlaceholder: "Write something" },
                function(inputValue){
                    $('#img_test').attr('src','');
                    $('#img_test').attr('src', inputValue);     // test image
                    $('#img_test')
                        .on('error', function() {       // loi load anh
                            swal.showInputError("Image error");
                            return false
                        })
                        .on('load', function() {
                            if (inputValue === false)
                                return false;
                            if (inputValue === "") {
                                swal.showInputError("You need to write something!");
                                return false
                            }
                            if (inputValue.indexOf("jpg") < 0 && inputValue.indexOf("jpeg") < 0 && inputValue.indexOf("gif") < 0 && inputValue.indexOf("png") < 0 || inputValue.indexOf(" ") >= 0 ) {
                                swal.showInputError("Not image");
                                return false
                            }

                            if(team == 'home') {
                                thumb.html('<img src="'+inputValue+'" style="max-width: 100%;" >');

                                $('.logo-' + team).val(inputValue);
                            } else {
                                thumb.html('<img src="'+inputValue+'" style="max-width: 100%;">');
                                $('.logo-' + team).val(inputValue);
                            }
                            $('.preview-placeholder.preview-placeholder-' + team).css('display', 'none');
                            $('.previewshow.previewshow-' + team).css('display', 'block');
                            $('.sweet-alert button.cancel').click();
                        })
                    ;
                });
        });

        $('.create-live .previewshow .thumbactions, .edit-live .previewshow .thumbactions').click(function(){   // xoa anh
            var thumb = $('.imagepr_wrap.imagepr_wrap-' + $(this).data('logo'));
            var input_thumb = $('#id_of_the_target_input_' + $(this).data('logo'));
            var team = $(this).data('logo');
            return swal({
                title: "Are you sure?",
                text: "You will not be able to recover this imaginary file!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            }, function(){
                thumb.html('');
                input_thumb.removeAttr('value');
                $('.preview-placeholder.preview-placeholder-' + team).css('display', 'block');
                $('.previewshow.previewshow-' + team).css('display', 'none');
                $('.sweet-alert button.cancel').click();
            });
        });
    }

    var s = function() {    // Show/Hidden player
        $('.chosen-select').each(function(){
            $(this).change(function(){
                var box = $(this).parents('.box:first');
                var event_player = box.find('.event_player');
                if($(this).val() != 'text' && $(this).val() != 'fulltime') {
                    if(event_player.html() == '') {
                        event_player.html('<div class="form-group player"><label class="col-md-3">'+AdminCPLang.event_player+'</label> <div class="col-md-9"><input type="text" name="player" class="form-control player" value="" placeholder="Ronando" required></div></div><hr class="dotted">');
                    }
                } else {
                    event_player.html('');
                }
            });
        })
    }

    // show/hide box publish live
    var p = function() {
        $('#button_review_live .publish button').click(function(){      // click vao publish thi hien bang publish len
            if($(this).attr('status') == 'off') {
                $('#button_review_live .box-reviewlive').slideUp(300);
                $('#button_review_live .modal-dialog form').slideDown(300);
                $(this).html('Back');
                $(this).attr('status', 'on');
            } else {
                $('#button_review_live .box-reviewlive').slideDown(300);
                $('#button_review_live .modal-dialog form').slideUp(300);
                $(this).html('Publish');
                $(this).attr('status', 'off');
            }
        });


        $('#button_review_live .close').click(function(){       // click vao nut dong cua so thi dong bang publish
            $('#button_review_live .box-reviewlive').slideDown(300);
            $('#button_review_live .modal-dialog form').slideUp(300);
            $('#button_review_live .publish button').html('Publish');
            $('#button_review_live .publish button').attr('status', 'off');
        });
        $("html").click(function (e) {                          // click ra ngoai dong cua so thi dong bang publish
            if ($('#button_review_live').is(":visible")){

            }else {
                $('#button_review_live .box-reviewlive').slideDown(300);
                $('#button_review_live .modal-dialog form').slideUp(300);
                $('#button_review_live .publish button').html('Publish');
                $('#button_review_live .publish button').attr('status', 'off');
            }
        });
    }

    // init
    return {
        initListLive: function() {
            p(), cb()
        },

        initFormCreateLive: function() {
            a(), c(), g(), cb()
        },

        initFormEditLive: function() {
            b(), c(), g(), cb()
        },

        initFormEvent: function() {
            s(), c(), cb(), ce(), ee(), pe()
        },

        initForm: function () {
            a()
        },
        initUserDatatable: function () {
            e(), d(), m()
        },
        initSelectZone: function () {
            m()
        }
    }
}();

function reviewLive(id) {
    $.ajax({
        url: '/media/fixture/table-review-live',
        data: {live_id: id},
        type: 'GET',
        success: function (resp) {
            $('#button_review_live .box-reviewlive').html(resp);
            $('#button_review_live .live_id_publish').val(id);
            $('.button_review_live').click();
        },
        error: function(resp){
            alert('error!');
        }
    });
}

function deleteLive(live_id) {      // Delete live
    return swal({
        title: AdminCPLang.lang_1,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: AdminCPLang.lang_3,
        closeOnConfirm: false
    }, function () {
        $.ajax({
            url: '/media/fixture/delete',
            data: {live_id: live_id},
            type: 'POST',
            success: function (resp) {
                if(resp.status == 'success') {
                    $('.list-live table tr.' + live_id).remove();
                    $('.sweet-alert button.cancel').click();
                } else {
                    swal(resp.status, resp.msg, resp.status);
                }

            },
            error: function (resp) {
                return swal("Error", "Some thing when wrong", "error");
            }
        });
    });
}

//  Delete event
function deleteEvent(live_id , event_key, type) {
    return swal({
        title: AdminCPLang.lang_1,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: AdminCPLang.lang_3,
        closeOnConfirm: false
    }, function () {
        $.ajax({
            url: '/media/fixture/delete-event',
            data: { live_id : live_id , event_key : event_key},
            type: 'POST',
            success: function(resp) {
                if(resp.status == 'success') {
                    if(type == 'li') {
                        $('.create-event li.event-key-' + event_key).remove();
                    } else {
                        $('.create-event table tr.event-key-' + event_key).remove();
                    }
                    swal("Success", "Deleted!", "success");
                } else {
                    swal("Error", "Deleted error!", "error");
                }
            },
            error: function (resp) {
                return swal("Error", "Some thing when wrong", "error");
            }
        });
    });
}