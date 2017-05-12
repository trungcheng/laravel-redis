@extends('layouts.master')

@section('main_content')
    <?php
    $avatar = '';
    $link_video = '';
    $title = '';
    $width = '';
    $height = '';
    if (\Cache::has('config_web_video')) {
        $config = json_decode(\Cache::get('config_web_video'));
        $link_video = isset($config->link_mp4) ? $config->link_mp4 : '';
        $title = isset($config->title) ? $config->title : '';
        $width = isset($config->width) ? $config->width : '';
        $height = isset($config->height) ? $config->height : '';
        $avatar = isset($config->avatar) ? $config->avatar : '';
    }
    ?>
    <section class="content-header">
        <h1>Cấu Hình</h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Cấu Hình</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="box box-primary">
                    <form id="addCategory" role="form" method="post">
                        {{csrf_field()}}
                        <div class="box-body">
                            <div class="form-group">
                                <label for="catTitle">Avatar</label>
                                <input name="title" value="{{$avatar}}" type="text" class="form-control" id="avatar">
                            </div>
                            <div class="form-group">
                                <label for="catTitle">Video MP4</label>
                                <input name="title" value="{{$link_video}}" type="text" class="form-control" id="video">
                            </div>
                            <div class="form-group">
                                <label for="catTitle">Title</label>
                                <input name="title" value="{{$title}}" type="text" class="form-control" id="title">
                            </div>
                            <div class="form-group">
                                <label for="catTitle">Width</label>
                                <input name="title" value="{{$width}}" type="number" min="0" class="form-control"
                                       id="width">
                            </div>
                            <div class="form-group">
                                <label for="catTitle">Height</label>
                                <input name="title" value="{{$height}}" type="number" min="0" class="form-control"
                                       id="height">
                            </div>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">{{ trans('category.add_cat') }}</button>
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
@section('custom_footer')
    <script type="text/javascript">
        $(document).ready(function () {
            $.getScript("https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js");
            $('#addCategory').submit(function (event) {
                event.preventDefault();
                var formData = {
                    _token: $("input[name='_token']").val(),
                    title: $("#title").val() === '' ? swal({
                        title: 'Chưa nhập Title',
                        type: 'error'
                    }) : $("#title").val(),
                    link_mp4: $("#video").val() === '' ? swal({
                        title: 'Chưa nhập Link',
                        type: 'error'
                    }) : $("#video").val(),
                    width: $("#width").val() === '' ? swal({
                        title: 'Chưa nhập Width',
                        type: 'error'
                    }) : $("#width").val(),
                    avatar : $("#avatar").val(),
                    height: $("#height").val() === '' ? swal({
                        title: 'Chưa nhập Height',
                        type: 'error'
                    }) : $("#height").val(),
                };
                if (!formData.title || !formData.width || !formData.height || !formData.link_mp4) {
                    return;
                }
                $.ajax({
                            type: "POST",
                            url: '/config/create',
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

            function isSlug(slug) {
                value = slug.trim();
                if (value == '') return true;
                var regex = /^([a-zA-Z0-9_.+-])+$/;
                return regex.test(value);
            }
        });
    </script>
@stop