@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1 style="margin-bottom: 10px;">{{ trans('menu.list_notif') }}</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{ trans('menu.media_zone') }}</li>
            <li class="active">{{ trans('menu.notification') }}</li>
        </ol>
    </section>
    <section class="content">
        <div>
            <div style="padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #ddd">
                <form method="get" action="/class-register" autocomplete="off" role="form" class="form-inline">
                    <div class="form-group">
                        <input type="text" class="form-control search_top" name="key" id="key"
                               value="{{old('key')}}"
                               autocomplete="off" placeholder="{{ trans('user.search_by_name') }}"
                               style="width: 300px;">
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="user_type" data-live-search="true" data-width="150px">
                            <option value="" @if(!isset($request_all['user_type'])){{"selected"}}@endif >{{ trans('user.user_type') }}</option>
                            <option value="Admin" @if(isset($request_all['user_type']) && $request_all['user_type'] =='Admin' ){{"selected"}}@endif>
                                Admin
                            </option>
                            <option value="Editor" @if(isset($request_all['user_type']) && $request_all['user_type'] =='Editor' ){{"selected"}}@endif>
                                Editor
                            </option>
                            <option value="Normal" @if(isset($request_all['user_type']) && $request_all['user_type'] =='Normal' ){{"selected"}}@endif>
                                Normal
                            </option>
                            <option value="User_Normal" @if(isset($request_all['user_type']) && $request_all['user_type'] =='User_Normal' ){{"selected"}}@endif>
                                User Normal
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="status" data-live-search="true" data-width="150px">
                            <option value="" @if(!isset($request_all['status'])){{"selected"}}@endif >{{ trans('user.status') }}</option>
                            <option value="Active" @if(isset($request_all['status']) && $request_all['status'] =='Active' ){{"selected"}}@endif>
                                Active
                            </option>
                            <option value="Inactive" @if(isset($request_all['status']) && $request_all['status'] =='Inactive' ){{"selected"}}@endif>
                                Inactive
                            </option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger" name="search">Tìm kiếm</button>
                </form>
            </div>
            <div class="post-container">
                <div class="box box-solid">
                    <div class="box-body no-padding">
                        <div>
                            <table class="table table-striped" id="tblSortUser">
                                <thead>
                                <tr>
                                    <th scope="col" id="cb" class="manage-column column-cb check-column">
                                        <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                                        <input id="cb-select-all-1" type="checkbox"
                                               onclick="javascript:selectAllUser(this);">
                                    </th>
                                    <th>{{ trans('user.avatar') }}</th>
                                    <th>{{ trans('user.name') }}</th>
                                    <th>{{ trans('user.email') }}</th>
                                    <th>{{ trans('user.user_type') }}</th>
                                    <th>{{ trans('user.status') }}</th>
                                    <th>{{ trans('user.content') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr id="row-data-{{$user['id']}}" class="post-item post-id-{{$user['id']}}">
                                        <td>
                                            <label class="screen-reader-text"
                                                   for="cb-select-{{$user['id']}}">{{$user['name']}}</label>
                                            <input type="checkbox" class="row-select-id"
                                                   name="select_id[{{$user['id']}}]" value="{{$user['id']}}"
                                                   id="cb-select-{{$user['id']}}">
                                            <input type="hidden" name="row-user[]" class="id_user_item"
                                                   value="{{$user['id']}}">
                                        </td>
                                        <td>
                                            @if(!empty($user->email))
                                                <img class="img-circle" alt="User Image"
                                                     src="http://www.gravatar.com/avatar/35575592399b0a4e352e4fbf4c256130?s=80&d=mm&r=g"/>
                                            @else
                                                <img class="img-circle" alt="User Image"
                                                     src="<?php echo get_gravatar($user->email); ?>"/>
                                            @endif
                                        </td>
                                        <td><p>{{ $user['name'] }}</p></td>
                                        <td><p>{{ $user['email'] }}</p></td>
                                        <td><p>{{ $user['user_type'] }}</p></td>
                                        <td><p>{{ $user['status'] }}</p></td>
                                        <?php
                                        $data = '';
                                        if (!empty($user['meta_value'])) {
                                            if (json_decode($user['meta_value'])) {
                                                $json_array = json_decode((string)$user['meta_value'] , true);
                                                if (!empty($json_array)) {
                                                        foreach ($json_array as $k =>  $json) {
                                                            $data .= '<b>'.$k.'</b>: <span style="color:red" >'.$json.'</span><br>' ;
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                        <td>
                                            <p>@if(!empty($user['meta_value']) && $user['meta_value']!= 'null') {!! $data !!} @endif </p>
                                        </td>
                                        <!--                                    <td><p>
                                            <?php
                                        //                                            $user_id = str_replace('@facebook.vn', '', $user['email']);
                                        //                                            if (str_contains($user['email'], '@facebook.vn') == true) {
                                        //                                                ?>
                                                <a href="https://www.facebook.com/app_scoped_user_id///<?php // echo $user_id; ?>"><?php // echo $user['name']; ?></a>
                                                //<?php
                                        //                                            } else {
                                        //                                                echo '';
                                        //                                            }
                                        ?>
                                                </p></td>-->
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

                    input[type=checkbox] {
                        border: 1px solid #bbb;
                        background: #fff;
                        color: #555;
                        clear: none;
                        cursor: pointer;
                        display: inline-block;
                        line-height: 0;
                        height: 16px;
                        margin: -4px 4px 0 0;
                        outline: 0;
                        padding: 0 !important;
                        text-align: center;
                        vertical-align: middle;
                        width: 16px;
                        min-width: 16px;
                        -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
                        box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
                        -webkit-transition: .05s border-color ease-in-out;
                        transition: .05s border-color ease-in-out;
                    }
                </style>
                {!! $users->appends(\Request::all())->render() !!}
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
                <div class="modal-body" id="reviewArticleModalBody"></div>
            </div>
        </div>
    </div>
@stop

@section('custom_footer')

    <link href="{{ asset('dist/css/jquery.datetimepicker.css') }}" rel="stylesheet">
    <script src="{{ asset('dist/js/jquery.datetimepicker.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#start_date").datetimepicker({
                timepicker: false,
                format: "Y-m-d H:i:s",
            });
            $("#end_date").datetimepicker({
                timepicker: false,
                format: "Y-m-d H:i:s"
            });
        });
        function selectAllUser(tag) {
            if ($(tag).prop("checked")) {
                $('#tblSortUser input[type=checkbox]').each(function () {
                    $(this).prop("checked", true);
                    $(this).attr("checked", "checked");
                });
            } else {
                $('#tblSortUser input[type=checkbox]').each(function () {
                    $(this).prop("checked", false);
                    $(this).removeAttr("checked", "checked");
                });
            }

        }

    </script>
@stop