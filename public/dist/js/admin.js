$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// load modal from URL
function loadModalContent(id, url) {
    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'html',
        ifModify: false,
        success: function (data) {
            $('#' + id).html(data);
        }
    });
}

// init tinymce
function initTinyMCE(target, url_filemanager) {
    tinymce.remove();
    tinymce.init({
        selector: target,
        plugins: [
            "advlist autoresize autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern"
        ],
        toolbar1: "undo redo bold italic forecolor backcolor alignleft alignjustify bullist numlist outdent indent insertfile",
        toolbar2: "pastetext | image | media | fontsizeselect link unlink | removeformat | blockquote",
        fontsize_formats: "8px 10px 12px 14px 16px 18px 20px 24px 36px",
        image_advtab: true,
        convert_urls: false,
        entity_encoding: "raw",
        file_browser_callback: function (field_name, url, type, win) {

            // from http://andylangton.co.uk/blog/development/get-viewport-size-width-and-height-javascript
            var w = window,
                d = document,
                e = d.documentElement,
                g = d.getElementsByTagName('body')[0],
                x = w.innerWidth || e.clientWidth || g.clientWidth,
                y = w.innerHeight || e.clientHeight || g.clientHeight;

            var cmsURL = url_filemanager + '/filemanager/tini.html?&field_name=' + field_name + '&langCode=' + tinymce.settings.language;

            if (type == 'image') {
                cmsURL = cmsURL + "&type=images";
            }

            tinyMCE.activeEditor.windowManager.open({
                file: cmsURL,
                title: 'Filemanager',
                width: x * 0.8,
                height: y * 0.9,
                resizable: "yes",
                close_previous: "no"
            });

        },
        init_instance_callback: "stickEditor"

    });
}

// global usage
$(function () {
    $(".select2").select2();

    // image async load
    $(".lazy-img").imageloader();
});

// init tinymce
function initTinyMCE(target, url_filemanager) {
    tinymce.remove();
    tinymce.init({
        selector: target,
        plugins: [
            "advlist autoresize autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern"
        ],
        toolbar1: "undo redo bold italic forecolor backcolor alignleft alignright aligncenter alignjustify bullist numlist outdent indent insertfile ",
        toolbar2: "pastetext image  media  fontsizeselect link unlink removeformat blockquote code fullscreen",
        fontsize_formats: "8px 10px 12px 14px 16px 18px 20px 24px 36px",
        image_advtab: true,
        convert_urls: false,
        entity_encoding: "raw",
        file_browser_callback: function (field_name, url, type, win) {

            // from http://andylangton.co.uk/blog/development/get-viewport-size-width-and-height-javascript
            var w = window,
                d = document,
                e = d.documentElement,
                g = d.getElementsByTagName('body')[0],
                x = w.innerWidth || e.clientWidth || g.clientWidth,
                y = w.innerHeight || e.clientHeight || g.clientHeight;

            var cmsURL = url_filemanager + '/filemanager/tini.html?&field_name=' + field_name + '&langCode=' + tinymce.settings.language;

            if (type == 'image') {
                cmsURL = cmsURL + "&type=images";
            }

            tinyMCE.activeEditor.windowManager.open({
                file: cmsURL,
                title: 'Filemanager',
                width: x * 0.8,
                height: y * 0.9,
                resizable: "yes",
                close_previous: "no"
            });

        },
        init_instance_callback: "stickEditor"

    });
}

function initTinyMCERecipe(target, url_filemanager) {
    tinymce.remove();
    tinymce.init({
        selector: target,
        plugins: [
            "advlist autoresize autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern"
        ],
        toolbar1: "undo redo bold italic forecolor backcolor alignleft alignright aligncenter alignjustify bullist numlist outdent indent insertfile ",
        toolbar2: "pastetext image  media  fontsizeselect link unlink removeformat blockquote code",
        fontsize_formats: "8px 10px 12px 14px 16px 18px 20px 24px 36px",
        image_advtab: true,
        convert_urls: false,
        entity_encoding: "raw",
        file_browser_callback: function (field_name, url, type, win) {

            // from http://andylangton.co.uk/blog/development/get-viewport-size-width-and-height-javascript
            var w = window,
                d = document,
                e = d.documentElement,
                g = d.getElementsByTagName('body')[0],
                x = w.innerWidth || e.clientWidth || g.clientWidth,
                y = w.innerHeight || e.clientHeight || g.clientHeight;

            var cmsURL = url_filemanager + '/filemanager/tini.html?&field_name=' + field_name + '&langCode=' + tinymce.settings.language;

            if (type == 'image') {
                cmsURL = cmsURL + "&type=images";
            }

            tinyMCE.activeEditor.windowManager.open({
                file: cmsURL,
                title: 'Filemanager',
                width: x * 0.8,
                height: y * 0.9,
                resizable: "yes",
                close_previous: "no"
            });

        },
        init_instance_callback: "stickEditor"

    });
}
function createEditor(target, url_filemanager) {

    tinymce.init({
        selector: target,
        plugins: [
            "advlist autoresize autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern"
        ],
        toolbar1: "undo redo bold italic forecolor backcolor alignleft alignright aligncenter alignjustify bullist numlist outdent indent insertfile ",
        toolbar2: "pastetext image  media  fontsizeselect link unlink removeformat blockquote code",
        fontsize_formats: "8px 10px 12px 14px 16px 18px 20px 24px 36px",
        image_advtab: true,
        convert_urls: false,
        entity_encoding: "raw",
        file_browser_callback: function (field_name, url, type, win) {

            // from http://andylangton.co.uk/blog/development/get-viewport-size-width-and-height-javascript
            var w = window,
                d = document,
                e = d.documentElement,
                g = d.getElementsByTagName('body')[0],
                x = w.innerWidth || e.clientWidth || g.clientWidth,
                y = w.innerHeight || e.clientHeight || g.clientHeight;

            var cmsURL = url_filemanager + '/filemanager/tini.html?&field_name=' + field_name + '&langCode=' + tinymce.settings.language;

            if (type == 'image') {
                cmsURL = cmsURL + "&type=images";
            }

            tinyMCE.activeEditor.windowManager.open({
                file: cmsURL,
                title: 'Filemanager',
                width: x * 0.8,
                height: y * 0.9,
                resizable: "yes",
                close_previous: "no"
            });

        },
        init_instance_callback: "stickEditor"

    });

}