@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1>{{trans('menu.cat_add')}}</h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{trans('menu.cat_add')}}</li>
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
                                <label>Type</label>
                                <select name="type" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">{{trans('category.cat_name_none')}}</option>
                                    @foreach(config('admincp.type_category') as $k => $item)
                                        <option value="{{$k}}">{{ $item[0]}}</option>
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
@section('custom_footer')
    <script type="text/javascript">
        $(document).ready(function () {
            $.getScript( "https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js") ;
            $('#addCategory').submit(function (event) {
                event.preventDefault();
                var slug = $("input[name='slug']").val() + ' ';
                var formData = {
                    _token : $("input[name='_token']").val(),
                    type: $("select[name='type']").val() === '' ? swal({
                        title: 'Chưa nhập Loại Category',
                        type: 'error'
                    }) : $("select[name='type']").val(),
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
                if (!formData.type || !formData.title) {
                    return;
                }
                $.ajax({
                            type: "POST",
                            url: '/media/category/create',
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