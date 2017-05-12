@extends('layouts.master')

@section('main_content')
<section class="content-header">
    <h1>{{trans('event.event_add')}}</h1>
    <ol class="breadcrumb">
        <li><a href="javascript:void(0)"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">{{trans('event.event_add')}}</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- left column -->
        <div class="col-md-6" style="width: 60%;">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"></h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form id="addEvent" role="form" method="post">
                    {{csrf_field()}}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="eventName">{{trans('event.name')}}</label>
                            <input name="event_name" type="text" class="form-control" id="event_name">
                        </div>
                        <div class="form-group">
                            <label for="thumbnail">{{trans('event.thumbnail')}}</label>
                            <div class="box-body">
                                <a class="btn btn-block btn-danger fa fa-trash"
                                   style="width:35px;position: relative;top:0px ;float: right;display: none;">
                                </a>
                                <img onclick="BrowseServer('id_of_the_target_input');" src="" id="image_replace"
                                     style="display:none;margin-top:-28px;cursor: pointer;max-height: 200px;width:100%;">
                                <div class="preview-placeholder" id="replace">
                                    <div>
                                        <i class="fa fa-plus fa-2x"
                                           onclick="BrowseServer('id_of_the_target_input');"></i><br>
                                        <h4 class="text-muted">Chọn ảnh sự kiện</h4>
                                    </div>
                                </div>

                            </div><!-- /.box-body -->
                            <input id="id_of_the_target_input" type="hidden" name="thumbnail"/>
                        </div><!-- /.box -->
                        <div class="form-group">
                            <label><i class="fa fa-paragraph"></i>Cách thức tham gia</label>
                            <textarea class="form-control" name="content" id="editor"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="start_date">{{trans('event.start_date')}}</label>
                            <input name="event_started" type="text" class="form-control" id="start_date">
                        </div>
                        <div class="form-group">
                            <label for="end_date">{{trans('event.end_date')}}</label>
                            <input name="event_ended" type="text" class="form-control" id="end_date">
                        </div>
                        <div class="form-group">
                            <label>{{trans('event.type')}}</label>
                            <select name="type" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                <option value="0">0</option>
                                <option value="1">1</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{trans('event.status')}}</label>
                            <select name="status" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                <option value="0">Verify</option>
                                <option value="1">Active</option>
                            </select>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">{{ trans('event.add_cat') }}</button>
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
<link href="{{ asset('dist/css/jquery.datetimepicker.css') }}" rel="stylesheet">
<script src="{{ asset('dist/js/jquery.datetimepicker.js') }}"></script>
<script type="text/javascript">
                                               $(document).ready(function () {
                                                   $.getScript("https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js");
                                                   $("#start_date").datetimepicker({
                                                       timepicker: false,
                                                       format: "Y-m-d H:i:s",
                                                   });
                                                   $("#end_date").datetimepicker({
                                                       timepicker: false,
                                                       format: "Y-m-d H:i:s"
                                                   });
                                                   $('#addEvent').submit(function (event) {
                                                       event.preventDefault();
                                                       var content = tinyMCE.activeEditor.getContent();

                                                       var start_date = $("input[name='event_started']").val();
                                                       var end_date = $("input[name='event_ended']").val();
                                                       if (new Date(start_date) > new Date(end_date))
                                                       {
                                                           alert('Thời gian bắt đầu phải sau thời gian kết thúc');
                                                           return false;
                                                       }

                                                       var formData = {
                                                           _token: $("input[name='_token']").val(),
                                                           name: $("input[name='event_name']").val() === '' ? swal({
                                                               title: 'Chưa nhập tên sự kiện',
                                                               type: 'error'
                                                           }) : $("input[name='event_name']").val(),
                                                           thumbnail: $("input[name='thumbnail']").val() === '' ? swal({
                                                               title: 'Chưa chọn ảnh sự kiện',
                                                               type: 'error'
                                                           }) : $("input[name='thumbnail']").val(),
                                                           rule: content === '' ? swal({
                                                               title: 'Chưa nhập cách thức tham gia',
                                                               type: 'error'
                                                           }) : content,
                                                           start_date: start_date === '' ? swal({
                                                               title: 'Chưa chọn thời gian bắt đầu',
                                                               type: 'error'
                                                           }) : start_date,
                                                           end_date: end_date === '' ? swal({
                                                               title: 'Chưa chọn thời gian kết thúc',
                                                               type: 'error'
                                                           }) : end_date,
                                                           type: $("select[name='type']").val(),
                                                           status: $("select[name='status']").val()
                                                       };
                                                       if (!formData.name || !formData.thumbnail || !formData.start_date || !formData.end_date || !formData.rule) {
                                                           return false;
                                                       }
                                                       $('button[type=submit]').hide();
                                                       $.ajax({
                                                           type: "POST",
                                                           url: '/events/create',
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
                                                       }
                                                       );
                                                   });

                                                   function isSlug(slug) {
                                                       value = slug.trim();
                                                       if (value == '')
                                                           return true;
                                                       var regex = /^([a-zA-Z0-9_.+-])+$/;
                                                       return regex.test(value);
                                                   }
                                               });
                                               initTinyMCE("#editor", "{{url('/')}}");


                                               var urlobj;
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
                                                       $('#image_replace').attr('src', '{{env("MEDIA_PATH")}}' + url.replace('{{env("REPLACE_PATH")}}', ''));
                                                       $('#image_replace').show();
                                                       oWindow = null;
                                                   } else {
                                                       $('#text-folder').html(url);
                                                   }
                                               }
                                               $('.fa-trash').on('click', function () {
                                                   $('#image_replace').hide();
                                                   $('.fa-trash').hide();
                                                   $('#replace').show();
                                                   $('#id_of_the_target_input').attr('value', '');
                                               });
                                               $('#get_url_image').on('click', function () {
                                                   swal({
                                                       title: "Link Image!",
                                                       text: "Write Url Here:",
                                                       type: "input",
                                                       showCancelButton: true,
                                                       closeOnConfirm: false,
                                                       animation: "slide-from-top",
                                                       inputPlaceholder: "Write something"
                                                   },
                                                   function (inputValue) {
                                                       if (inputValue === false)
                                                           return false;
                                                       if (inputValue === "") {
                                                           swal.showInputError("You need to write something!");
                                                           return false
                                                       }
                                                       swal({title: 'Choose Image Success', type: 'success'}, function (isConfirm) {
                                                           if (isConfirm) {
                                                               $('#replace').hide();
                                                               $('.fa-trash').show();
                                                               $('#id_of_the_target_input').attr('value', inputValue);
                                                               $('#image_replace').attr('src', inputValue);
                                                               $('#image_replace').show();

                                                           }
                                                       });
                                                   });
                                               });
</script>
@stop
{{--End Script--}}