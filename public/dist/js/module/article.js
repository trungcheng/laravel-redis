$('#article_form').submit(function (event) {
    event.preventDefault();
    $categories = [];
    $array = $('input[name="category[]"]:checked').each(function () {
        if ($(this).is(':checked'))
            $categories.push($(this).val());
    });

    var formData = {
        _token: $("input[name='_token']").val(),
        type: 'Review',
        content: $("textarea[name='content']").val(),
        status: $('select[name="status"]').val() === '' ? '' : $('select[name="status"]').val(),
        title: $("input[name='title']").val() === '' ? swal({
            title: 'Chưa nhập tiêu đề',
            type: 'error'
        }) : $("input[name='title']").val(),
        title_extra: $("input[name='title_extra']").val(),
        ward: $("select[name='ward']").val(),
        type_article: $("select[name='type_article']").val(),
        address: $("input[name='address']").val(),
        price: $("input[name='price']").val(),
        phone: $("input[name='phone']").val(),
        time_action: $("input[name='time_action']").val(),
        description: $("textarea[name='description']").val(),
        publish_date: $("input[name='publish_date']").val(),
        publish_time: $("input[name='publish_time']").val(),
        open_time: $("input[name='open_time']").val(),
        close_time: $("input[name='close_time']").val(),
        latitude: $("input[name='latitude']").val(),
        longitude: $("#longit").val(),
        tags: $("input[name='tags']").val(),
        related: $("input[name='related']").val(),
        category: $categories.length === 0 ? swal({
            title: 'Chưa chọn chuyên mục',
            type: 'error'
        }) : JSON.stringify($categories),
        thumbnail: $("input[name='thumbnail']").val() === '' ? swal({
            title: 'Chưa chọn ảnh đại diện',
            type: 'error'
        }) : $("input[name='thumbnail']").val(),
        thumbnail_extra: $("input[name='thumbnail_extra']").val(),
        parent_category: $("input[name='parent_id']").val() === '' ? swal({
            title: 'Chưa Chọn Chuyên Mục Cha Hãy Hover Vào Lá Cờ',
            type: 'error'
        }) : $("input[name='parent_id']").val(),
        gallery: $("input[name='gallery']").val(),
        seo_title: $("input[name='seo_title']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_title']").val(),
        seo_meta: $("input[name='seo_meta']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_meta']").val(),
        seo_description: $("textarea[name='seo_description']").val() === '' ? $("textarea[name='description']").val() : $("textarea[name='seo_description']").val(),
    };
    if (!formData.title || !formData.category || !formData.thumbnail || !formData.parent_category) {
        return;
    }
    $('button[type=submit]').hide();
    $.ajax({
        type: "POST",
        url: '/media/article/create/',
        dataType: 'json',
        data: formData,
        success: function (res) {
            event.preventDefault();
            swal({title: res.msg, type: res.status}, function (isConfirm) {
                if (isConfirm) {
                    location.reload();
                }
            });
        }
    }
    );
});

$('#recipe_form').submit(function (event) {
    event.preventDefault();
    $categories = [];
    $array = $('input[name="category[]"]:checked').each(function () {
        if ($(this).is(':checked'))
            $categories.push($(this).val());
    });

    $ingredients = [];
    $array = $('input[name="ingredients[]"]').each(function () {
        if ($(this).val() != '') {
            $ingredients.push($(this).val());
        } else {
            $ingredients = false;
        }
    });
    $quanlity = [];
    $array = $('input[name="quanlity[]"]').each(function () {
        if ($(this).val() != '') {
            $quanlity.push($(this).val());
        } else {
            swal('Thất Bại', 'Nhập thiếu box số lượng');
            $quanlity = false;
        }
    });
    $quanlity_type = [];
    $array = $('input[name="quanlity_type[]"]').each(function () {
        if ($(this).val() != '') {
            $quanlity_type.push($(this).val());
        } else {
            swal('Thất Bại', 'Nhập thiếu box đơn vị');
            $quanlity_type = false;
        }
    });

    $steps = [];

    $array = $('textarea[name="steps[]"]').each(function () {
        if ($(this).val() != '') {
            $steps.push($(this).val());
        } else {
            swal('Thất Bại', 'Nhập thiếu Hướng Dẫn');
            $steps = false;
        }
    });

    $end_count = $steps.length;
    if ($end_count === undefined) {
        $end_count = 1;
    }
    $formSteps = [];
    for ($i = 1; $i <= $end_count; $i++) {
        $image_steps = [];
        $array = $('input[name="files_step_' + $i + '[]"]').each(function () {
            if ($(this).val() != '') {
                $image_steps.push($(this).val());
            } else {
                $image_steps.push('');
            }
        });

        $formSteps[parseInt($i) - 1] = $image_steps;
    }

    if ($("select[name='slLevel']").val().length > 0) {
        $categories.push($("select[name='slLevel']").val());
    }
    var formData = {
        _token: $("input[name='_token']").val(),
        type: 'Recipe',
        status: $('select[name="status"]').val() === '' ? '' : $('select[name="status"]').val(),
        content: $("textarea[name='content']").val(),
        title: $("input[name='title']").val() === '' ? swal({
            title: 'Chưa nhập tiêu đề',
            type: 'error'
        }) : $("input[name='title']").val(),
        title_extra: $("input[name='title_extra']").val(),
        prep_time: $("select[name='prep_time']").val(),
        cook_time: $("select[name='cook_time']").val(),
        type_article: $("select[name='type_article']").val(),
        steps: $steps,
        files_steps: JSON.stringify($formSteps),
        ingredients: $ingredients == false ? swal('Thất Bại', 'Nhập thiếu box nguyên liệu') : $ingredients,
        quanlity: $quanlity == false ? swal('Thất Bại', 'Nhập thiếu box số lượng') : $quanlity,
        quanlity_type: $quanlity_type == false ? swal('Thất Bại', 'Nhập thiếu box đơn vị') : $quanlity_type,
        directions: $("textarea[name='directions']").val(),
        description: $("textarea[name='description']").val(),
        tags: $("input[name='tags']").val(),
        publish_date: $("input[name='publish_date']").val(),
        publish_time: $("input[name='publish_time']").val(),
        number_people: $("input[name='number_people']").val(),
        youtube: $("input[name='youtube']").val(),
        related: $("input[name='related']").val(),
        category: $categories.length === 0 ? swal({
            title: 'Chưa chọn chuyên mục',
            type: 'error'
        }) : JSON.stringify($categories),
        parent_category: $("input[name='parent_id']").val() === '' ? swal({
            title: 'Chưa Chọn Chuyên Mục Cha Hãy Hover Vào Lá Cờ',
            type: 'error'
        }) : $("input[name='parent_id']").val(),
        thumbnail: $("input[name='thumbnail']").val() === '' ? swal({
            title: 'Chưa chọn ảnh đại diện',
            type: 'error'
        }) : $("input[name='thumbnail']").val(),
        thumbnail_extra: $("input[name='thumbnail_extra']").val(),
        gallery: $("input[name='gallery']").val(),
        seo_title: $("input[name='seo_title']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_title']").val(),
        seo_meta: $("input[name='seo_meta']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_meta']").val(),
        seo_description: $("textarea[name='seo_description']").val() === '' ? $("textarea[name='description']").val() : $("textarea[name='seo_description']").val(),
    };
    if (!formData.title || !formData.category || !formData.thumbnail || !formData.ingredients || !formData.quanlity || !formData.quanlity_type || !formData.steps || !formData.parent_category) {
        return;
    }
    $('button[type=submit]').hide();
    $.ajax({
        type: "POST",
        url: '/media/recipe/create/',
        dataType: 'json',
        data: formData,
        success: function (res) {
            event.preventDefault();
            swal({title: res.msg, type: res.status}, function (isConfirm) {
                if (isConfirm) {
                    location.reload();
                }
            });
        }
    }
    );
});

$('#blog_form').submit(function (event) {
    event.preventDefault();
    $categories = [];
    $array = $('input[name="category[]"]:checked').each(function () {
        if ($(this).is(':checked'))
            $categories.push($(this).val());
    });
    var formData = {
        _token: $("input[name='_token']").val(),
        type: 'Blogs',
        content: $("textarea[name='content']").val(),
        status: $('select[name="status"]').val() === '' ? '' : $('select[name="status"]').val(),
        title: $("input[name='title']").val() === '' ? swal({
            title: 'Chưa nhập tiêu đề',
            type: 'error'
        }) : $("input[name='title']").val(),
        title_extra: $("input[name='title_extra']").val(),
        publish_date: $("input[name='publish_date']").val(),
        publish_time: $("input[name='publish_time']").val(),
        category: JSON.stringify($categories),
        description: $("textarea[name='description']").val(),
        tags: $("input[name='tags']").val(),
        related: $("input[name='related']").val(),
        type_article: $("select[name='type_article']").val(),
        thumbnail: $("input[name='thumbnail']").val() === '' ? swal({
            title: 'Chưa chọn ảnh đại diện',
            type: 'error'
        }) : $("input[name='thumbnail']").val(),
        thumbnail_extra: $("input[name='thumbnail_extra']").val(),
        parent_category: $("input[name='parent_id']").val() === '' ? swal({
            title: 'Chưa Chọn Chuyên Mục Cha Hãy Hover Vào Lá Cờ',
            type: 'error'
        }) : $("input[name='parent_id']").val(),
        gallery: $("input[name='gallery']").val(),
        seo_title: $("input[name='seo_title']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_title']").val(),
        seo_meta: $("input[name='seo_meta']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_meta']").val(),
        seo_description: $("textarea[name='seo_description']").val() === '' ? $("textarea[name='description']").val() : $("textarea[name='seo_description']").val(),
    };
    if (!formData.title || !formData.thumbnail || !formData.parent_category) {
        return;
    }
    $('button[type=submit]').hide();
    $.ajax({
        type: "POST",
        url: '/media/blogs/create/',
        dataType: 'json',
        data: formData,
        success: function (res) {
            event.preventDefault();
            swal({title: res.msg, type: res.status}, function (isConfirm) {
                if (isConfirm) {
                    location.reload();
                }
            });
        }
    }
    );
});

$('#article_form_edit').submit(function (event) {
    event.preventDefault();
    $categories = [];
    $array = $('input[name="category[]"]:checked').each(function () {
        if ($(this).is(':checked'))
            $categories.push($(this).val());
    });
    var id = $('input[name=id]').val() === '' ? null : $('input[name=id]').val();

    var formData = {
        _token: $("input[name='_token']").val(),
        id: $('input[name=id]').val() === '' ? null : $('input[name=id]').val(),
        status: $('select[name="status"]').val() === '' ? '' : $('select[name="status"]').val(),
        type: 'Review',
        content: $("textarea[name='content']").val(),
        title: $("input[name='title']").val() === '' ? swal({
            title: 'Chưa nhập tiêu đề',
            type: 'error'
        }) : $("input[name='title']").val(),
        title_extra: $("input[name='title_extra']").val(),
        address: $("input[name='address']").val(),
        price: $("input[name='price']").val(),
        phone: $("input[name='phone']").val(),
        ward: $("select[name='ward']").val(),
        type_article: $("select[name='type_article']").val(),
        time_action: $("input[name='time_action']").val(),
        publish_date: $("input[name='publish_date']").val(),
        publish_time: $("input[name='publish_time']").val(),
        open_time: $("input[name='open_time']").val(),
        close_time: $("input[name='close_time']").val(),
        latitude: $("input[name='latitude']").val(),
        longitude: $("#longit").val(),
        description: $("textarea[name='description']").val(),
        tags: $("input[name='tags']").val(),
        related: $("input[name='related']").val(),
        category: $categories.length === 0 ? swal({
            title: 'Chưa chọn chuyên mục',
            type: 'error'
        }) : JSON.stringify($categories),
        thumbnail: $("input[name='thumbnail']").val() === '' ? swal({
            title: 'Chưa chọn ảnh đại diện',
            type: 'error'
        }) : $("input[name='thumbnail']").val(),
        thumbnail_extra: $("input[name='thumbnail_extra']").val(),
        parent_category: $("input[name='parent_id']").val() === '' ? swal({
            title: 'Chưa Chọn Chuyên Mục Cha Hãy Hover Vào Lá Cờ',
            type: 'error'
        }) : $("input[name='parent_id']").val(),
        gallery: $("input[name='gallery']").val(),
        seo_title: $("input[name='seo_title']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_title']").val(),
        seo_meta: $("input[name='seo_meta']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_meta']").val(),
        seo_description: $("textarea[name='seo_description']").val() === '' ? $("textarea[name='description']").val() : $("textarea[name='seo_description']").val(),
    };
    if (!formData.title || !formData.category || !formData.thumbnail || !formData.parent_category) {
        return;
    }
    $('button[type=submit]').hide();

    $.ajax({
        type: "POST",
        url: '/media/article/edit/' + id,
        dataType: 'json',
        data: formData,
        success: function (res) {
            event.preventDefault();
            swal({title: res.msg, type: res.status}, function (isConfirm) {
                if (isConfirm) {
                    location.reload();
                }
            });
        }
    }
    );
});

$('#blog_form_edit').submit(function (event) {
    event.preventDefault();
    var id = $('input[name=id]').val() === '' ? null : $('input[name=id]').val();
    $categories = [];
    $array = $('input[name="category[]"]:checked').each(function () {
        if ($(this).is(':checked'))
            $categories.push($(this).val());
    });

    var formData = {
        _token: $("input[name='_token']").val(),
        id: $('input[name=id]').val() === '' ? null : $('input[name=id]').val(),
        status: $('select[name="status"]').val() === '' ? '' : $('select[name="status"]').val(),
        type: 'Blogs',
        content: $("textarea[name='content']").val(),
        title: $("input[name='title']").val() === '' ? swal({
            title: 'Chưa nhập tiêu đề',
            type: 'error'
        }) : $("input[name='title']").val(),
        title_extra: $("input[name='title_extra']").val(),
        category: JSON.stringify($categories),
        type_article: $("select[name='type_article']").val(),
        description: $("textarea[name='description']").val(),
        publish_date: $("input[name='publish_date']").val(),
        publish_time: $("input[name='publish_time']").val(),
        tags: $("input[name='tags']").val(),
        related: $("input[name='related']").val(),
        thumbnail: $("input[name='thumbnail']").val() === '' ? swal({
            title: 'Chưa chọn ảnh đại diện',
            type: 'error'
        }) : $("input[name='thumbnail']").val(),
        thumbnail_extra: $("input[name='thumbnail_extra']").val(),
        parent_category: $("input[name='parent_id']").val() === '' ? swal({
            title: 'Chưa Chọn Chuyên Mục Cha Hãy Hover Vào Lá Cờ',
            type: 'error'
        }) : $("input[name='parent_id']").val(),
        gallery: $("input[name='gallery']").val(),
        seo_title: $("input[name='seo_title']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_title']").val(),
        seo_meta: $("input[name='seo_meta']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_meta']").val(),
        seo_description: $("textarea[name='seo_description']").val() === '' ? $("textarea[name='description']").val() : $("textarea[name='seo_description']").val(),
    };
    if (!formData.title || !formData.thumbnail || !formData.category || !formData.parent_category) {
        return;
    }
    $('button[type=submit]').hide();
    $.ajax({
        type: "POST",
        url: '/media/blogs/edit/' + id,
        dataType: 'json',
        data: formData,
        success: function (res) {
            event.preventDefault();
            swal({title: res.msg, type: res.status}, function (isConfirm) {
                if (isConfirm) {
                    location.reload();
                }
            });
        }
    }
    );
});

$('#recipe_form_edit').submit(function (event) {
    event.preventDefault();
    $categories = [];
    $array = $('input[name="category[]"]:checked').each(function () {
        if ($(this).is(':checked'))
            $categories.push($(this).val());
    });
    if ($("select[name='slLevel']").val().length > 0) {
        $categories.push($("select[name='slLevel']").val());
    }
    $ingredients = [];
    $array = $('input[name="ingredients[]"]').each(function () {
        if ($(this).val() != '') {
            $ingredients.push($(this).val());
        } else {
            $ingredients = false;
        }
    });
    $quanlity = [];
    $array = $('input[name="quanlity[]"]').each(function () {
        if ($(this).val() != '') {
            $quanlity.push($(this).val());
        } else {
            swal('Thất Bại', 'Nhập thiếu box số lượng');
            $quanlity = false;
        }
    });
    $quanlity_type = [];
    $array = $('input[name="quanlity_type[]"]').each(function () {
        if ($(this).val() != '') {
            $quanlity_type.push($(this).val());
        } else {
            swal('Thất Bại', 'Nhập thiếu box đơn vị');
            $quanlity_type = false;
        }
    });


    $steps = [];

    $array = $('textarea[name="steps[]"]').each(function () {
        if ($(this).val() != '') {
            $steps.push($(this).val());
        } else {
            swal('Thất Bại', 'Nhập thiếu Hướng Dẫn');
            $steps = false;
        }
    });

    $end_count = $steps.length;
    if ($end_count === undefined) {
        $end_count = 1;
    }
    $formSteps = [];
    for ($i = 1; $i <= $end_count; $i++) {
        $image_steps = [];
        $array = $('input[name="files_step_' + $i + '[]"]').each(function () {
            if ($(this).val() != '') {
                $image_steps.push($(this).val());
            } else {
                $image_steps.push('');
            }
        });

        $formSteps[parseInt($i) - 1] = $image_steps;
    }

    var id = $('input[name=id]').val() === '' ? null : $('input[name=id]').val();

    var formData = {
        _token: $("input[name='_token']").val(),
        id: $('input[name=id]').val() === '' ? null : $('input[name=id]').val(),
        type: 'Recipe',
        status: $('select[name="status"]').val() === '' ? '' : $('select[name="status"]').val(),
        steps: $steps,
        files_steps: JSON.stringify($formSteps),
        content: $("textarea[name='content']").val(),
        title: $("input[name='title']").val() === '' ? swal({
            title: 'Chưa nhập tiêu đề',
            type: 'error'
        }) : $("input[name='title']").val(),
        type_article: $("select[name='type_article']").val(),
        event_id: $("select[name='event']").val(),
        title_extra: $("input[name='title_extra']").val(),
        prep_time: $("select[name='prep_time']").val(),
        cook_time: $("select[name='cook_time']").val(),
        directions: $("textarea[name='directions']").val(),
        description: $("textarea[name='description']").val(),
        ingredients: $ingredients == false ? swal('Thất Bại', 'Nhập thiếu box nguyên liệu') : $ingredients,
        quanlity: $quanlity == false ? swal('Thất Bại', 'Nhập thiếu box số lượng') : $quanlity,
        quanlity_type: $quanlity_type == false ? swal('Thất Bại', 'Nhập thiếu box đơn vị') : $quanlity_type,
        publish_date: $("input[name='publish_date']").val(),
        publish_time: $("input[name='publish_time']").val(),
        tags: $("input[name='tags']").val(),
        number_people: $("input[name='number_people']").val(),
        youtube: $("input[name='youtube']").val(),
        related: $("input[name='related']").val(),
        category: $categories.length === 0 ? swal({
            title: 'Chưa chọn chuyên mục',
            type: 'error'
        }) : JSON.stringify($categories),
        thumbnail: $("input[name='thumbnail']").val() === '' ? swal({
            title: 'Chưa chọn ảnh đại diện',
            type: 'error'
        }) : $("input[name='thumbnail']").val(),
        thumbnail_extra: $("input[name='thumbnail_extra']").val(),
        parent_category: $("input[name='parent_id']").val() === '' ? swal({
            title: 'Chưa Chọn Chuyên Mục Cha Hãy Hover Vào Lá Cờ',
            type: 'error'
        }) : $("input[name='parent_id']").val(),
        gallery: $("input[name='gallery']").val(),
        seo_title: $("input[name='seo_title']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_title']").val(),
        seo_meta: $("input[name='seo_meta']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_meta']").val(),
        seo_description: $("textarea[name='seo_description']").val() === '' ? $("textarea[name='description']").val() : $("textarea[name='seo_description']").val(),
    };

    if (!formData.title || !formData.category || !formData.thumbnail || !formData.ingredients || !formData.quanlity || !formData.quanlity_type || !formData.steps || !formData.parent_category) {
        return;
    }
    $('button[type=submit]').hide();
    $.ajax({
        type: "POST",
        url: '/media/recipe/edit/' + id,
        dataType: 'json',
        data: formData,
        success: function (res) {
            event.preventDefault();
            swal({title: res.msg, type: res.status}, function (isConfirm) {
                if (isConfirm) {
                    location.reload();
                }
            });
        }
    }
    );
});

function deleteArticle(target) {
    var article_id = $(target).data('article_id');
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
            url: '/media/article/delete',
            dataType: 'json',
            data: {id: article_id, select_action: 'Delete', type: 'ajax'}, // serializes the form's elements.
            success: function (res) {
                swal({title: res.msg, type: res.status});
                $('tr.post-item.post-id-' + article_id).remove();
            },
            error: function (resp) {
                alert('Erorr!');
            }
        }
        );
    });
}

// review Article
function reviewArticle(target) {
    var article_id = $(target).data('article_id');
    loadModalContent('reviewArticleModalBody', '/media/article/review/' + article_id)
    $("#reviewArticleModal").modal('show');
}

// mo cua so chon list to web publish
$("#btn-status").on('click', function () {
    var status = $(this).attr('status');
    if (status == "off") {
        $(".modal-body #webPublish ").slideDown(300);
        $(".modal-body #viewReviewArticle ").slideUp(300);
        $(this).html('Back');
        $(this).attr('status', 'on');
    }
    if (status == "on") {
        $(".modal-body #webPublish ").slideUp(300);
        $(".modal-body #viewReviewArticle ").slideDown(300);
        $(this).html('Publish');
        $(this).attr('status', 'off');
    }
});
$('.modal-content .modal-header #close').click(function () {
    // click vao nut dong cua so thi dong bang publish
    $(".modal-body #webPublish ").slideDown(300);
    $(".modal-body #viewReviewArticle ").slideUp(300);
    $('.modal-content .modal-header #btn-status').html('Publish');
    $('.modal-content .modal-header #btn-status').attr('status', 'off');
});
$("html").click(function (e) {                          // click ra ngoai dong cua so thi dong bang publish
    if ($('.modal-content').is(":visible")) {
    } else {
        $('.modal-body #webPublish').slideDown(300);
        $('.modal-body #viewReviewArticle').slideUp(300);
        $('.modal-content .modal-header #btn-status').html('Publish');
        $('.modal-content .modal-header #btn-status').attr('status', 'off');
    }
});

// submit to list web publish
function summitToWebPublish(target) {
    var article_id = $(target).data('article_id');
    var type_to_publish = $(target).data('type');
    var list = [];
    $array = $('input[name="listWeb[]"]:checked').each(function () {
        if ($(this).is(':checked'))
            list.push($(this).val());
    });
    if (list == '') {
        alert("Error ! Choose Web Fail !");
    } else {
        $.ajax({
            type: "POST",
            url: '/media/article/submit',
            dataType: 'json',
            data: {id: article_id, st: type_to_publish, li: list},
            success: function (res) {
                if (res.status == "error") {
                    swal({title: res.msg, type: res.status});
                } else {
                    $('#reviewArticleModal').modal('hide');
                    swal({title: res.msg, type: res.status});
                    location.reload();
                }
            },
            error: function (resp) {
                alert('Erorr!');
            }
        }
        );
    }

}

// Active status Article
function activeArticle(target) {
    var article_id = $(target).data('article_id');
    var st = $(target).data('status');
    $.ajax({
        type: "POST",
        url: '/media/article/update-status',
        dataType: 'json',
        data: {id: article_id, st: st}, // serializes the form's elements.
        success: function (res) {
            $('#reviewArticleModal').modal('hide');
            swal({title: res.msg, type: res.status});
            setTimeout(function () {
                window.location.reload(1);
            }, 2000);
        },
        error: function (resp) {
            alert('Erorr!');
        }
    }
    );
}
function makeid() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 5; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function RemoveClass($class) {
    $($class).remove();
}