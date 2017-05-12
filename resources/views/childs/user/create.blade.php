@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1>
            {{ trans('user.create_user') }}
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> {{ trans('user.dashboard') }}</a></li>
            <li class="active">{{ trans('user.create_user') }}</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <img class="profile-user-img img-responsive img-circle"
                             src="{{ get_gravatar(auth()->user()->email) }}"
                             alt="User profile picture">
                        <h3 class="profile-username text-center">{{ auth()->user()->name }}</h3>
                        <strong><i class="fa fa-file-text-o margin-r-5"></i> Notes</strong>
                        <p class="profile-description">{!! auth()->user()->description !== '' ? auth()->user()->description : '' !!}</p>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#settings" data-toggle="tab">Settings</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="settings">
                            <form id="my_profile" class="form-horizontal" action="{{ url('/user/profile') }}"
                                  method="post">
                                <div class="form-group">
                                    <label for="inputuser_type"
                                           class="col-sm-2 control-label">{{ trans('user.user_type') }}</label>
                                    <div class="col-sm-10">
                                        <input type="name" class="form-control" name="name" id="inputuser_type"
                                               value="{{ auth()->user()->user_type }}" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputName"
                                           class="col-sm-2 control-label">{{ trans('user.name') }}</label>
                                    <div class="col-sm-10">
                                        <input type="name" class="form-control" name="name" id="inputName"
                                               value="{{ auth()->user()->name }}"
                                               placeholder="{{ trans('user.name_placeholder') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail"
                                           class="col-sm-2 control-label">{{ trans('user.email') }}</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" name="email" id="inputEmail"
                                               value="{{ auth()->user()->email }}"
                                               placeholder="{{ trans('user.email_placeholder') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword"
                                           class="col-sm-2 control-label">{{ trans('user.password') }}</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" name="password" id="inputPassword"
                                               placeholder="{{ trans('user.password_placeholder') }}"
                                               autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputDescription"
                                           class="col-sm-2 control-label">{{ trans('user.description') }}</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="inputDescription"
                                                  placeholder="{{ trans('user.description_placeholder') }}">{{ auth()->user()->description }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-danger">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div><!-- /.tab-pane -->
                    </div><!-- /.tab-content -->
                </div><!-- /.nav-tabs-custom -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section>
@stop

@section('custom_footer')
    <script src="{{ asset('dist/js/module/user.js') }}"></script>
    <script>
        $(document).ready(function () {
            UserModule.initForm();
        });
    </script>
@stop