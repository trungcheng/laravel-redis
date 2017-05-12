var UserModule = function () {
    // validate and submit profile form
    var a = function () {
        $("#my_profile").submit(function (event) {
            event.preventDefault();

            var formData = {
                name: $("#inputName").val(),
                email: $("#inputEmail").val(),
                pass: $("input[name=new_pass]").val(),
                description: $("#inputDescription").val() ,
                thumbnail: $("input[name=thumbnail]").val() ,
            };

            $.ajax({
                type: "POST",
                url: '/user/profile',
                dataType: 'json',
                data: formData, // serializes the form's elements.
                success: function (res) {
                    swal({title: res.msg, type: res.status});
                    //location.reload();
                }
            }
            );
        });
    };

    // init datatable
    var e = function () {
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/user/user-data',
            stateSave: true,
            bDestroy: true,
            columns: [
                {data: 'avatar', name: 'avatar', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'user_type', name: 'user_type'},
                {data: 'status', name: 'status'},
                {data: 'facebook', name: 'facebook'},
                {data: 'action', name: 'action'}
            ]
        });
    };
	var g = function () {
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/user/user-data?user_fe=true',
            stateSave: true,
            bDestroy: true,
            columns: [
                {data: 'avatar', name: 'avatar', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'user_type', name: 'user_type'},
                {data: 'status', name: 'status'},
                {data: 'facebook', name: 'facebook'},
                {data: 'action', name: 'action'}
            ]
        });
    };

    // create user
    var d = function () {
        $("#formCreateUser").submit(function (event) {
            event.preventDefault();
            var i, r, o, p;
            var t = $('select[name=user_type]').val();
            if (t == UserType.type_2 || t == UserType.type_3) {
                i = $('select[name=media_zone]').val();
                r = $('select[name=sms_zone]').val();
            } else if (t == UserType.type_4) {
                o = $('select[name=report_zone]').val();
            } else if (t == UserType.type_5) {
                p = $('select[name=search_zone]').val();
            }

            var re = /^([0-9a-zA-Z]([-_\\.]*[0-9a-zA-Z]+)*)@([0-9a-zA-Z]([-_\\.]*[0-9a-zA-Z]+)*)[\\.]([a-zA-Z]{2,9})$/;

            var formData = {
                user_type: t,
                media_zone: !i ? '' : i,
                sms_zone: !r ? '' : r,
                report_zone: !o ? '' : o,
                search_zone: !p ? '' : p,
                name: $('input[name=name]').val() === '' ? swal(AdminCPLang.lang_13) : $('input[name=name]').val(),
                email: $('input[name=email]').val() === '' || !re.test($('input[name=email]').val()) ? swal(AdminCPLang.lang_14) : $('input[name=email]').val(),
                password: $('input[name=password]').val().length < 6 ? swal(AdminCPLang.lang_15) : $('input[name=password]').val(),
                description: $('textarea[name=description]').val()
            };

            if (!formData.name || !formData.email || !formData.password) {
                return;
            }
            //console.log(formData);
            $.ajax({
                type: "POST",
                url: '/user/create',
                dataType: 'json',
                data: formData, // serializes the form's elements.
                success: function (res) {
                    swal({title: res.msg, type: res.status}, function () {
                        e();
                        $("#createUserModal").modal('hide');
                    });
                }
            }
            );
        });
    }

    // zone selector
    var m = function () {
        $('select[name=user_type]').change(function () {
            var t = $(this).val();
            $("select[name$='zone']").prop('disabled', 'disabled');
            if (t == UserType.type_2 || t == UserType.type_3) {
                $('select[name=media_zone]').prop('disabled', false);
                $('select[name=sms_zone]').prop('disabled', false);
            } else if (t == UserType.type_4) {
                $('select[name=report_zone]').prop('disabled', false);
            } else if (t == UserType.type_5) {
                $('select[name=search_zone]').prop('disabled', false);
            }
        });
    }

    // init
    return {
        initForm: function () {
            a();
        },
        initUserDatatable: function () {
            e(), d(), m();
        },
		initUserFeDatatable: function () {
            g(), d(), m();
        },
        initSelectZone: function () {
            m();
        }
    };
}();

function updateStatusUser(target) {
    var uid = $(target).data('uid');
    var st = $(target).data('status');
    return swal({
        title: AdminCPLang.lang_1,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: AdminCPLang.lang_16,
        closeOnConfirm: false
    }, function () {
        $.ajax({
            type: "POST",
            url: '/user/update-status',
            dataType: 'json',
            data: {id: uid, st: st}, // serializes the form's elements.
            success: function (res) {
                swal({title: res.msg, type: res.status}, function () {
                    UserModule.initUserDatatable();
                });
            }
        }
        );
    });
}

function deleteUser(target) {
    var uid = $(target).data('uid');
    return swal({
        title: AdminCPLang.lang_1,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: AdminCPLang.lang_3,
        closeOnConfirm: false
    }, function () {
        $.ajax({
            type: "POST",
            url: '/user/delete',
            dataType: 'json',
            data: {id: uid}, // serializes the form's elements.
            success: function (res) {
                swal({title: res.msg, type: res.status}, function () {
                    UserModule.initUserDatatable();
                });
            }
        }
        );
    });
}

function editUser(target) {
    var uid = $(target).data('uid');
    //console.log(uid);
    loadModalContent('editUserModalBody', '/user/edit/' + uid);
    $("#editUserModal").modal('show');
}