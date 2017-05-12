@extends('layouts.master')

@section('main_content')
<section class="content-header">
    <h1>{{trans('ward.lst_ward')}}</h1>
    <ol class="breadcrumb">
        <li><a href="javascript:void(0)"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">{{trans('ward.lst_ward')}}</li>
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
                                    <th>{{ trans('ward.id') }}</th>
                                    <th>{{ trans('ward.name') }}</th>
                                    <th>{{ trans('ward.district') }}</th>
                                    <th>{{ trans('ward.created_at') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $index = 1;
                                ?>
                                @foreach($wards as $item)
                                <tr class="{{$item['wardid']}}">
                                    <td>{{ $index++ }}</td>
                                    <td>{{$item['name']}}</td>
                                    <td>
                                        <?php
                                        echo isset($item->getDistrict->name) ? $item->getDistrict->name : '';
                                        ?>
                                    </td>
                                    <td>{{date('Y/m/d H:i:s', strtotime($item['created_at']))}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success dropdown-toggle"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                {{trans('category.action')}} <span class="caret"></span> <span
                                                    class="sr-only"></span></button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a onclick="javascript:deleteWard('<?php echo isset($item['wardid']) ? $item['wardid'] : '0'; ?>');" href="javascript:void(0)"
                                                       data-uid="{{$item['wardid']}}">
                                                        <i class="fa fa-remove"></i>{{trans('ward.del_ward')}}
                                                    </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {!! $wards->render()!!}
                </div>
            </div>
        </div>
    </div>
    <!-- Content -->
</section>
<!-- Main content -->
<section class="content-header">
    <h1>{{trans('menu.add_street')}}</h1>
</section>
<section class="content">
    <div class="row">
        <form id="addWard" role="form" method="post">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="catTitle">Tên đường phố</label>
                    <input name="name" type="text" class="form-control" id="in-name">
                </div>
                <div class="form-group">
                    <label>Quận (Huyện / Thị xã)</label>
                    <select class="district form-control" name="district">
                        <option value="" selected="selected">Chọn quận(huyện)</option>
                    </select>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">{{ trans('category.add_cat') }}</button>
            </div>
        </form>
    </div>
    <!-- /.box -->
</div>
<!--/.col (left) -->
</div>
<!-- /.row -->
<style type="text/css">
    #sea_provice p{
        cursor: pointer;
        padding: 5px;
        margin: 0px;
    }

    #sea_provice p:hover{
        background: #dddddd;
        border-radius: 2px;
    }

</style>
</section>
@stop
{{--Script Import --}}
@section('custom_footer')
<script type="text/javascript">
    $(document).ready(function () {
        $.getScript("https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js");
        $('#addWard').submit(function (event) {
            event.preventDefault();

            var formData = {
                _token: $("input[name='_token']").val(),
                name: $("input[name='name']").val() === '' ? swal({
                    title: 'Bạn chưa nhập tên đường',
                    type: 'error'
                }) : $("input[name='name']").val(),
                district: $("select[name='district']").val() === '' ? swal({
                    title: 'Bạn chưa chọn quận(huyện)',
                    type: 'error'
                }) : $("select[name='district']").val()
            };
            if (!formData.name || !formData.district) {
                return;
            }
            $.ajax({
                type: "POST",
                url: '/media/category/street',
                dataType: 'json',
                data: formData,
                success: function (res) {
                    event.preventDefault();
                    swal({title: res.msg, type: res.status}, function (isConfirm) {
                        if (isConfirm) {
                            location.reload();
                        }
                    });
                }
            });
        });
    });

    function deleteWard(id) {
        var id = parseInt(id);
        if (id > 0) {
            $.ajax({
                type: 'POST',
                url: "{{ URL::to('media/category/delete-ward') }}",
                data: 'id=' + id,
                success: function (obj) {
                    if (obj !== null) {
                        obj = $.parseJSON(obj);
                        swal({title: obj.msg, type: obj.status}, function (isConfirm) {
                            if (isConfirm) {
                                location.reload();
                            }
                        });
                    } else {
                        //
                    }
                },
                error: function (a, b, c) {
                }
            });
        }
    }
</script>
@stop
{{--End Script--}}