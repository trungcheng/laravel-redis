@extends('layouts.master')

@section('main_content')
<?php
$key = isset($_GET['key']) ? strip_tags($_GET['key']) : '';
$name = isset($_GET['name']) ? strip_tags($_GET['name']) : '';
$user_type = isset($_GET['user_type']) ? strip_tags($_GET['user_type']) : '';
?>
<section class="content-header">
    <h1 style="margin-bottom: 10px;">Danh sách người dùng</h1>
</section>
<div style="margin: 5px 0px;padding:10px 5px; border-top: 1px solid #ddd;border-bottom: 1px solid #ddd">
    <form method="get" action="notify" autocomplete="off" role="form" class="form-inline">
        <div class="form-group">
            <input type="text" class="form-control search_top" name="name" id="name"
                   value="{{$name}}" autocomplete="off" placeholder="{{ trans('notify.search_by_name') }}"
                   style="width: 300px;border-radius: 4px;">
        </div>
        <button type="submit" class="btn btn-danger" name="search">Tìm kiếm</button>
    </form>
</div>
<section>
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
                                <th>Ảnh đại diện</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Quyền thành viên</th>
                                <th>Facebook</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user )
                            <?php
                            $avt = !empty($user->thumbnail) ? $user->thumbnail : 'http://www.gravatar.com/avatar/35575592399b0a4e352e4fbf4c256130?s=80&d=mm&r=g';
                            ?>
                            <tr>
                                <td>
                                    <label class="screen-reader-text" for="cb-select-{{$user['id']}}">{{$user['name']}}</label>
                                    <input type="checkbox" class="row-select-id" name="select_id[{{$user['id']}}]" value="{{$user['id']}}" id="cb-select-{{$user['id']}}">
                                    <input type="hidden" name="row-user[]" class="id_user_item" value="{{$user['id']}}">
                                </td>
                                <td>
                                    <p><img style="width:50px;border-radius: 100%;" src="{{$avt}}" alt="{{$user->name}}" /> </p>
                                </td>
                                <td><p>{{$user->name}}</p></td>
                                <td><p>{{$user->email}}</p></td>
                                <td><p>{{$user->user_type}}</p></td>
                                <td>
                                    <p>
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
                                    </p>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {!! $users->appends(request()->all())->render() !!}
    </div>
</section>

<section class="content-header">
    <h1 style="margin-bottom: 10px;">Danh sách thông báo</h1>
    <a href="{{ URL::to('notify/create') }}" class="btn btn-success"> Tạo thông báo </a>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
        <li class="active">Danh sách thông báo</li>
    </ol>
</section>
<div style="margin: 5px 0px;padding:10px 5px; border-top: 1px solid #ddd;border-bottom: 1px solid #ddd">
    <form method="get" action="notify" autocomplete="off" role="form" class="form-inline">
        <div class="form-group">
            <input type="text" class="form-control search_top" name="key" id="key"
                   value="{{$key}}" autocomplete="off" placeholder="{{ trans('notify.search_by_name') }}"
                   style="width: 300px;border-radius: 4px;">
        </div>

        <button type="submit" class="btn btn-danger" name="search">Tìm kiếm</button>
    </form>
</div>
<section>
    <div class="post-container">
        <div class="box box-solid">
            <div class="box-body no-padding">
                <div>
                    <table class="table table-striped" id="tblSortNotify">
                        <thead>
                            <tr>
                                <th scope="col" id="cb" class="manage-column column-cb check-column">
                                    <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                                    <input id="cb-select-all-1" type="checkbox" onclick="javascript:selectAllNotify(this);">
                                </th>
                                <th>Tiêu đề</th>
                                <th>Nội dung</th>
                                <th>Loại push</th>
                                <th>Người tạo</th>
                                <th>Ngày push</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifys as $notify )
                            <tr>
                                <td>
                                    <label class="screen-reader-text" for="cb-select-{{$notify['id']}}">{{$notify['title']}}</label>
                                    <input type="checkbox" class="row-select-id" name="select_id[{{$notify['id']}}]" value="{{$notify['id']}}" id="cb-select-{{$notify['id']}}">
                                    <input type="hidden" name="row-notify[]" class="id_notify_item" value="{{$notify['id']}}">
                                </td>
                                <td><p>{{$notify->title}}</p></td>
                                <td><p>{{$notify->content}}</p></td>
                                <td><p>{{$notify->type}}</p></td>
                                <td><p>{{$notify->creator}}</p></td>
                                <td><p>{{$notify->date_push}}</p></td>
                                <td><a href="{{route('edit-notify').'/'.$notify['id']}}">View</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="form-group">
    <label style="margin: 0px 10px;"> Loại thông báo </label>
    <select class="form-control" style="width: 150px;" name="push_type" data-live-search="true" onchange="javascript:slPushType();">
        <option value="VIEW_HOME"> Thông báo </option>
        <option value="VIEW_RECIPE"> Công thức </option>
        <option value="VIEW_RESTAURANT"> Nhà hàng </option>
        <option value="VIEW_BLOG"> Bài viết </option>
        <option value="VIEW_STREAM"> Live stream </option>
    </select>
    <label style="margin: 0px 10px;">Thiết bị</label>
    <select class="form-control" style="width: 150px;border-color: #ddd" name="device" data-live-search="true">
        <option value="all" > -Thiết bị- </option>
        <option value="android" > Android </option>
        <option value="ios" > IOS </option>
    </select>
    <label style="margin: 0px 10px;" id="article_ID"></label>
    <input type="text" name="txtArticleID" value="" style="border: 1px solid #ddd;display: none;padding: 6px;border-radius: 4px;" placeholder="Nhập mã bài viết ..." />
    <label style="margin: 0px 10px;">Đặt lịch push</label>
    <input id="time-push" style="width: 18%;border: 1px solid #ddd;border-radius: 3px;padding: 5px;mar" type="text" value="" />
</div>
<div style="margin-left: 20px;padding-bottom: 20px;" class="form-group">
    <!--<button id="btnPushText" class="btn btn-danger" name="push_device" onclick="javascript:pushDevice()" style="margin-right: 20px;">Push device</button>-->
    @if(Auth::user()->user_type == 'Admin')
    <button id="btnPushSelectUser" class="btn btn-danger" name="push_select" onclick="javascript:pushUser()">Push người dùng được chọn</button>
    <button id="btnPushAllUser" class="btn btn-danger" name="push_all" onclick="javascript:pushAllUser()">Push tất cả người dùng</button>
    @endif
</div>
@stop

@section('custom_header')
<link rel="stylesheet" href="{{ asset('dist/css/jquery.datetimepicker.css') }}">
<link href="{{ asset('plugins/iCheck/minimal/blue.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('dist/css/select2.css') }}" />
@stop

@section('custom_footer')
<script type="text/javascript" src="{{ asset('dist/js/select2.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('dist/js/module/article.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
<script src="{{ asset('dist/js/jquery.datetimepicker.js') }}"></script>
<script src="{{ asset('plugins/iCheck/icheck.js') }}"></script>
<script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
<script type="text/javascript">
        $(document).ready(function () {
            $("#time-push").datetimepicker({
                timepicker: false,
                format: "Y-m-d H:i:s"
            });
            slPushType();

            $("select[name=device]").select2();
            $("select[name=user_type]").select2();
            $("select[name=push_type]").select2();
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

        function selectAllNotify(tag) {
            if ($(tag).prop("checked")) {
                $('#tblSortNotify input[type=checkbox]').each(function () {
                    $(this).prop("checked", true);
                    $(this).attr("checked", "checked");
                });
            } else {
                $('#tblSortNotify input[type=checkbox]').each(function () {
                    $(this).prop("checked", false);
                    $(this).removeAttr("checked", "checked");
                });
            }

        }

        function pushUser() {
//    $('#btnPushText').attr("disabled", "disabled");
            var api = "notify/push";
            var push_type = $('select[name=push_type]').val();
            var device = $('select[name=device]').val();
            var article_id = $('input[name=txtArticleID]').val();
            var time_push = $('#time-push').val();
            var str_data = '';
            var id = '';
            var users_id = '';
            if (push_type != null) {
                str_data += 'push_type=' + push_type;
            }
            if (device != null) {
                str_data += '&device=' + device;
            }
            if (push_type != 'VIEW_HOME') {
                if (article_id != null && article_id != '0') {
                    str_data += '&article_id=' + article_id;
                } else {
                    alert('Bạn chưa nhập mã bài viết !');
                    return false;
                }
            }

            $('#tblSortNotify tbody input[class=row-select-id]:checked').each(function () {
                if (parseInt($(this).val()) > 0) {
                    id += $(this).val().trim() + ',';
                }
            });
            if (id != '') {
                str_data += '&id=' + id;
            } else {
                alert('Bạn chưa chọn thông báo !');
                return false;
            }

            $('#tblSortUser tbody input[class=row-select-id]:checked').each(function () {
                if (parseInt($(this).val()) > 0) {
                    users_id += $(this).val().trim() + ',';
                }
            });

            if (users_id != '') {
                str_data += '&users_id=' + users_id;
            } else {
                alert('Bạn chưa chọn người dùng !');
                return false;
            }

            if (time_push != '') {
                str_data += '&time_push=' + time_push;
            } else {
                alert('Bạn chưa đặt lịch push !');
                return false;
            }

            $.ajax({
                type: 'POST',
                url: api,
                timeout: 5000,
                data: str_data,
                success: function (obj) {
                    if (obj !== null) {
                        obj = $.parseJSON(obj);
                        if (obj.status == 200) {
                            alert('Bạn đã đặt lịch push thành công !');
                        }
                    }
                },
                error: function (a, b, c) {
                }
            });
        }

        function pushAllUser() {
//    $('#btnPushText').attr("disabled", "disabled");
            var check = confirm("Bạn có chắc muốn push tất cả người dùng ?");
            if (check == true) {
                var api = "notify/push-all";
                var push_type = $('select[name=push_type]').val();
                var device = $('select[name=device]').val();
                var article_id = $('input[name=txtArticleID]').val();
                var time_push = $('#time-push').val();
                var str_data = '';
                var id = '';
                if (push_type != null) {
                    str_data += 'push_type=' + push_type;
                }

                if (device != null) {
                    str_data += '&device=' + device;
                }

                if (push_type != 'VIEW_HOME') {
                    if (article_id != null && article_id != '0') {
                        str_data += '&article_id=' + article_id;
                    } else {
                        alert('Bạn chưa nhập mã bài viết !');
                        return false;
                    }
                }

                $('#tblSortNotify tbody input[class=row-select-id]:checked').each(function () {
                    if (parseInt($(this).val()) > 0) {
                        id += $(this).val().trim() + ',';
                    }
                });
                if (id != '') {
                    str_data += '&id=' + id;
                } else {
                    alert('Bạn chưa chọn thông báo !');
                    return false;
                }

                if (time_push != '') {
                    str_data += '&time_push=' + time_push;
                } else {
                    alert('Bạn chưa đặt lịch push !');
                    return false;
                }

                $.ajax({
                    type: 'POST',
                    url: api,
                    timeout: 10000,
                    data: str_data,
                    success: function (obj) {
                        if (obj !== null) {
                            obj = $.parseJSON(obj);
                            if (obj.status == 200) {
                                alert('Bạn đã đặt lịch push thành công !');
                            }
                        }
                    },
                    error: function (a, b, c) {
                    }
                });
            } else {
                return false;
            }

        }

        function slPushType() {
            var type = $('select[name=push_type]').val();
            if (type == 'VIEW_RECIPE') {
                $('#article_ID').text('Mã công thức');
                $('input[name=txtArticleID]').show();
            } else if (type == 'VIEW_RESTAURANT') {
                $('#article_ID').text('Mã nhà hàng');
                $('input[name=txtArticleID]').show();
            } else if (type == 'VIEW_BLOG') {
                $('#article_ID').text('Mã bài viết');
                $('input[name=txtArticleID]').show();
            } else if (type == 'VIEW_STREAM') {
                $('#article_ID').text('Mã bài viết');
                $('input[name=txtArticleID]').show();
            } else {
                $('#article_ID').text('');
                $('input[name=txtArticleID]').hide();
            }
        }
</script>
@stop