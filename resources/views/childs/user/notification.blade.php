@extends('layouts.master')

@section('main_content')
<section class="content-header">
    <h1 style="margin-bottom: 10px;">{{ trans('menu.list_notif') }}</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">{{ trans('menu.media_zone') }}</li>
        <li class="active">{{ trans('menu.notification') }}</li>
    </ol>
</section>
<section class="content">
    <div>
        <div style="padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #ddd">
            <form method="get" action="/media/notification" autocomplete="off" role="form" class="form-inline">
                <div class="form-group">
                    <input type="text" class="form-control search_top" name="key" id="key"
                           value="{{old('key')}}"
                           autocomplete="off" placeholder="{{ trans('user.search_by_name') }}"
                           style="width: 300px;">
                </div>
                <div class="form-group">
                    <select class="form-control" name="user_type" data-live-search="true" data-width="150px">
                        <option value="" @if(!isset($request_all['user_type'])){{"selected"}}@endif >{{ trans('user.user_type') }}</option>
                        <option value="Admin" @if(isset($request_all['user_type']) && $request_all['user_type'] =='Admin' ){{"selected"}}@endif> Admin</option>
                        <option value="Editor" @if(isset($request_all['user_type']) && $request_all['user_type'] =='Editor' ){{"selected"}}@endif> Editor</option>
                        <option value="Normal" @if(isset($request_all['user_type']) && $request_all['user_type'] =='Normal' ){{"selected"}}@endif> Normal</option>
                        <option value="User_Normal" @if(isset($request_all['user_type']) && $request_all['user_type'] =='User_Normal' ){{"selected"}}@endif> User Normal</option>
                    </select>
                </div>
                <div class="form-group">
                    <select class="form-control" name="status" data-live-search="true" data-width="150px">
                        <option value="" @if(!isset($request_all['status'])){{"selected"}}@endif >{{ trans('user.status') }}</option>
                        <option value="Active" @if(isset($request_all['status']) && $request_all['status'] =='Active' ){{"selected"}}@endif> Active</option>
                        <option value="Inactive" @if(isset($request_all['status']) && $request_all['status'] =='Inactive' ){{"selected"}}@endif> Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-danger" name="search">Tìm kiếm</button>
            </form>
        </div>
        <div class="post-container">
            <div class="box box-solid">
                <div class="box-body no-padding">
                    <div>
                        <table class="table table-striped" id="tblSortUser">
                            <thead>
                                <tr>
                                    <th scope="col" id="cb" class="manage-column column-cb check-column">
                                        <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                                        <input id="cb-select-all-1" type="checkbox" onclick="javascript:selectAllUser(this);">
                                    </th>
                                    <th>{{ trans('user.avatar') }}</th>
                                    <th>{{ trans('user.name') }}</th>
                                    <th>{{ trans('user.email') }}</th>
                                    <th>{{ trans('user.user_type') }}</th>
                                    <th>{{ trans('user.status') }}</th>
                                    <th>{{ trans('user.facebook') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr id="row-data-{{$user['id']}}" class="post-item post-id-{{$user['id']}}">
                                    <td>
                                        <label class="screen-reader-text" for="cb-select-{{$user['id']}}">{{$user['name']}}</label>
                                        <input type="checkbox" class="row-select-id" name="select_id[{{$user['id']}}]" value="{{$user['id']}}" id="cb-select-{{$user['id']}}">
                                        <input type="hidden" name="row-user[]" class="id_user_item" value="{{$user['id']}}">
                                    </td>
                                    <td>
                                        @if(!empty($user->email))
                                        <img class="img-circle" alt="User Image" src="http://www.gravatar.com/avatar/35575592399b0a4e352e4fbf4c256130?s=80&d=mm&r=g"/>
                                        @else
                                        <img class="img-circle" alt="User Image" src="<?php echo get_gravatar($user->email); ?>"/>
                                        @endif
                                    </td>
                                    <td><p>{{ $user['name'] }}</p></td>
                                    <td><p>{{ $user['email'] }}</p></td>
                                    <td><p>{{ $user['user_type'] }}</p></td>
                                    <td><p>{{ $user['status'] }}</p></td>
                                    <td><p>
                                            <?php
                                            $user_id = str_replace('@facebook.vn', '', $user['email']);
                                            if (str_contains($user['email'], '@facebook.vn') == true) {
                                                ?> 
                                                <a href="https://www.facebook.com/app_scoped_user_id/<?php echo $user_id; ?>"><?php echo $user['name']; ?></a>
                                                <?php
                                            } else {
                                                echo '';
                                            }
                                            ?>
                                        </p></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <style>
                table tr td p span {cursor: pointer;color: #3c8dbc;}
                table tr td p span:hover {color: #72afd2;}
                input[type=checkbox]{border: 1px solid #bbb;
                                     background: #fff;color: #555;
                                     clear: none;cursor: pointer;
                                     display: inline-block;
                                     line-height: 0;height: 16px;
                                     margin: -4px 4px 0 0;
                                     outline: 0;
                                     padding: 0!important;text-align: center;
                                     vertical-align: middle;width: 16px;
                                     min-width: 16px;
                                     -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
                                     box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
                                     -webkit-transition: .05s border-color ease-in-out;
                                     transition: .05s border-color ease-in-out;
                }
            </style>
            {!! $users->appends(\Request::all())->render() !!}
            </form>
        </div>

        <div style="width: 50%;padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #ddd;overflow: hidden;">
            <div class="form-group">
                <label><i class="fa fa-list-ul"></i> Tiêu đề</label>
                <input type="name" class="form-control" id="title-push" name="title-push" placeholder="Nhập tiêu đề" value="">
            </div>
            <div class="form-group">
                <label><i class="fa fa-paragraph"></i> Nội dung</label>
                <textarea class="form-control" name="content-push" rows="4" id="content-push" placeholder="Nội dung"></textarea>
            </div>
            <div class="form-group">
                <label for="start_date"> Thời gian bắt đầu</label>
                <input name="start_date" type="text" class="form-control" id="start_date">
            </div>
            <div class="form-group">
                <button id="btnPushText" class="btn btn-danger" name="push_device" onclick="javascript:pushDevice()" style="margin-right: 20px;">Push device</button>
                <button id="btnPushAllDevices" class="btn btn-danger" name="push_all" onclick="javascript:pushAllDevices()">Push all devices</button>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="reviewArticleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div style="width: 70%;" class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button id="close" type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h2 class="col-md-9 modal-title" id="exampleModalLabel">{{ trans('article.review_article') }}</h2>
                <button id="btn-status" style="width:130px;margin-left: 60px;" status="off" id="summitToPublish"
                        class="btn btn-success" type="button">
                    {{ trans('article.publish') }}
                </button>
            </div>
            <div class="modal-body" id="reviewArticleModalBody"></div>
        </div>
    </div>
</div>
@stop

@section('custom_footer')

<link href="{{ asset('dist/css/jquery.datetimepicker.css') }}" rel="stylesheet">
<script src="{{ asset('dist/js/jquery.datetimepicker.js') }}"></script>
<script type="text/javascript">
                    $(document).ready(function () {
                        $("#start_date").datetimepicker({
                            timepicker: false,
                            format: "Y-m-d H:i:s",
                        });
                        $("#end_date").datetimepicker({
                            timepicker: false,
                            format: "Y-m-d H:i:s"
                        });
                    });
                    function selectAllUser(tag) {
                        if ($(tag).prop("checked")) {
                            $('#tblSortUser input[type=checkbox]').each(function () {
                                $(this).prop("checked", true);
                                $(this).attr("checked", "checked");
                            });
                        } else {
                            $('#tblSortUser input[type=checkbox]').each(function () {
                                $(this).prop("checked", false);
                                $(this).removeAttr("checked", "checked");
                            });
                        }

                    }

                    function pushDevice() {
//                    $('#btnPushText').attr("disabled", "disabled");
                        var start_date = $('input[name=start_date]').val();
//                            var end_date = $('input[name=end_date]').val();
//                        var device = $('select[name=device-push]').val();
                        var title = $('input[name=title-push]').val();
                        var content = $('textarea[name=content-push]').val();
                        var id = '';
                        $('#tblSortUser tbody input[class=row-select-id]:checked').each(function () {
                            if (parseInt($(this).val()) > 0) {
                                id += $(this).val().trim() + ',';
                            }
                        });
                        var api = "/media/notification/push-text";
//                            + start_date + '&end_date=' + end_date
                        var str_data = 'id=' + id + '&title=' + title + '&content=' + content + '&start_date=' + start_date;
                        $.ajax({
                            type: 'POST',
                            url: api,
                            timeout: 5000,
                            data: str_data,
                            success: function (obj) {
                                if (obj != null) {
                                    obj = $.parseJSON(obj);
//                            $('#btnPushText').removeAttr("disabled");
                                }
                            },
                            error: function (a, b, c) {
                            }
                        });
                    }

                    function pushAllDevices() {
//                    $('#btnPushText').attr("disabled", "disabled");
                        var start_date = $('input[name=start_date]').val();
//                        var end_date = $('input[name=end_date]').val();
//                        var device = $('select[name=device-push]').val();
                        var title = $('input[name=title-push]').val();
                        var content = $('textarea[name=content-push]').val();
                        var api = "/media/notification/push-text";

                        var str_data = 'title=' + title + '&content=' + content + '&start_date=' + start_date;
                        $.ajax({
                            type: 'POST',
                            url: api,
                            timeout: 5000,
                            data: str_data,
                            success: function (obj) {
                                if (obj != null) {
                                    obj = $.parseJSON(obj);
//                            $('#btnPushText').removeAttr("disabled");
                                }
                            },
                            error: function (a, b, c) {
                            }
                        });
                    }
</script>
@stop