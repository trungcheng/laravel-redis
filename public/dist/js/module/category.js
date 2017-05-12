
function deleteCat(target) {
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
                url: '/media/category/delete',
                dataType: 'json',
                data: {id: uid}, // serializes the form's elements.
                success: function (res) {
                    swal({title: res.msg, type: res.status}, function () {
                        location.reload();
                    });
                }
            }
        );
    });
}

function changeStatus(target) {
    var uid = $(target).data('uid');
    $.ajax({
            type: "POST",
            url: '/media/category/update-status',
            dataType: 'json',
            data: {id: uid},
            success: function (res) {
                $(target).text(res.data.name);
                if(res.data.status == 1) {
                    $(target).removeClass('btn-danger').addClass('btn-success');
                } else {
                    $(target).removeClass('btn-success').addClass('btn-danger');
                }
            },
            error: function (resp) {
                alert('Erorr!');
            }
        }
    );
}