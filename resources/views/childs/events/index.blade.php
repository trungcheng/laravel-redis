@extends('layouts.master')

@section('main_content')
<section class="content-header">
    <h1>DANH SÁCH SỰ KIỆN</h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=>Events</li>
    </ol>
</section>
<section class="content">
    <div>
        <div style="padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #ddd">
            <form method="get" action="/events" autocomplete="off" role="form" class="form-inline">
                <div class="form-group">
                    <input type="text" class="form-control search_top" name="key" id="key"
                           value="<?php echo isset($_GET['key']) ? $_GET['key'] : ''; ?>"
                           autocomplete="off" placeholder="{{ trans('event.search_by_name') }}"
                           style="width: 235px;">
                </div>
                <div class="form-group">
                    <select class="form-control" name="status" data-live-search="true" style="width:150px;">
                        <option value="-1">{{ trans('event.status') }}</option>
                        <option @if(isset($request_all['status']) && (int)$request_all['status'] == 1){{"selected"}}@endif value="1">{{ trans('event.active') }}</option>
                        <option @if(isset($request_all['status']) && (int)$request_all['status'] == 0){{"selected"}}@endif value="0">{{ trans('event.verify') }}</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-danger" name="search">Tìm kiếm</button>
                <div class="form-group" style="float: right;">
                    <a href="/events/create" style="display: inline-block;padding: 5px 10px;border-radius: 2px;border: 1px solid #008d4c;background-color: #00a65a;color: #fff;">Tạo sự kiện mới</a>
                </div>
            </form>
        </div>
        <div class="post-container">
            <div class="box box-solid">
                <div class="box-body no-padding">
                    <div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <?php
                                    $order_id = \Request::get('order_id');
                                    $order_id == 'desc' ? $order_id = 'asc' : $order_id = 'desc';

                                    $order_type = \Request::get('order_type');
                                    $order_type == 'desc' ? $order_type = 'asc' : $order_type = 'desc';

                                    $order_created = \Request::get('order_created');
                                    $order_created == 'desc' ? $order_created = 'asc' : $order_created = 'desc';
                                    ?>
                                    <th><a href="?order_id={{$order_id}}">ID @if($order_id == 'asc')<i
                                                class="fa fa-angle-up"></i> @else <i
                                                class="fa fa-angle-down"></i> @endif </a></th>
                                    <th style="width: 25%;"><a href="?order_name={{$order_type}}">Tên sự kiện @if($order_type == 'asc')<i
                                                class="fa fa-angle-up"></i> @else <i
                                                class="fa fa-angle-down"></i> @endif  </a></th>
                                    <!--<th>Cách thức tham gia</th>-->
                                    <th>Loại sự kiện</th>
                                    <th>Thời gian bắt đầu</th>
                                    <th>Thời gian kết thúc</th>
                                    <th>Trạng thái</th>
                                    <th>Chức năng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $item )
                                <?php
                                $status = 'Verify';
                                if ((int) $item->status == 1) {
                                    $status = 'Active';
                                }
                                ?>
                                <tr id="" class="post-item">
                                    <td><p>{{$item->id}}</p></td>
                                    <td>
                                        <a href="/member/article?event={{$item->id}}"><p>{{$item->event_name}}</p></a>
                                    </td>

                                    <!--<td><?php // echo isset($item->rule) ? $item->rule : ''; ?></td>-->
                                    <td><p>{{$item->type}}</p></td>
                                    <td><p><input type="text" style="border: 0px;" id="begintime" name="begintime" value="{{$item->event_started}}"/></p></td>
                                    <td><p><input type="text" style="border: 0px;" id="begintime" name="begintime" value="{{$item->event_ended}}"/></p></td>
                                    <td><p>{{$status}}</p></td>
                                    <td>
                                        <p>
                                            <a href="/events/edit/{{$item->id}}" style="display: inline-block;padding: 5px 10px;border-radius: 2px;border: 1px solid #008d4c;background-color: #00a65a;color: #fff;margin-bottom: 5px;">Chỉnh sửa</a>
                                            @if($status == 'Active')
                                            <a href="javascript:void(0);" onclick="javascript:statusEvent({{$item->id}}, 0)" style="display: inline-block;padding: 5px 10px;border-radius: 2px;border: 1px solid #008d4c;background-color: #00a65a;color: #fff;margin-bottom: 5px;">Verify</a>
                                            @else
                                            <a href="javascript:void(0);" onclick="javascript:statusEvent({{$item->id}}, 1)" style="display: inline-block;padding: 5px 10px;border-radius: 2px;border: 1px solid #008d4c;background-color: #00a65a;color: #fff;margin-bottom: 5px;">Active</a>
                                            @endif
                                            <a href="javascript:void(0);" onclick="javascript:deleteEvent({{$item->id}})" style="display: inline-block;padding: 5px 10px;border-radius: 2px;border: 1px solid #008d4c;background-color: #00a65a;color: #fff;">Xóa sự kiện</a>
                                        </p>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {!! $events->appends(\Request::all())->render() !!}
            <style>
                table tr td p span {
                    cursor: pointer;
                    color: #3c8dbc;
                }

                table tr td p span:hover {
                    color: #72afd2;
                }
            </style>
            </form>
        </div>
    </div>
</section>

@stop

@section('custom_header')
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css') }}">
<link href="{{ asset('plugins/iCheck/minimal/blue.css') }}" rel="stylesheet">
@stop

@section('custom_footer')
@foreach($events as $item)
<script type="text/javascript">
            $('#submit_content_{{$item->id}}').on('click', function () {
    $content = $('#content_edit_{{$item->id}}').html();
            $('#input_content_{{$item->id}}').attr('value', $content);
            $('#content_form_{{$item->id}}').submit();
    });</script>
@endforeach
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('dist/js/module/article.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/iCheck/icheck.js') }}"></script>
<script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>

<script>
            (function (window, document) {
            $('.div-update-time').hide();
                    $("#begintime").datetimepicker({
            timepicker: false,
                    format: "Y-m-d H:i:s",
            });
                    $("#endtime").datetimepicker({
            timepicker: false,
                    format: "Y-m-d H:i:s"
            });
            });
            function deleteEvent(id) {
            link = "{{URL::to('/events/delete/')}}";
                    $.ajax({
                    type: 'POST',
                            url: link,
                            data: 'id=' + id,
                            success: function (obj) {
                            if (obj !== null) {
                            obj = $.parseJSON(obj);
                                    if (obj.status === 'success') {
                            location.reload();
                            }
                            }
                            },
                            error: function (a, b, c) {
                            }
                    });
            }
    function statusEvent(id, status) {
    link = "{{URL::to('/events/status/')}}";
            $.ajax({
            type: 'POST',
                    url: link,
                    data: 'id=' + id + '&status=' + status,
                    success: function (obj) {
                    if (obj !== null) {
                    obj = $.parseJSON(obj);
                            if (obj.status === 'success') {
                    location.reload();
                    }
                    }
                    },
                    error: function (a, b, c) {
                    }
            });
    }

    $(document).ready(function () {
    $('#reportrange').daterangepicker({
    ranges: {
    '{{ trans('article.today') }}': [moment(), moment()],
            '{{ trans('article.yesterday') }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '{{ trans('article.last_7_day') }}': [moment().subtract(6, 'days'), moment()],
            '{{ trans('article.last_30_day') }}': [moment().subtract(29, 'days'), moment()],
            '{{ trans('article.this_month') }}': [moment().startOf('month'), moment().endOf('month')],
            '{{ trans('article.last_month') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
            startDate: moment('{{ date('Y - m - d 00:00:00', time()) }}'),
            endDate: moment('{{ date('Y - m - d 23:59:59', time()) }}')
    },
            function (start, end) {
            $('#reportrange span').html(start.format('D MMMM, YYYY') + ' - ' + end.format('D MMMM, YYYY'));
                    $('#start_date').val(start.format('YYYY-MM-DD HH:mm:ss'));
                    $('#end_date').val(end.format('YYYY-MM-DD HH:mm:ss'));
            }
    );
            var timeSelect = moment('{{date('Y - m - d 00:00:00', time())}}').format('D MMMM, YYYY') + ' - ' + moment('{{date('Y - m - d 23:59:59', time())}}').format('D MMMM, YYYY');
            $('#time_select').html(timeSelect);
            $('#time_select2').html(timeSelect);
    });
</script>
@stop