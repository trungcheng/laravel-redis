@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1>{{trans('product.add')}}</h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{trans('product.add')}}</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"></h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form id="addProduct" role="form" method="POST">
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="form-group">
                                <label for="proName">{{trans('product.name')}}</label>
                                <input placeholder="{{ trans('product.holderName') }}" name="proName" type="text" class="form-control" id="proName">
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label><i class="fa fa-paragraph"></i> {{trans('product.desc')}}</label>
                                <textarea class="form-control" name="proDesc" rows="4"
                                          id="editor"
                                          placeholder="{{ trans('product.holderDesc') }}"></textarea>
                                <small class="pull-right" style="margin-top: -25px;margin-right: 5px;">
                                    (words left: <span
                                        id="word_left"></span>)
                                </small>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="proPrice">{{trans('product.price')}}</label>
                                <input placeholder="{{ trans('product.holderPrice') }}" name="proPrice" type="text" class="form-control" id="proPrice">
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="proPoint">{{trans('product.point')}}</label>
                                <input placeholder="{{ trans('product.holderPoint') }}" name="proPoint" type="text" class="form-control" id="proPoint">
                            </div>
                        </div>
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-image"></i>
                                <h3 class="box-title">{{ trans('product.image') }}</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body" style="width: 40%">
                                <a class="btn btn-block btn-danger fa fa-trash"
                                   style="width:35px;position: relative;top:0px ;float: right;display: none;" onclick="javascript:removeImage(this,1);">
                                </a>
                                {{--<i class="fa fa-trash" style="margin-left: 94%;cursor: pointer;display: none"></i>--}}
                                <img onclick="BrowseServer('id_of_the_target_input');" src="" id="image_replace"
                                     style="display:none;margin-top:-28px;cursor: pointer;max-height: 200px;width:100%;">
                                <div class="preview-placeholder" id="replace">
                                    <div>
                                        <i class="fa fa-plus fa-2x"
                                           onclick="BrowseServer('id_of_the_target_input');"></i><br>
                                        <h4 class="text-muted">Bấm Vào! Chọn Ảnh</h4>
                                    </div>
                                </div>

                            </div><!-- /.box-body -->
                            <input id="id_of_the_target_input" type="hidden" name="thumbnail"/>
                        </div><!-- /.box -->
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">{{ trans('product.add_pro') }}</button>
                        </div>
                    </form>
                </div>
                <!-- /.box -->
            </div>
            <!--/.col (left) -->
        </div>
        <!-- /.row -->
    </section>
@stop
{{--Script Import --}}
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
<script type="text/javascript">
    var max_len = 200;
    $(document).ready(function () {
        $.getScript("https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js");
        $.getScript("{{ asset('dist/js/module/product.js') }}");
    });

    var urlobj;
    function BrowseServer(obj) {
        urlobj = obj;
        OpenServerBrowser(
            "{{url('/')}}" + '/filemanager/index.html',
            screen.width * 0.7,
            screen.height * 0.7
        );
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
            $('#image_replace').attr('src', '{{env("MEDIA_PATH")}}' + url.replace('{{env("REPLACE_PATH_2")}}', ''));
            $('#image_replace').show();
            oWindow = null;
        } else if (urlobj == 'id_of_the_target_input_extra') {
            $('#replace_extra').hide();
            $('.fa-trash').show();
            $('#image_replace_extra').attr('src', '{{env("MEDIA_PATH")}}' + url.replace('{{env("REPLACE_PATH_2")}}', ''));
            $('#image_replace_extra').show();
            oWindow = null;
        } else {
            $('#text-folder').html(url);
        }
    }

    function removeImage(tag,index){
        if(index==1){
            $('#image_replace').hide();
        $('#replace').show();
        $('#id_of_the_target_input').attr('value', '');
        }else{
        $('#image_replace_extra').hide();
        $('#replace_extra').show();
        $('#id_of_the_target_input_extra').attr('value', '');
        }
        $(tag).hide();
    }
</script>
@stop
{{--End Script--}}