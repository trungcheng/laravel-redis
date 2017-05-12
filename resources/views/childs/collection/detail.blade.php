@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1>BỘ SƯU TẬP
            <small> {{ $collection->title }} </small>

        </h1>
        <br>
        @if($collection->status == 0 )
        <a href="/collection/update-status/{{$collection->id}}/2" class="btn btn-success">Hiển Thị</a>
        @else
        <a href="/collection/update-status/{{$collection->id}}/0" class="btn btn-warning">Không Hiển Thị</a>
        @endif
        <a href="/collection/delete/{{$collection->id}}" class="btn btn-danger">Delete</a>
        <a href="{{ url()->previous() }}" class="btn btn-warning"><i class="fa fa-recycle"></i> Go Back</a>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>{{ trans('menu.media_zone') }}</li>
            <li class="active">BỘ SƯU TẬP</li>
        </ol>
    </section>
    <section class="content">
        <div>
            <div class="post-container">
                <div class="box box-solid">
                    <div class="box-body no-padding">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">DANH SÁCH BÀI VIẾT</h3>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <table class="table table-bordered">
                                            <tbody>
                                            <tr>
                                                <th>ID</th>
                                                <th>TIÊU ĐỀ</th>
                                                <th>NGƯỜI TẠO</th>
                                                <th>LOẠI</th>
                                                <th>NGÀY TẠO</th>
                                            </tr>
                                            @foreach($article as $items )
                                                <tr>
                                                    <td>{{$items->id}}.</td>
                                                    <td>
                                                        <?php
                                                        if ($items->type === 'Review') {
                                                            $link = 'article';
                                                        } elseif ($items->type === 'Blogs') {
                                                            $link = 'blogs';
                                                        } elseif ($items->type === 'Recipe') {
                                                            $link = 'recipe';
                                                        }
                                                        ?>
                                                        {{$items->title}}
                                                        <p>
                                                            <a href="/media/{{$link}}/edit/{{$items->id}}">Edit</a>
                                                            &nbsp;&nbsp;
                                                            &nbsp;&nbsp;
                                                            <a href="/collection/delete-article/{{$collection->id}}/{{$items->id}}">Xóa
                                                                Khỏi
                                                                Bộ Sưu Tập</a>
                                                        </p>
                                                    </td>
                                                    <td>{{$items->getUser->name}}</td>
                                                    <td>{{$items->type}}</td>
                                                    <td>{{$items->created_at}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.box -->
                            </div>
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

            </div>
        </div>
    </section>
    <section class="content-header">
        <h1>Thêm Bài Viết Vào Bộ Sưu Tập
            <small> {{ $collection->title }} </small>
        </h1>
    </section>
    <section class="content">
        <div>
            <div style="padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #ddd">
                <form method="get" action="" autocomplete="off" role="form" class="form-inline">
                    <div class="form-group">
                        <input type="text" class="form-control search_top" name="key" id="key"
                               value="{{ old('key') }}"
                               autocomplete="off" placeholder="{{ trans('article.search_by_title') }}"
                               style="width: 150px;">
                    </div>
                    <input type="submit" class="btn btn-danger" name="search" value="{{ trans('home.search') }}">
            </div>
            @if(isset($articles_search) )
                <div class="post-container">
                    <div class="box box-solid">
                        <div class="box-body no-padding">
                            <div>
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{{ trans('article.title') }}</th>
                                        <th>{{ trans('article.author') }}</th>
                                        <th>LOẠI</th>
                                        <th>{{ trans('article.created_at') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($articles_search as $article)
                                        <tr id="" class="post-item post-id-{{$article['id']}}">
                                            <td><p>{{ $article->id }}</p></td>
                                            <td>
                                                <p>{{ $article['title'] }}</p>
                                                <p>
                                                    <a href="/collection/add-article/{{$collection->id}}/{{ $article['id'] }}">Add
                                                        to
                                                        collection</a></p>
                                            </td>
                                            <td><p>{{ $article['creator'] }}</p></td>
                                            <td><p>{{ $article['type'] }}</p></td>
                                            <td><p>{{ $article['created_at'] }}</p></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {!! $articles_search->appends(\Request::all())->render() !!}
                    </form>
                </div>
        </div>
        @endif
    </section>
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