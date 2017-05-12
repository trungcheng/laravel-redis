@extends('layouts.master')

@section('main_content')
<style>
    #article_panel > div > div > div > div.col-md-12 > div > span > span.selection > span {
        height: 34px;
        border-radius: 0px;
    }
</style>
<section class="content-header" style="margin-bottom: 10px;">
    <h1>Tạo thông báo</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
        <li class="active">{{ trans('menu.media_zone') }}</li>
        <li class="active">Tạo thông báo</li>
    </ol>
</section>
<!-- Main content -->
<section>
    <div class="row" style="margin-left: 20px;">
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    <div class="active tab-pane" id="settings">
                        <form id="my_profile" class="form-horizontal" action="" method="post">
                            <div class="form-group">
                                <label><i class="fa fa-list-ul"></i> Title push</label>
                                <input type="name" class="form-control" style="width: 98%;" id="title-push" name="title-push" placeholder="Title ..." @if(isset($notify)) value="{{$notify->title}}" @endif />
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-paragraph"></i> Content push</label>
                                <textarea class="form-control" style="width: 98%;" name="content-push" rows="4" id="content-push" placeholder="Content ...">@if(isset($notify)) {{$notify->content}} @endif</textarea>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-paragraph"></i> Type notify</label>
                                <select class="form-control" style="width: 98%;" name="type_notify">
                                    <option value="content_text" @if(isset($notify) && $notify->type=='content_text') selected @endif> Thông báo </option>
                                    <option value="content_recipe" @if(isset($notify) && $notify->type=='content_live') selected @endif> Công thức </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="start_date"> Time push</label>
                                <input name="start_date" style="width: 98%;" type="text" class="form-control" id="start_date" @if(isset($notify)) value="{{$notify->date_push}}" @endif>
                            </div>
                            <input type="hidden" class="form-control" id="in-notify-id" name="in-notify-id" @if(isset($notify)) value="{{$notify->id}}" @endif />
                                   <div class="form-group">
                                <div class="col-sm-10">
                                    <button onclick="javascript:CreateNotify();" type="button" class="btn btn-danger">Tạo thông báo</button>
                                </div>
                            </div>
                        </form>
                    </div><!-- /.tab-pane -->
                </div><!-- /.tab-content -->
            </div><!-- /.nav-tabs-custom -->
    </div><!-- /.row -->
</section>

@stop

@section('custom_header')
<link rel="stylesheet" href="{{ asset('dist/css/jquery.datetimepicker.css') }}" />
@stop

@section('custom_footer')
<script type="text/javascript" src="{{ asset('dist/js/jquery.datetimepicker.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {
    $("#start_date").datetimepicker({
        timepicker: false,
        format: "Y-m-d H:i:s"
    });
});

function CreateNotify() {
    var title = $('input[name=title-push]').val();
    var content = $('textarea[name=content-push]').val();
    var type = $('select[name=type_notify]').val();
    var time_push = $('input[name=start_date]').val();
    var api = "action-create";

    var str_data = 'title=' + title + '&content=' + content + '&type=' + type + '&time_push=' + time_push;
    $.ajax({
        type: 'POST',
        url: api,
        data: str_data,
        success: function (obj) {
            obj = $.parseJSON(obj);
            if (obj.status == 200) {
                swal({
                    title: "Thành Công!",
                    text: "Bạn Tạo Thông Báo Thành Công",
                    showCancelButton: false,
                    type: "success",
                    animation: "slide-from-top"
                }, function () {
                    location.reload();
                });
            }
        },
        error: function (a, b, c) {
        }
    });
}

function reset() {
    $('#tblSortUser input[type=checkbox]').each(function () {
        $(this).removeAttr("checked", "checked");
    });
    $('input[name=title-push]').val('');
    $('textarea[name=content-push]').val('');
    $('select[name=type_notify]').val('content_text');
    $('input[name=start_date]').val('');
}
</script>
@stop