@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1>DANH SÁCH BÌNH LUẬN</h1>
        {{--<br>--}}
        {{--<a href="{{ URL::to('questions/create') }}" class="btn btn-success">Tạo Câu Hỏi</a>--}}
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class=>Questions</li>
        </ol>
    </section>
    <section class="content">
        <div>
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
                                    <th>TITLE</th>
                                    <th>TYPE</th>
                                    <th><a href="?order_type={{$order_type}}">Comment | Reply @if($order_type == 'asc')
                                                <i
                                                        class="fa fa-angle-up"></i> @else <i
                                                        class="fa fa-angle-down"></i> @endif  </a></th>
                                    <th>NỘI DUNG</th>
                                    <th>NGƯỜI TẠO</th>
                                    <th><a href="?order_created={{$order_created}}">NGÀY
                                            TẠO @if($order_created == 'asc')<i
                                                    class="fa fa-angle-up"></i> @else <i
                                                    class="fa fa-angle-down"></i> @endif</a></th>
                                    <th>TÁC VỤ</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($questions as $item )
                                    <?php
                                    $type = '';
                                    $status = '';
                                    $item->parent_id == 0 ? $type = 'Comment' : $type = 'Re-Comment';
                                    $item->status === 1 ? $status = 'Ẩn Comment' : $status = 'Hiển Thị Comment';
                                    $item->status === 1 ? $status_index = 0 : $status_index = 1;
                                    ?>
                                    <tr id="" class="post-item">
                                        <td>
                                            <p>{{$item->id}}</p>
                                        </td>
                                        <td width="14%">
                                            <form role="form" method="post"
                                                  action="/questions/edit">
                                                <input type="name" class="form-control" name="title"
                                                       value="{{$item->title}}"
                                                       placeholder="Nhập Title" required>
                                                <input type="hidden" name="id" value="{{$item->id}}">
                                                <br>
                                                <button type="submit"> Thay đổi tiêu đề</button>
                                            </form>
                                        </td>
                                        <td><p>{{$item->type}}</p></td>
                                        <td><p>{{$type}}</p></td>
                                        <td width="30%">
                                            <style>
                                                .content_edit img {
                                                    width: 100%;
                                                    height: auto;
                                                    margin-top: 10px;
                                                }
                                            </style>
                                            <div class="content_edit" id="content_edit_{{$item->id}}"
                                                 contentEditable="true"
                                                 style="height: 150px; overflow: scroll">
                                                {{$item->content}}
                                            </div>

                                            <form role="form" method="post" id="content_form_{{$item->id}}"
                                                  action="/questions/edit"><input id="input_content_{{$item->id}}"
                                                                                  type="hidden" name="content"
                                                                                  value="{{$item->content}}">
                                                <input type="hidden" name="id" value="{{$item->id}}">
                                                <br>

                                            </form>
                                            <button id="submit_content_{{$item->id}}"> Thay đổi nội dung</button>
                                        </td>
                                        <td><p>{{$item->creator}}</p></td>
                                        <td><p>{{$item->created_at}}</p></td>
                                        <td width="20%">
                                            <p>
                                                <a href="/questions/status/{{$status_index}}/{{$item->id}}">{{$status}}</a>
                                                &nbsp;&nbsp;
                                                <a href="/questions/delete/{{$item->id}}">Xóa</a>
                                            </p>
                                            @if($item->parent_id == 0 )
                                                <p>
                                                <form role="form" method="post"
                                                      action="/questions/reply">
                                                    <input type="name" class="form-control" name="content" value=""
                                                           placeholder="Nhập nội dung trả lời">
                                                    <input type="hidden" name="parent_id" value="{{$item->id}}"><br>
                                                    <input type="hidden" name="article_id"
                                                           value="{{$item->article_id}}">
                                                    <button type="submit"> Trả lời</button>
                                                </form>
                                                </p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $questions->appends(\Request::all())->render() !!}
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
    @foreach($questions as $item)
        <script>
            $('#submit_content_{{$item->id}}').on('click', function () {
                $content = $('#content_edit_{{$item->id}}').html();
                $('#input_content_{{$item->id}}').attr('value', $content);
                $('#content_form_{{$item->id}}').submit();
            });
        </script>
    @endforeach
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('dist/js/module/article.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/iCheck/icheck.js') }}"></script>
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
    <script>

        function deleteArt(id, check) {
            if (check === 1) {
                link = "{{URL::to('/media/article/delete/')}}";
            } else {
                link = "{{URL::to('/media/recipe/delete/')}}";
            }
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

        function publishArt(id, check) {
            if (check === 1) {
                link = "{{URL::to('/media/article/publish/')}}";
            } else {
                link = "{{URL::to('/media/recipe/publish/')}}";
            }
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

        function trashArt(id, check, status) {
            if (check === 1) {
                if (status === 1) {
                    link = "{{URL::to('/media/article/untrash/')}}";
                } else {
                    link = "{{URL::to('/media/article/trash/')}}";
                }
            } else {
                if (status === 1) {
                    link = "{{URL::to('/media/recipe/untrash/')}}";
                } else {
                    link = "{{URL::to('/media/recipe/trash/')}}";
                }
            }
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

        function draftArt(id, check) {
            if (check === 1) {
                link = "{{URL::to('/media/article/draft/')}}";
            } else {
                link = "{{URL::to('/media/recipe/draft/')}}";
            }
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

        function verifyArt(id, check, status) {
            if (check === 1) {
                if (status === 1) {
                    link = "{{URL::to('/media/article/unverify/')}}";
                } else {
                    link = "{{URL::to('/media/article/verify/')}}";
                }
            } else {
                if (status === 1) {
                    link = "{{URL::to('/media/recipe/unverify/')}}";
                } else {
                    link = "{{URL::to('/media/recipe/verify/')}}";
                }
            }
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