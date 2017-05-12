@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1>{{ trans('product.list') }}</h1>
        <a href="{{ URL::to('media/product/create') }}" class="btn btn-success">{{ trans('product.add') }}</a>
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{ trans('product.list') }}</li>
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
                                    <th>{{ trans('product.id') }}</th>
                                    <th>{{ trans('product.name') }}</th>
                                    <th>{{ trans('product.desc') }}</th>
                                    <th>{{ trans('product.price') }}</th>
                                    <th>{{ trans('product.point') }}</th>
                                    <th>{{ trans('product.image') }}</th>
                                    <th>{{ trans('product.creator') }}</th>
                                    <th>{{ trans('product.created_at') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i=0; ?>
                                @foreach($products as $item)
                                <?php $i++; ?>
                                    <tr class="{{$item['id']}}">
                                        <td>{{$i}}</td>
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ $item['description'] }}</td>
                                        <td>{{ $item['price'] }} coins</td>
                                        <td>{{ $item['point'] }}</td>
                                        <td><img src="{{ str_replace(env('REPLACE_PATH_2'), env('MEDIA_PATH'), $item['thumbnail']) }}" width="70" height="60" /></td>
                                        <td>{{ $item['creator'] }}</td>
                                        <td>{{ date('H:i d/m/Y', strtotime($item['created_at'])) }}</td>
                                        <td><div class="btn-group">
                                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    {{trans('product.action')}} <span class="caret"></span> <span class="sr-only"></span></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="/media/product/edit/{{$item['id']}}"><i class="fa fa-edit"></i>{{trans('product.edit_pro')}}</a></li>
                                                    <li><a onclick="deleteProduct(this);" href="javascript:void(0)" data-uid="{{$item['id']}}">
                                                            <i class="fa fa-remove"></i>{{trans('product.del_pro')}}
                                                        </a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content -->
    </section>
@stop

@section('custom_footer')
    <script src="{{ asset('dist/js/module/product.js') }}"></script>
@stop