@extends('layouts.master')

@section('main_content')
    <ul class="breadcrumb breadcrumbs">
        <li><i class="fa fa-home"></i><a href="/home/">Trang chủ</a></li>
        <li class="active">Bảng điều khiển</li>
    </ul>
    <section class="content-header">
        <h1>{{ trans('menu.dashboard') }}</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> {{ trans('menu.dashboard') }}</a></li>
        </ol>
    </section>
    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-institution"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Nhà Hàng</span>
                        <span class="info-box-number">{{$count_restaurants}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-files-o"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Công Thức</span>
                        <span class="info-box-number">{{$count_recipes}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-pencil"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Bài Viết</span>
                        <span class="info-box-number">{{$count_blogs}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa  fa-user-plus"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Thành Viên đăng ký</span>
                        <span class="info-box-number">{{$count_users}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
            <div class="col-md-4">
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Thành viên mới đăng ký</h3>

                        <div class="box-tools pull-right">
                            <span class="label label-danger">8 New Members</span>
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                        class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding">
                        <ul class="users-list clearfix">
                            @foreach($user_new as $user )
                                <li>
                                    <img src="http://www.gravatar.com/avatar/d3db94166a6ee21987f1e1e69b69c7a5?s=80&d=mm&r=g"
                                         alt="User Image" class="userImage">
                                    <a class="users-list-name" href="#">{{$user->name}}</a>
                                    <span class="users-list-date"></span>
                                </li>
                            @endforeach
                        </ul>
                        <!-- /.users-list -->
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-center">
                        <a href="/user?user_fe=true" class="uppercase">Xem thêm</a>
                    </div>
                    <!-- /.box-footer -->
                </div>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Bài Viết Đang Đặt Lịch</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                        class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <ul class="products-list product-list-in-box">
                            @foreach($articles_schedule as $items )
                                <li class="item">
                                    <div class="product-img">
                                        <img src="{{ env('MEDIA_PATH') . str_replace(env('REPLACE_PATH') , '' , $items->thumbnail )}}" alt="{{$items->title}}" height="50" width="50">
                                    </div>
                                    <div class="product-info">
                                        <a href="javascript:void(0)" class="product-title">{{$items->title}}</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-center">

                    </div>
                    <!-- /.box-footer -->
                </div>
            </div>
            <!-- /.col -->
            <!-- Left col -->
            <div class="col-md-8">
                <!-- MAP & BOX PANE -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Bài Viết Mới</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                        class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table no-margin">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Bài Viết</th>
                                    <th>Người tạo</th>
                                    <th>Ngày Tạo</th>
                                    <th>Trạng thái</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($articles  as $article )
                                    <tr>
                                        <td>{{$article->id}}</td>
                                        <td>{{$article->title}}</td>
                                        <td>{{$article->getUser->name}}</td>
                                        <td>{{$article->created_at}}</td>
                                        <td>{{$article->status}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer clearfix">
                        <a href="/media/article" class="btn btn-sm btn-default btn-flat pull-right">Xem
                            thêm</a>
                    </div>
                    <!-- /.box-footer -->
                </div>
            </div>

            <!-- /.col -->


        </div>
    </section>

    <section class="content">
        <!-- Notifications -->
        <!-- Content -->
    </section>
@stop