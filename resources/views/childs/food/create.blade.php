@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1>{{trans('food.add')}}</h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{trans('food.add')}}</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"></h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form id="addCategory" role="form" method="post">
                        {{csrf_field()}}
                        <div class="box-body">
                            <div class="form-group">
                                <label for="catTitle">{{trans('category.title')}}</label>
                                <input name="title" type="text" class="form-control" id="catTitle">
                            </div>
                            <div class="form-group">
                                <label for="catSlug">{{trans('category.slug')}}</label>
                                <input name="slug" type="text" class="form-control" id="catSlug">
                            </div>
                            <div class="form-group">
                                <label>{{trans('category.parent')}}</label>
                                <select name="parent_id" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                    <option value="0">{{trans('category.cat_name_none')}}</option>
                                    @foreach($parentCats as $item)
                                        <option value="{{$item['id']}}">{{ $item['title'] }}</option>
                                    @endforeach
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
    </section>
@stop
{{--Script Import --}}
<?php
$replace_path = env("REPLACE_PATH_2");
?>
@section('custom_footer')
<script type="text/javascript">
    $(document).ready(function () {
        $.getScript( "https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js") ;
        $('#addCategory').submit(function (event) {
            event.preventDefault();
            var slug = $("input[name='slug']").val() + ' ';
            var formData = {
                _token : $("input[name='_token']").val(),
                type: 'Review' ,
                title: $("input[name='title']").val() === '' ? swal({
                    title: 'Chưa nhập tiêu đề',
                    type: 'error'
                }) : $("input[name='title']").val(),
                slug: isSlug(slug) ? slug : swal({
                    title: 'Nhập sai định dạng slug',
                    type: 'error'
                }),
                parent_id: $("select[name='parent_id']").val()
            };
            if (!formData.slug || !formData.title) {
                return;
            }
            $.ajax({
                type: "POST",
                url: '/media/food/create',
                dataType: 'json',
                data: formData,
                success: function (res) {
                    event.preventDefault();
                    swal({title: res.msg, type: res.status} , function(isConfirm){
                        if(isConfirm) {
                            location.reload();
                        }
                    });
                }
            }
            );
        });

        function isSlug(slug) {
            value = slug.trim();
            if(value == '') return true;
            var regex = /^([a-zA-Z0-9_.+-])+$/;
            return regex.test(value);
        }
    });
</script>
@stop
{{--End Script--}}