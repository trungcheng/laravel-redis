@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1 style="margin-bottom: 10px;">{{ trans('menu.list_articles') }}</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{ trans('menu.media_zone') }}</li>
            <li class="active">{{ trans('menu.list_articles') }}</li>
        </ol>
    </section>
    <section class="content">
        <div>
            <div style="padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #ddd">
                <form method="get" action="" autocomplete="off" role="form" class="form-inline">
                    <div class="form-group">
                        <select class="form-control select2" name="user_search" data-live-search="true"
                                data-width="220px">
                            <?php
                            $users = \App\Models\User::where(function ($query) {
                                $query->where('user_type', '=', 'Admin');
                                $query->Orwhere('user_type', '=', 'Normal');
                                $query->Orwhere('user_type', '=', 'Editor');
                            })->take(200)->get();
                            ?>
                            <option value="">
                                None
                            </option>
                            @foreach($users as $user)
                                <option value="{{$user->email}}" @if(request()->get('user_search') == $user->email){{"selected"}}@endif >{{$user->name}}</option>
                            @endforeach

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
                    <button type="submit" class="btn btn-danger" name="search">Tìm kiếm</button>

                    <a href="?logout=true">
                        <button type="button" class="btn btn-danger" name="search">Thoát Google Accout</button>
                    </a>
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
                                    <th>{{ trans('article.published_at') }}</th>
                                    <th>View GA</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($data_request as $k =>  $data_child)
                                    <?php
                                    $article = $data_article[$k];
                                    $blog = $article;
                                    $blog_link = null;
                                    $id = isset($blog->id) ? $blog->id : 1;
                                    $title = isset($blog->title) ? $blog->title : '';
                                    if ($blog->type == 'Review') {
                                        $route_name = 'detail-review';
                                    } elseif ($blog->type == 'Recipe') {
                                        $route_name = 'detail-recipe';
                                    } elseif ($blog->type == 'Blogs') {
                                        $route_name = 'detail-blog';
                                    }
                                    $blog_link = route($route_name) . '/' . str_slug($blog->slug) . '_' . $id . '.html';
                                    $blog_link = str_replace(url()->to('/'), 'http://feedy.vn', genLink($blog, $route_name, $blog->slug, $id, $blog_link));
                                    $ga_link = str_replace(url()->to('/'), '', genLink($blog, $route_name, $blog->slug, $id, $blog_link));
                                    $view_ga = isset($data_request[$ga_link]) ? $data_request[$ga_link] : 0;
                                    ?>
                                    <tr id="" class="post-item post-id-{{$article['id']}}">
                                        <td>
                                            <p>
                                                <a href="{{$blog_link}}">{{ $article['title'] }}</a>
                                            </p>
                                        </td>
                                        <td><p>{{ $article['creator'] }}</p></td>
                                        <td><p>{{ $article['published_at'] }}</p></td>
                                        <td><p>{{ $view_ga }}</p></td>
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