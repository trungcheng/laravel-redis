@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1> {{ trans('menu.list_user') }}</h1>
        <button type="button" class="btn btn-success" data-toggle="modal"
                data-target="#createUserModal">{{ trans('user.create_user') }}</button>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{ trans('menu.user') }}</a></li>
            <li class="active">{{ trans('user.list_user') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <table id="users-table" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans('user.avatar') }}</th>
                                <th>{{ trans('user.name') }}</th>
                                <th>{{ trans('user.email') }}</th>
                                <th>{{ trans('user.user_type') }}</th>
                                <th>{{ trans('user.status') }}</th>
                                <th>{{ trans('user.facebook') }}</th>
                                <th>{{ trans('user.action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->

    <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="exampleModalLabel">{{ trans('user.create_user') }}</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="formCreateUser" action="/user/create" method="post">
                        <div class="form-group">
                            <label for="inputuser_type"
                                   class="col-sm-2 control-label">{{ trans('user.user_type') }}</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="user_type">
                                    @foreach(config('admincp.user_type') as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName"
                                   class="col-sm-2 control-label">{{ trans('user.name') }}</label>
                            <div class="col-sm-10">
                                <input type="name" class="form-control" name="name"
                                       placeholder="{{ trans('user.name_placeholder') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail"
                                   class="col-sm-2 control-label">{{ trans('user.email') }}</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email"
                                       placeholder="{{ trans('user.email_placeholder') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword"
                                   class="col-sm-2 control-label">{{ trans('user.password') }}</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="password"
                                       placeholder="{{ trans('user.enter_password') }}"
                                       autocomplete="new-password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputDescription"
                                   class="col-sm-2 control-label">{{ trans('user.description') }}</label>
                            <div class="col-sm-10">
                                        <textarea class="form-control" name="description"
                                                  placeholder="{{ trans('user.description_placeholder') }}"></textarea>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit"
                                    class="btn btn-success pull-right">{{ trans('user.create_user') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="exampleModalLabel">{{ trans('user.edit_user') }}</h4>
                </div>
                <div class="modal-body" id="editUserModalBody">

                </div>
            </div>
        </div>
    </div>
@stop

@section('custom_header')
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css') }}">
@stop

@section('custom_footer')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('dist/js/module/user.js') }}"></script>
    <script>
        var UserType = {
            @foreach(config('admincp.user_type') as $key => $item)
            '{{ $key }}': '{{ $item }}',
            @endforeach
        }
		@if(\Request::get('user_fe') != '' )
        $(document).ready(function () {
            UserModule.initUserFeDatatable();
        });
		@else 
		$(document).ready(function () {
            UserModule.initUserDatatable();
        });
		@endif	
    </script>
@stop