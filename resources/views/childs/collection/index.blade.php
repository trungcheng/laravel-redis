@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1>{{ trans('collection.list_collection') }}</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{ trans('menu.media_zone') }}</li>
            <li class="active">{{ trans('collection.list_collection') }}</li>
        </ol>
    </section>
    <section class="content">
        <div>
            <div style="padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #ddd">
                <form method="get" action="/media/collection" autocomplete="off" role="form" class="form-inline">
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
                    <input type="submit" class="btn btn-danger" name="search" value="{{ trans('home.search') }}">
            </div>
            <div class="post-container">
                <div class="box box-solid">
                    <div class="box-body no-padding">
                        <div>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>{{ trans('collection.title') }}</th>
                                    <th>{{ trans('collection.creator') }}</th>
                                    <th>{{ trans('collection.description') }}</th>
                                    <?php
                                    \Request::get('order_by_type') == 'desc' ? $order_by_type = 'asc' : $order_by_type = 'desc';
                                    ?>
                                    <th>
                                        <a href="?order_by_type={{$order_by_type}}&page={{\Request::get('page')}}">{{ trans('collection.type') }}</a>
                                    </th>
                                    <th>Trạng Thái</th>
                                    <th>Số Bài Viết</th>
                                    <th>Cài Đặt</th>
                                    <th>{{ trans('comment.update') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($collections as $collection)
                                    <?php
                                    if (!isset ($collection->id)) {
                                        $collection->id = 1;
                                    }
                                    ?>
                                    <tr id="" class="post-item post-id-{{$collection['id']}}">
                                        <td>
                                            <p><input name="txt_input_title" id="txtTitle-{{ $collection['id'] }}"
                                                      value="{{ $collection['title'] }}"/></p>
                                        </td>

                                        <td><p>{{ $collection['creator'] }}</p></td>
                                        <td>
                                            <textarea name="txt_input_content" id="txtDesc-{{ $collection['id'] }}"
                                                      rows="4" cols="30">{{ $collection->description }}</textarea>
                                        <td><p>{{ $collection['type'] }}</p></td>
                                        <?php
                                        $collection['status'] == 2 ? $status = 'Hiển Thị' : $status = 'Chưa Hiển Thị';
                                        $article_count = $collection->getTotalCollectionArticleAttribute() ;
                                        ?>

                                        <td><p>{{ $status}}</p></td>
                                        <td>
                                            <center>{{ $article_count }}</center>
                                        </td>
                                        <td>
                                            <center><a href="/collection/detail/{{$collection->id}}"><span
                                                            class="glyphicon glyphicon-eye-open"></span></a></center>
                                        </td>

                                        <td>
                                            <input class="btn-update" value="Cập nhật" id="btnUpdate"
                                                   onclick="javacript:updateColl('{{ $collection['id'] }}');"/>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <style>
                    table tr td p span {
                        cursor: pointer;
                        color: #3c8dbc;
                    }

                    table tr td p span:hover {
                        color: #72afd2;
                    }

                    table tr td input, table tr td textarea {
                        border: 0px;
                        width: 100%;
                    }

                    table tr td input.btn-update {
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
                {!! $collections->appends(\Request::all())->render() !!}
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
                    <button id="btn-status" style="width:130px;margin-left: 60px;" status="off" id="summitToPublish"
                            class="btn btn-success" type="button">
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
        function deleteColl(id) {
            link = "{{URL::to('/media/collection/delete/')}}";
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

        function updateColl(id) {
            link = "{{URL::to('/media/collection/update/')}}";
            var title = $('#txtTitle-' + id).val();
            var description = $('#txtDesc-' + id).val();
            $.ajax({
                type: 'POST',
                url: link,
                data: 'id=' + id + '&title=' + title + '&description=' + description,
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