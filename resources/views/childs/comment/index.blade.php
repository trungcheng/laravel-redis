@extends('layouts.master')

@section('main_content')
<section class="content-header">
    <h1>{{ trans('comment.list_comment') }}</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">{{ trans('menu.media_zone') }}</li>
        <li class="active">{{ trans('comment.list_comment') }}</li>
    </ol>
</section>
<section class="content">
    <div>
        <div style="padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #ddd">
            <form method="get" action="/media/comment" autocomplete="off" role="form" class="form-inline">
                <div class="form-group">
                    <input type="text" class="form-control search_top" name="key" id="key"
                           value="{{ old('key') }}"
                           autocomplete="off" placeholder="{{ trans('article.search_by_title') }}"
                           style="width: 150px;">
                </div>
                <div class="form-group">
                    <div id="reportrange" class="btn btn-default "
                         style="position: relative; display: inline-block">
                        <i class="glyphicon glyphicon-calendar"></i>
                        @if(old('start_date') != null && old('end_date') != null)
                        <span id="time_select">{{date(old('start_date'))}}
                            - {{date(old('end_date')) }}</span>
                        @else
                        <span id="time_select">{{date('Y-m-01 00:00:00',time())}}
                            - {{date('Y-m-t 23:59:59',time()) }}</span>
                        @endif
                        <b class="caret"></b>
                        <input type="hidden" name="start_date" id="start_date" value="{{ old('start_date') }}">
                        <input type="hidden" name="end_date" id="end_date" value="{{ old('end_date') }}">
                    </div>
                </div>
                <div class="form-group">
                    <select class="form-control" name="status" data-live-search="true" style="width:140px;">
                        <option @if(!isset($request_all['status'])){{"selected"}}@endif value="">{{ trans('comment.status') }}</option>
                        <option @if(isset($request_all['status']) && $request_all['status'] === "1"){{"selected"}}@endif value="1">{{ trans('comment.show') }}</option>
                        <option @if(isset($request_all['status']) && $request_all['status'] === "0"){{"selected"}}@endif value="0">{{ trans('comment.hide') }}</option>
                    </select>
                </div>
                <input type="submit" class="btn btn-danger" name="search" value="{{ trans('home.search') }}">
                </div>
                <div class="post-container">
                    <div class="box box-solid">
                        <div class="box-body no-padding">
                            <div>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('comment.title') }}</th>
                                            <th>{{ trans('comment.creator') }}</th>
                                            <th>{{ trans('comment.content') }}</th>
                                            <th>{{ trans('comment.article') }}</th>
                                            <th>{{ trans('comment.ratting') }}</th>
                                            <th>{{ trans('comment.created_at') }}</th>
                                            <th>{{ trans('comment.status') }}</th>
                                            <th>{{ trans('comment.update') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($comments as $comment)
                                        <tr id="" class="post-item post-id-{{$comment['id']}}" >
                                            <td>
                                                <p><input name="txt_input_title" id="txtTitle-{{ $comment['id'] }}" value="{{ $comment['title'] }}" /></p>
                                                <p>
                                                    <?php
                                                    if ((int) $comment['status'] === 0) {
                                                        ?>
                                                        <span onclick="javacript:showHideCom('{{ $comment['id'] }}', 1);" >{{ trans('comment.show') }}</span> &nbsp;&nbsp;
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <span onclick="javacript:showHideCom('{{ $comment['id'] }}', 0);" >{{ trans('comment.hide') }}</span> &nbsp;&nbsp;
                                                        <?php
                                                    }
                                                    ?>
                                                    <span onclick="javacript:deleteCom('{{ $comment['id'] }}');" >{{ trans('comment.delete') }}</span> &nbsp;&nbsp;
                                                </p>
                                            </td>
                                            <td><p>{{ $comment['creator'] }}</p></td>
                                            <td>
                                                <textarea name="txt_input_content" id="txtContent-{{ $comment['id'] }}">{{ $comment['content'] }}</textarea>
                                            <td><p>
                                                    <?php
                                                    echo isset($comment->articleComment->title) ? str_limit($comment->articleComment->title, $limit = 50, $end = '...') : '';
                                                    ?>
                                                </p></td>
                                            <td><p>{{ $comment['ratings'] }}</p></td>
                                            <td><p>{{ $comment['created_at'] }}</p></td>
                                            <td><p>
                                                    <?php
                                                    if ((int) $comment['status'] === 0) {
                                                        echo 'Hide';
                                                    } else {
                                                        echo 'Show';
                                                    }
                                                    ?>
                                            </td>
                                            <td>
                                                <input class="btn-update" value="Cập nhật" id="btnUpdate" onclick="javacript:update('{{ $comment['id'] }}');" />
                                            </td>

                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <style>
                        table tr td p span{
                            cursor: pointer;
                            color: #3c8dbc;
                        }
                        table tr td p span:hover{
                            color:#72afd2;
                        }
                        table tr td input,table tr td textarea{
                            border: 0px;
                            width: 100%;
                        }
                        table tr td input.btn-update{
                            background: #E68447;
                            border: 0px;
                            border-radius: 1px;
                            color: #fff;
                            padding: 5px 10px;
                            font-weight: bold;
                            width: 80px;
                            cursor: pointer;
                        }
                    </style>
                    {!! $comments->render() !!}
            </form>
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
                <button id="btn-status" style="width:130px;margin-left: 60px;" status="off"  id="summitToPublish" class="btn btn-success" type="button">
                    {{ trans('article.publish') }}
                </button>
            </div>
            <div class="modal-body" id="reviewArticleModalBody">

            </div>
        </div>
    </div>
</div>
@stop

@section('custom_header')
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css') }}">
<link href="{{ asset('plugins/iCheck/minimal/blue.css') }}" rel="stylesheet">
@stop

@section('custom_footer')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('dist/js/module/article.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/iCheck/icheck.js') }}"></script>
<script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
<script>
                    function deleteCom(id){
                    link = "{{URL::to('/media/comment/delete/')}}";
                            $.ajax({
                            type: 'POST',
                                    url: link,
                                    data: 'id=' + id,
                                    success: function (obj) {
                                    if (obj !== null) {
                                    obj = $.parseJSON(obj);
                                            if (obj.status === 'success'){
                                    location.reload();
                                    }
                                    }
                                    },
                                    error: function (a, b, c) {
                                    }
                            });
                    }

            function showHideCom(id, check){
            if (check === 1){
            link = "{{URL::to('/media/comment/show/')}}";
            } else{
            link = "{{URL::to('/media/comment/hide/')}}";
            }
            $.ajax({
            type: 'POST',
                    url: link,
                    data: 'id=' + id,
                    success: function (obj) {
                    if (obj !== null) {
                    obj = $.parseJSON(obj);
                            if (obj.status === 'success'){
                    location.reload();
                    }
                    }
                    },
                    error: function (a, b, c) {
                    }
            });
            }

            function update(id){
            link = "{{URL::to('/media/comment/update/')}}";
                    var title = $('#txtTitle-' + id).val();
                    var content = $('#txtContent-' + id).val();
                    $.ajax({
                    type: 'POST',
                            url: link,
                            data: 'id=' + id + '&title=' + title + '&content=' + content,
                            success: function (obj) {
                            if (obj !== null) {
                            obj = $.parseJSON(obj);
                                    if (obj.status === 'success'){
                            location.reload();
                            }
                            }
                            },
                            error: function (a, b, c) {
                            }
                    });
            }

            $(document).ready(function () {
            $('#reportrange').daterangepicker(
            {
            ranges: {
            '{{ trans('article.today') }}': [moment(), moment()],
                    '{{ trans('article.yesterday') }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '{{ trans('article.last_7_day') }}': [moment().subtract(6, 'days'), moment()],
                    '{{ trans('article.last_30_day') }}': [moment().subtract(29, 'days'), moment()],
                    '{{ trans('article.this_month') }}': [moment().startOf('month'), moment().endOf('month')],
                    '{{ trans('article.last_month') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
                    startDate: moment('{{ date('Y-m-d 00:00:00', time()) }}'),
                    endDate: moment('{{ date('Y-m-d 23:59:59', time()) }}')
            },
                    function (start, end) {
                    $('#reportrange span').html(start.format('D MMMM, YYYY') + ' - ' + end.format('D MMMM, YYYY'));
                            $('#start_date').val(start.format('YYYY-MM-DD HH:mm:ss'));
                            $('#end_date').val(end.format('YYYY-MM-DD HH:mm:ss'));
                    }
            );
                    var timeSelect = moment('{{date('Y-m-d 00:00:00', time())}}').format('D MMMM, YYYY') + ' - ' + moment('{{date('Y-m-d 23:59:59', time())}}').format('D MMMM, YYYY');
                    $('#time_select').html(timeSelect);
                    $('#time_select2').html(timeSelect);
            });
</script>
@stop