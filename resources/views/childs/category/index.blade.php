@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1>{{ trans('menu.cat_title') }}</h1>
        <a href="{{ URL::to('media/category/create') }}" class="btn btn-success">{{ trans('menu.cat_add') }}</a>
		<a href="{{ URL::to('media/category/street') }}" class="btn btn-success">{{ trans('menu.add_street') }}</a>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{ trans('menu.cat_list') }}</li>
        </ol>
    </section>

    <section class="content">
        <!-- Notifications -->
        <div>
            <div class="post-container">
                <div class="box box-solid">
                    <div class="box-body no-padding">
                        <div>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>{{ trans('category.id') }}</th>
                                    <th>{{ trans('category.title') }}</th>
                                    <th>{{ trans('category.slug') }}</th>
                                    <th>{{ trans('category.status') }}</th>
                                    <th>{{ trans('category.created_at') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($categories as $item)
                                    <tr class="{{$item['id']}}">
                                        <td>{{$item['id']}}</td>
                                        <td>{{$item['title']}}</td>
                                        <td>{{$item['slug']}}</td>
                                        <td>
                                            <button onclick="changeStatus(this)" data-uid="{{$item['id']}}"
                                                    class="btn {{($item['status'] == 1) ? 'btn-success' : 'btn-danger'}}">
                                                {{($item['status'] == 1) ? 'Active' : 'Inactive'}}
                                            </button>
                                        </td>
                                        <td>{{date('H:i d/m/Y', strtotime($item['created_at']))}}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-success dropdown-toggle"
                                                        data-toggle="dropdown" aria-expanded="false">
                                                    {{trans('category.action')}} <span class="caret"></span> <span
                                                            class="sr-only"></span></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a onclick="deleteCat(this);" href="javascript:void(0)"
                                                           data-uid="{{$item['id']}}">
                                                            <i class="fa fa-remove"></i>{{trans('category.del_cat')}}
                                                        </a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <?php
                                 $array = \Request::all() ;
                            ?>
                            {!! $categories->appends($array)->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content -->
    </section>
@stop

@section('custom_footer')
    <script src="{{ asset('dist/js/module/category.js') }}"></script>
@stop