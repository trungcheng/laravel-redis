@extends('layouts.master')

@section('main_content')
    <?php
    $replace_path_thumb = str_contains(auth()->user()->thumbnail , 'zdata') ? env('REPLACE_PATH_2') : env('REPLACE_PATH') ;
    ?>
    <section class="content-header">
        <h1>
            {{ trans('user.my_profile') }}
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> {{ trans('user.dashboard') }}</a></li>
            <li class="active">{{ trans('menu.user') }}</li>
            <li class="active">{{ trans('user.my_profile') }}</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        @if(auth()->user()->thumbnail == '' )
                            <img id="image_replace" class="profile-user-img img-responsive img-circle"
                                 src="{{ get_gravatar(auth()->user()->email) }}"
                                 alt="User profile picture" onclick="BrowseServer('id_of_the_target_input');">
                        @else
                            <img id="image_replace" class="profile-user-img img-responsive img-circle"
                                 src="{{ env('MEDIA_PATH') . str_replace($replace_path_thumb , '' , auth()->user()->thumbnail )}}"
                                 alt="User profile picture" onclick="BrowseServer('id_of_the_target_input');">
                        @endif
                        <input id="id_of_the_target_input" type="hidden" name="thumbnail">
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
                    <div class="tab-content" id="change-info">
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
                                    <label for="inputuser_type"
                                           class="col-sm-2 control-label">{{ trans('user.user_name') }}</label>
                                    <div class="col-sm-10">
                                        <input type="name" class="form-control" name="name" id="inputuser_name"
                                               value="{{ auth()->user()->user_name }}" disabled>
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
                                    <label for="inputName"
                                           class="col-sm-2 control-label">{{ trans('user.password') }}</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" name="old_pass" id="txtOldPass"
                                               value=""
                                               placeholder="{{ trans('user.old_pass') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword"
                                           class="col-sm-2 control-label">{{ trans('user.new_pass') }}</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" name="new_pass" id="txtNewPass"
                                               placeholder="{{ trans('user.new_pass') }}"
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
                                        <button type="submit" class="btn btn-danger">C?p nh?t</button>
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
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

            $('#change-info').show();
            $('#change-pass').hide();

            $('#btnChangePass').click(function () {
                $('#change-info').hide();
                $('#change-pass').show();
            });

        });
        function BrowseServer(obj) {
            urlobj = obj;
            OpenServerBrowser(
                "{{url('/')}}" + '/filemanager/index.html',
                screen.width * 0.7,
                screen.height * 0.7);
        }
        function OpenServerBrowser(url, width, height) {
            var iLeft = (screen.width - width) / 2;
            var iTop = (screen.height - height) / 2;
            var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes";
            sOptions += ",width=" + width;
            sOptions += ",height=" + height;
            sOptions += ",left=" + iLeft;
            sOptions += ",top=" + iTop;
            var oWindow = window.open(url, "BrowseWindow", sOptions);
        }

        function SetUrl(url, width, height, alt) {
            document.getElementById(urlobj).value = url;
            if (urlobj == 'id_of_the_target_input') {
                $('#replace').hide();
                $('.fa-trash').show();
                $('#image_replace').attr('src', '{{env("MEDIA_PATH")}}' + url.replace('{{env("REPLACE_PATH_2")}}', ''));
                $('#image_replace').show();
                oWindow = null;
            } else {
                $('#text-folder').html(url);
            }
        }
    </script>
@stop

