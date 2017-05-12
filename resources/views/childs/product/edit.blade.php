@extends('layouts.master')

@section('main_content')
    <?php
        $replace_path_thumb = str_contains($product->thumbnail, 'zdata') ? env('REPLACE_PATH_2') : env('REPLACE_PATH');
    ?>
    <section class="content-header">
        <h1>{{trans('product.edit_pro')}}</h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{trans('product.edit_pro')}}</li>
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
                    <form id="editProduct" role="form" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" id="productId" value="{{ $product['id'] }}" />
                        <div class="box-body">
                            <div class="form-group">
                                <label for="proName">{{trans('product.name')}}</label>
                                <input value="{{$product['name']}}" name="proName" type="text" class="form-control" id="proName">
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label><i class="fa fa-paragraph"></i> {{trans('product.desc')}}</label>
                                <textarea class="form-control" name="proDesc" rows="4"
                                          id="editor"
                                          placeholder="{{ trans('product.holder') }}">{{$product['description']}}</textarea>
                                <small class="pull-right" style="margin-top: -25px;margin-right: 5px;">
                                    (words left: <span
                                        id="word_left"></span>)
                                </small>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="proPrice">{{trans('product.price')}}</label>
                                <input value="{{$product['price']}}" name="proPrice" type="text" class="form-control" id="proPrice">
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="proPoint">{{trans('product.point')}}</label>
                                <input value="{{$product['point']}}" placeholder="{{ trans('product.holderPoint') }}" name="proPoint" type="text" class="form-control" id="proPoint">
                            </div>
                        </div>
                        <div class="box box-solid"> 
                            <div class="box-header with-border">
                                <i class="fa fa-image"></i>
                                <h3 class="box-title">Ảnh đại diện</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body" style="width: 40%">
                                <a class="btn btn-block btn-danger fa fa-trash"
                                   style="width:35px;position: relative;top:0px ;float: right;display: none;"
                                   onclick="javascript:removeImage(this,1);">
                                </a>
                                <?php
                                if (str_contains($product->thumbnail, 'http') === false) {
                                ?>
                                <img src="{{ env('MEDIA_PATH') . str_replace($replace_path_thumb , '' , $product->thumbnail )}}"
                                     onclick="BrowseServer('id_of_the_target_input');" id="image_replace"
                                     style="margin-top:-28px;cursor: pointer;max-height: 200px;width:100%;">
                                <?php
                                } else {
                                ?>
                                <img src="{{ $product->thumbnail }}"
                                     onclick="BrowseServer('id_of_the_target_input');" id="image_replace"
                                     style="margin-top:-28px;cursor: pointer;max-height: 200px;width:100%;">
                                <?php
                                }
                                ?>
                                <div class="preview-placeholder" id="replace" style="display:none;">
                                    <div>
                                        <i class="fa fa-plus fa-2x"
                                           onclick="BrowseServer('id_of_the_target_input');"></i><br>
                                        <h4 class="text-muted">Bấm vào chọn ảnh</h4>
                                    </div>
                                </div>

                            </div><!-- /.box-body -->
                            <input id="id_of_the_target_input" type="hidden" name="thumbnail"
                                   value="{{$product->thumbnail}}"/>
                        </div><!-- /.box -->
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">{{ trans('product.edit_pro') }}</button>
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

@section('custom_footer')
<script type="text/javascript">
    $('.fa-trash').show();
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