@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1 style="margin-bottom: 10px;">{{ trans('menu.list_articles') }}</h1>
        <a href="{{ URL::to('media/article/create') }}" class="btn btn-success">Tạo bài giới thiệu nhà hàng</a>
        <a href="{{ URL::to('media/recipe/create') }}" class="btn btn-success">Tạo công thức nấu ăn </a>
        <a href="{{ URL::to('media/blogs/create') }}" class="btn btn-success">Tạo bài viết </a>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{ trans('menu.media_zone') }}</li>
            <li class="active">{{ trans('menu.list_articles') }}</li>
        </ol>
    </section>
    <section class="content">
        <div>
            <div style="padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #ddd">
                <form method="get" action="/media/article" autocomplete="off" role="form" class="form-inline">
                    <div class="form-group">
                        <input type="text" class="form-control search_top" name="key" id="key"
                               value="{{old('key')}}"
                               autocomplete="off" placeholder="{{ trans('article.search_by_title') }}"
                               style="width: 150px;">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control search_top" name="user_search" id="key"
                               value="{{old('user_search')}}"
                               autocomplete="off" placeholder="Tìm Theo Email"
                               style="width: 150px;">
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="category" data-live-search="true" data-width="120px">
                            <option value="" @if(!isset($request_all['category'])){{"selected"}}@endif >{{ trans('article.category') }}</option>
                            @foreach( $category as $item )
                                <option value="{{ $item->id }}" @if(isset($request_all['category']) && (int)$request_all['category'] ==$item->id ){{"selected"}}@endif>{{ $item->title }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="type" data-live-search="true" style="width:140px;">
                            <option value="0"> Tất cả</option>
                            <option @if(isset($request_all['type']) && $request_all['type'] == "Review"){{"selected"}}@endif value="Review">
                                Nhà hàng
                            </option>
                            <option @if(isset($request_all['type']) && $request_all['type'] == "Recipe"){{"selected"}}@endif value="Recipe">
                                Công thức
                            </option>
                            <option @if(isset($request_all['type']) && $request_all['type'] == "Blogs"){{"selected"}}@endif value="Blogs">
                                Bài viết
                            </option>
                            <option @if(isset($request_all['type']) && $request_all['type'] == "RecipeOfMem"){{"selected"}}@endif value="RecipeOfMem">
                                Bài viết ngoài
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <div id="reportrange" class="btn btn-default "
                             style="position: relative; display: inline-block">
                            <i class="glyphicon glyphicon-calendar"></i>
                            @if(isset($request_all['start_date']) && isset($request_all['end_date']))
                                <span id="time_select">{{date($request_all['start_date'])}}
                                    - {{date($request_all['end_date']) }}</span>
                            @else
                                <span id="time_select">{{date('Y-m-01 00:00:00',time())}}
                                    - {{date('Y-m-t 23:59:59',time()) }}</span>
                            @endif
                            <b class="caret"></b>
                            <input type="hidden" name="start_date" id="start_date"
                                   value="<?php echo isset($request_all['start_date']) ? $request_all['start_date'] : '' ?>">
                            <input type="hidden" name="end_date" id="end_date"
                                   value="<?php echo isset($request_all['end_date']) ? $request_all['end_date'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="status" data-live-search="true" style="width:140px;">
                            <option @if(!isset($request_all['status'])){{"selected"}}@endif value="">{{ trans('article.status') }}</option>
                            <option @if(isset($request_all['status']) && $request_all['status'] == "publish"){{"selected"}}@endif value="publish">{{ trans('article.published') }}</option>
                            <option @if(isset($request_all['status']) && $request_all['status'] == "scheduled"){{"selected"}}@endif value="scheduled">{{ trans('article.scheduled') }}</option>
                            <option @if(isset($request_all['status']) && $request_all['status'] == "draft"){{"selected"}}@endif value="draft">{{ trans('article.draft') }}</option>
                            <option @if(isset($request_all['status']) && $request_all['status'] == "pending"){{"selected"}}@endif value="pending">{{ trans('article.pending') }}</option>
                            <option @if(isset($request_all['status']) && $request_all['status'] == "trash"){{"selected"}}@endif value="trash">
                                Trash
                            </option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-danger" name="search">Tìm kiếm</button>
            </div>
            <div class="post-container">
                <div class="box box-solid">
                    <div class="box-body no-padding">
                        <div>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>{{ trans('article.title') }}</th>
                                    <th>{{ trans('article.author') }}</th>
                                    <th>{{ trans('article.category') }}</th>
                                    <th>{{ trans('article.type') }}</th>
                                    <th>{{ trans('article.tag') }}</th>
                                    <th>{{ trans('article.status') }}</th>
                                    <th>{{ trans('article.created_at') }}</th>
                                    <th>{{ trans('article.published_at') }}</th>
                                    <th>{{ trans('article.approved_by') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($articles as $article)
                                    <tr id="" class="post-item post-id-{{$article['id']}}">
                                        <td>
                                            <p><a href="http://feedy.vn/bai-viet/redirect_{{$article->id}}.html">{{ $article['title'] }}</a></p>
                                            <p>
                                                <?php
                                                if ($article->type == 'Review') {
                                                    $path = 'article';
                                                    $type = 1;

                                                } elseif ($article->type == 'Recipe') {
                                                    $path = 'recipe';
                                                    $type = 2;

                                                } elseif ($article->type == 'Blogs') {
                                                    $path = 'blogs';
                                                    $type = 3;
                                                }
                                                ?>
                                                <a href="/media/{{$path}}/edit/{{$article->id}}">Edit</a> &nbsp;&nbsp;
                                                <span onclick="javacript:verifyArt('{{$article->id}}',{{$type}}, 0);">Verify</span>
                                                &nbsp;&nbsp;
                                                <span onclick="javacript:trashArt('{{$article->id}}', {{$type}}, 0);">Move to trash</span>
                                                &nbsp;&nbsp;
                                                <span onclick="javacript:publishArt('{{$article->id}}', {{$type}});">Publish</span>
                                                &nbsp;&nbsp;
                                                <span onclick="javacript:draftArt('{{$article->id}}', {{$type}});">Draft</span>
                                            </p>
                                        </td>
                                        <td><p>{{ $article['creator'] }}</p></td>
                                        <td><p>
                                                @foreach($article->articleCategory as $value)
                                                {{ $value->title }}
                                                        <!--{{ @str_slug($value->title) }}-->
                                                @endforeach
                                            </p></td>
                                        <td><p>{{ $article['type'] }}</p></td>
                                        <td><p>{{ $article['tag'] }}</p></td>
                                        <td><p>{{ $article['status'] }}</p></td>
                                        <td><p>{{ $article['created_at'] }}</p></td>
                                        <td><p>{{ $article['published_at'] }}</p></td>
                                        <td><p>{{ $article['approve_by'] }}</p></td>
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
                </style>
                {!! $articles->appends(\Request::all())->render() !!}
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
        function deleteArt(id, check) {
            if (check == 1) {
                link = "{{URL::to('/media/article/delete/')}}";
            } else if (check == 2) {
                link = "{{URL::to('/media/recipe/delete/')}}";
            } else if (check == 3) {
                link = "{{URL::to('/media/blogs/delete/')}}";
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
            if (check == 1) {
                link = "{{URL::to('/media/article/publish/')}}";
            } else if (check == 2) {
                link = "{{URL::to('/media/recipe/publish/')}}";
            } else if (check == 3) {
                link = "{{URL::to('/media/blogs/publish/')}}";
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
            if (check == 1) {
                if (status === 1) {
                    link = "{{URL::to('/media/article/untrash/')}}";
                } else {
                    link = "{{URL::to('/media/article/trash/')}}";
                }
            } else if (check == 2) {
                if (status === 1) {
                    link = "{{URL::to('/media/recipe/untrash/')}}";
                } else {
                    link = "{{URL::to('/media/recipe/trash/')}}";
                }
            } else if (check == 3) {
                if (status === 1) {
                    link = "{{URL::to('/media/blogs/untrash/')}}";
                } else {
                    link = "{{URL::to('/media/blogs/trash/')}}";
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
            } else if (check === 2) {
                link = "{{URL::to('/media/recipe/draft/')}}";
            } else if (check === 3) {
                link = "{{URL::to('/media/blogs/draft/')}}";
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
            if (check == 1) {
                if (status === 1) {
                    link = "{{URL::to('/media/article/unverify/')}}";
                } else {
                    link = "{{URL::to('/media/article/verify/')}}";
                }
            } else if (check == 2) {
                if (status === 1) {
                    link = "{{URL::to('/media/recipe/unverify/')}}";
                } else {
                    link = "{{URL::to('/media/recipe/verify/')}}";
                }
            } else if (check == 3) {
                if (status === 1) {
                    link = "{{URL::to('/media/blogs/unverify/')}}";
                } else {
                    link = "{{URL::to('/media/blogs/verify/')}}";
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