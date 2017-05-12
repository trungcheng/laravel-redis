@extends('layouts.master')
@section('main_content')
    <style>
        #article_panel > div > div > div > div.col-md-12 > div > span > span.selection > span {
            height: 34px;
            border-radius: 0px;
        }
    </style>
    <section class="content-header">
        <h1>Gallery</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{ trans('menu.media_zone') }}</li>
            <li class="active">{{ trans('menu.create_article') }}</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form role="form" method="post">
                {{csrf_field()}}
                <div class="col-md-7">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="article_panel">
                            <div class="box box-info">
                                <div class="box-body">
                                    <div id="append_steps">
                                        <div class="form-group" id="steps_1" data-step="1">
                                            <label><i class="fa fa-paragraph"></i> ĐĂNG VIDEO</label>
                                            <input type="name" class="form-control" name="name"
                                                   placeholder="Điền tiêu đề">
                                        </div>
                                        <div style="width:800px;margin-bottom:20px;" id="fileupload">
                                            <div class="row fileupload-buttonbar">
                                                <div class="col-lg-7">
                                                    <!-- The fileinput-button span is used to style the file input field as button -->
                                                    <span class="btn btn-success fileinput-button">
                                                        <i class="glyphicon glyphicon-upload"></i>
                                                        <span>Thêm Ảnh</span>
                                                        <input type="file" name="files[]" multiple>
                                                    </span>
                                                    <!-- The global file processing state -->
                                                    <span class="fileupload-process"></span>
                                                </div>
                                            </div>
                                            <table role="presentation" style="width:600px;"
                                                   class="table table-striped">
                                                <tbody class="files"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                    </div>
            </form>
            <span id="submit-form" class="btn btn-success"><i class="fa fa-save"></i>
                Lưu Gallery
            </span>
        </div>

        <style type="text/css">
            .slLevel, .slPrepTime {
                width: 100%;
                padding: 5px;
                border-radius: 5px;
                font-size: 16px;
                border: 1px solid #dadada;
            }

        </style>


    </section>
@stop
{{--Script Import --}}
<script>
    var text = document.getElementById("infoartist").value;
    text = text.replace(/\r?\n/g, '<br />');
</script>
@section('script_upload')
    <script src="{{ asset('js/jquery.ui.widget.js') }}"></script>
    <script src="/js/tmpl.min.js"></script>
    <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
    <script src="/js/load-image.all.min.js"></script>
    <!-- The Canvas to Blob plugin is included for image resizing functionality -->
    <script src="/js/canvas-to-blob.min.js"></script>
    <!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
    <script src="/js/bootstrap.min.js"></script>
    <!-- blueimp Gallery script -->
    <script src="/js/jquery.blueimp-gallery.min.js"></script>
    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="{{ asset('js/jquery.iframe-transport.js') }}"></script>
    <!-- The basic File Upload plugin -->
    <script src="{{ asset('js/jquery.fileupload.js') }}"></script>
    <!-- The File Upload processing plugin -->
    <script src="{{ asset('js/jquery.fileupload-process.js') }}"></script>
    <!-- The File Upload image preview & resize plugin -->
    <script src="{{ asset('js/jquery.fileupload-image.js') }}"></script>
    <!-- The File Upload audio preview plugin -->
    <script src="{{ asset('js/jquery.fileupload-audio.js') }}"></script>
    <!-- The File Upload video preview plugin -->
    <script src="{{ asset('js/jquery.fileupload-video.js') }}"></script>
    <!-- The File Upload validation plugin -->
    <script src="{{ asset('js/jquery.fileupload-validate.js') }}"></script>
    <!-- The File Upload user interface plugin -->
    <script src="{{ asset('js/jquery.fileupload-ui.js') }}"></script>
    <!-- The main application script -->
    <script src="{{ asset('js/main.js') }}"></script>
@stop
@section('custom_footer')
    <script>
        var max_len = 200;
        $(document).ready(function () {
            $.getScript("https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js");
            $.getScript("{{ asset('dist/js/module/article.js') }}");
            $("textarea[name=description]").on('keyup', function () {
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

            //iCheck for checkbox and radio inputs
            $('input[type="checkbox"].minimal').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });

            $(".category").slimScroll({
                height: '250px'
            });
        });

        initTinyMCE("#editor", "{{url('/')}}");

        var urlobj;
        function BrowseServer(obj) {
            urlobj = obj;
            OpenServerBrowser(
                    "{{url('/')}}" + '/filemanager/index.html',
                    screen.width * 0.7,
                    screen.height * 0.7);
        }
        function OpenServerBrowser(url, width, height) {
            var iLeft = (screen.width - width) / 2;
            var iTop = (screen.height - height) / 2;
            var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes";
            sOptions += ",width=" + width;
            sOptions += ",height=" + height;
            sOptions += ",left=" + iLeft;
            sOptions += ",top=" + iTop;
            var oWindow = window.open(url, "BrowseWindow", sOptions);
        }

        function SetUrl(url, width, height, alt) {
            document.getElementById(urlobj).value = url;
            if (urlobj == 'id_of_the_target_input') {
                $('#replace').hide();
                $('.fa-trash').show();
                $('#image_replace').attr('src', '{{env("MEDIA_PATH")}}' + url.replace('{{env("REPLACE_PATH")}}', ''));
                $('#image_replace').show();
                oWindow = null;
            } else {
                $('#text-folder').html(url);
            }
        }
        $('input[name="tags"]').tagEditor({
            autocomplete: {
                delay: 0, // show suggestions immediately
                source: '/media/article/tag/',
                minLength: 3,
                maxlength: 255,
                placeholder: "Enter Tags In Here!",
                position: {collision: 'flip'},
            }
        });
        $('input[name="related"]').tagEditor({
            autocomplete: {
                delay: 0, // show suggestions immediately
                source: '/media/article/related/',
                minLength: 3,
                maxlength: 255,
                placeholder: "Enter Tags In Here!",
                position: {collision: 'flip'},
            }
        });
        $('input[name="ingredients[]"]').tagEditor({
            autocomplete: {
                delay: 0,
                source: '/media/article/search-ingredients/',
                minLength: 3,
                maxlength: 255,
                position: {collision: 'flip'},
            },
            maxTags: 1,
            style_height: true
        });
        $('.fa-trash').on('click', function () {
            $('#image_replace').hide();
            $('.fa-trash').hide();
            $('#replace').show();
            $('#id_of_the_target_input').attr('value', '');
        });
        $('#get_url_image').on('click', function () {
            swal({
                        title: "Link Image!",
                        text: "Write Url Here:",
                        type: "input",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        animation: "slide-from-top",
                        inputPlaceholder: "Write something"
                    },
                    function (inputValue) {
                        if (inputValue === false)
                            return false;
                        if (inputValue === "") {
                            swal.showInputError("You need to write something!");
                            return false
                        }
                        swal({title: 'Choose Image Success', type: 'success'}, function (isConfirm) {
                            if (isConfirm) {
                                $('#replace').hide();
                                $('.fa-trash').show();
                                $('#id_of_the_target_input').attr('value', inputValue);
                                $('#image_replace').attr('src', inputValue);
                                $('#image_replace').show();

                            }
                        });
                    });
        });
        $(function () {
            $(".timepicker").timepicker({
                showInputs: false
            });
            $('#datepicker').datepicker({
                autoclose: true
            });
        });
        $('.add_ingredients').on('click', function () {
            var str = makeid();
            $html = '<div class="ig_' + str + '"><br><input class="form-control" style="height: 30px;" name="ingredients[]" id="' + str + '" placeholder="Nguyên liệu"> <input class="" type="number" style="height: 30px;width:80px;margin-top:10px;" step="0.01" min="0" name="quanlity[]" placeholder="Số lượng"> <input class="" style="height: 30px;width:80px;" name="quanlity_type[]" placeholder="Đơn vị"><br><div onclick="RemoveClass(' + "'.ig_" + str + "'" + ')" style="margin-top:10px;cursor: pointer"> <i class="fa fa-close"></i>Xóa nguyên liệu </div></div>' +
                    '<script>' +
                    'createTag("' + str + '") ; ' +
                    '<\/script>';
            $('#append_ingredients').append($html);
            $(this).attr('data-id', str + 1);

        });
        function createTag(str) {
            $(document).ready(function () {
                $.getScript("{{ asset('dist/js/jquery-ui.js') }}", function () {
                    $.getScript("{{ asset('dist/js/caret.js') }}", function () {
                        $.getScript("{{ asset('dist/js/tag.js') }}", function () {
                            $('#' + str).tagEditor({
                                autocomplete: {
                                    delay: 0,
                                    source: '/media/article/search-ingredients/',
                                    minLength: 3,
                                    maxlength: 255,
                                    position: {collision: 'flip'},
                                },
                                maxTags: 1,
                                style_height: true
                            });
                        });

                    });
                });
            });

        }
        $('#fileupload').fileupload({
            url: '/upload-image'
        });

    </script>
@stop
{{--End Script--}}