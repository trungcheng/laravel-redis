$('#addProduct').submit(function (e) {
    e.preventDefault();
    var dataProName = $("input[name='proName']").val();
    var dataProDesc = $("#editor").val();
    var dataProPrice = $("input[name='proPrice']").val();
    var dataProPoint = $("input[name='proPoint']").val();
    var dataProThumbnail = $("input[name='thumbnail']").val();
    // var formData = new FormData($(this)[0]);
    var formData = new FormData();
    formData.append('_token', $("input[name='_token']").val());
    formData.append('proName',
        dataProName === '' ? swal({
            title: 'Chưa nhập tên sản phẩm',
            type: 'error'
        }) : dataProName
    );
    formData.append('proDesc',
        dataProDesc === '' ? swal({
            title: 'Chưa nhập mô tả sản phẩm',
            type: 'error'
        }) : dataProDesc
    );
    formData.append('proPrice',
        dataProPrice === '' ? swal({
            title: 'Chưa nhập giá sản phẩm',
            type: 'error'
        }) : dataProPrice
    );
    formData.append('proPoint',
        dataProPoint === '' ? swal({
            title: 'Chưa nhập điểm sản phẩm',
            type: 'error'
        }) : dataProPoint
    );
    formData.append('thumbnail', 
        dataProThumbnail === '' ? swal({
            title: 'Chưa chọn ảnh đại diện',
            type: 'error'
        }) : dataProThumbnail
    );
    if (!dataProName || !dataProDesc || !dataProPrice || !dataProPoint || !dataProThumbnail) {
        return;
    }
    $.ajax({
        type: "POST",
        url: '/media/product/create',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        dataType: 'json',
        success: function (res) {
            e.preventDefault();
            swal({title: res.msg, type: res.status} , function(isConfirm){
                if(isConfirm) {
                    location.reload();
                }
            });
        }
    });
});

$('#editProduct').submit(function (e) {
    e.preventDefault();
    var dataProName = $("input[name='proName']").val();
    var dataProDesc = $("#editor").val();
    var dataProPrice = $("input[name='proPrice']").val();
    var dataProPoint = $("input[name='proPoint']").val();
    var dataProThumbnail = $("input[name='thumbnail']").val();
    // var formData = new FormData($(this)[0]);
    var formData = new FormData();
    formData.append('_token', $("input[name='_token']").val());
    formData.append('proName',
        dataProName === '' ? swal({
            title: 'Chưa nhập tên sản phẩm',
            type: 'error'
        }) : dataProName
    );
    formData.append('proDesc',
        dataProDesc === '' ? swal({
            title: 'Chưa nhập mô tả sản phẩm',
            type: 'error'
        }) : dataProDesc
    );
    formData.append('proPrice',
        dataProPrice === '' ? swal({
            title: 'Chưa nhập giá sản phẩm',
            type: 'error'
        }) : dataProPrice
    );
    formData.append('proPoint',
        dataProPoint === '' ? swal({
            title: 'Chưa nhập điểm sản phẩm',
            type: 'error'
        }) : dataProPoint
    );
    formData.append('thumbnail', 
        dataProThumbnail === '' ? swal({
            title: 'Chưa chọn ảnh đại diện',
            type: 'error'
        }) : dataProThumbnail
    );
    if (!dataProName || !dataProDesc || !dataProPrice || !dataProPoint || !dataProThumbnail) {
        return;
    }
    var id = $('#productId').val();
    $.ajax({
        type: "POST",
        url: '/media/product/edit/' + id,
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        dataType: 'json',
        success: function (res) {
            swal({title: res.msg, type: res.status} , function(isConfirm){
                if(isConfirm) {
                    location.reload();
                }
            });
        }
    });
});

function deleteProduct(target) {
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
            url: '/media/product/delete',
            dataType: 'json',
            data: {id: uid}, // serializes the form's elements.
            success: function (res) {
                swal({title: res.msg, type: res.status}, function () {
                    location.reload();
                });
            }
        });
    });
}

$("textarea[name=proDesc]").on('keyup', function () {
    var words = 0;
    if (this.value !== '') {
        var words = this.value.match(/\S+/g).length;
        if (words > max_len) {
            // Split the string on first 200 words and rejoin on spaces
            var trimmed = $(this).val().split(/\s+/, max_len).join(" ");
            // Add a space at the end to keep new typing making new words
            $(this).val(trimmed + " ");
        }
    }
    $('#word_left').text(max_len - words);
});
$('#proPrice').keyup(function (e) { 
    if(($(this).val().split(".")[0]).indexOf("00") > -1){
        $(this).val($(this).val().replace("00","0"));
    } else {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    }
});
$('#proPoint').keyup(function (e) { 
    $(this).val($(this).val().replace(/[^0-9]/g, ''));
});