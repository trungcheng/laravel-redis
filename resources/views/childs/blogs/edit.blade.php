@extends('layouts.master')

@section('main_content')
    <?php
    $replace_path_thumb = str_contains($article->thumbnail, 'zdata') ? env('REPLACE_PATH_2') : env('REPLACE_PATH');
    $replace_path_thumb_extra = str_contains($article->thumbnail_extra, 'zdata') ? env('REPLACE_PATH_2') : env('REPLACE_PATH');
    ?>
    <section class="content-header">
        <h1>Chỉnh sửa bài viết blogs</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
            <li class="active">{{ trans('menu.media_zone') }}</li>
            <li class="active">{{ trans('menu.edit_article') }}</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form role="form" id="blog_form_edit" method="post">
                {{csrf_field()}}
                <div class="col-md-2">
                    <ul class="nav nav-tabs-custom nav-stacked" role="tablist">
                        <li role="presentation" class="active">
                            <a aria-controls="article_panel" role="tab" data-toggle="tab" href="#article_panel">
                                <i class="fa fa-bars"></i>
                                <strong>Bài viết blogs</strong>
                            </a>
                        </li>
                        <li role="presentation">
                            <a aria-controls="seo_panel" role="tab" data-toggle="tab" href="#seo_panel">
                                <i class="fa fa-bars"></i>
                                <strong>Tùy chọn SEO</strong>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-7">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="article_panel">
                            <div class="box box-info">
                                <div class="box-body">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label><i class="fa fa-list-ul"></i> {{ trans('article.title') }}</label>
                                            <input type="name" class="form-control" name='title'
                                                   value="{{$article->title}}"
                                                   placeholder="Nhập tiêu đề">
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fa fa-list-ul"></i> {{ trans('article.title_extra') }}
                                            </label>
                                            <input type="name" class="form-control" name='title_extra'
                                                   placeholder="Điền thêm tiêu đề" value="{{$article->title_extra}}">
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fa fa-paragraph"></i> Mô tả
                                            </label>
                                            <textarea class="form-control" name="description" rows="4"
                                                      id="description_article"
                                                      placeholder="{{ trans('article.description_ph') }}">{{$article->description}}</textarea>

                                            <small class="pull-right" style="margin-top: -25px;margin-right: 5px;">
                                                (words left: <span
                                                        id="word_left"></span>)
                                            </small>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fa fa-paragraph"></i> Nội dung
                                            </label>
                                            <textarea class="form-control" name="content"
                                                      id="editor">{!! $article->content !!}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fa fa-tags"></i> {{ trans('article.tag') }}</label>
                                            <input type="name" class="form-control"
                                                   placeholder="{{ trans('article.tag_ph') }}" id="tags_article"
                                                   name="tags">
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fa fa-tags"></i> Tin liên quan</label>
                                            <input type="name" class="form-control"
                                                   placeholder="{{ trans('article.tag_ph') }}" id="tags_article"
                                                   name="related">
                                        </div>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="seo_panel">
                            <div class="box box-info">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label><i class="fa fa-list-ul"></i> {{ trans('article.seo_title') }}</label>
                                        <input type="name" class="form-control" name="seo_title"
                                               value="<?php echo isset($review_seo_title) ? $review_seo_title : ''; ?>"
                                               placeholder="{{ trans('article.seo_title_ph') }}">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-list-ul"></i> {{ trans('article.seo_meta') }}</label>
                                        <input type="name" class="form-control" name="seo_meta"
                                               placeholder="{{ trans('article.seo_meta_ph') }}"
                                               value="<?php echo isset($review_seo_meta) ? $review_seo_meta : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-paragraph"></i> {{ trans('article.seo_description') }}
                                        </label>
                                        <textarea class="form-control" name="seo_description" rows="4"
                                                  name="seo_description"
                                                  placeholder="{{ trans('article.seo_description_ph') }}"><?php echo isset($review_seo_description) ? $review_seo_description : ''; ?></textarea>
                                    </div>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <i class="fa fa-image"></i>
                            <h3 class="box-title">Ảnh đại diện</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <a class="btn btn-block btn-danger fa fa-trash"
                               style="width:35px;position: relative;top:0px ;float: right;display: none;"
                               onclick="javascript:removeImage(this,1);">
                            </a>
                            <?php
                            if (str_contains($article->thumbnail, 'http') === false) {
                            ?>
                            <img src="{{ env('MEDIA_PATH') . str_replace($replace_path_thumb , '' , $article->thumbnail )}}"
                                 onclick="BrowseServer('id_of_the_target_input');" id="image_replace"
                                 style="margin-top:-28px;cursor: pointer;max-height: 200px;width:100%;">
                            <?php
                            } else {
                            ?>
                            <img src="{{ $article->thumbnail }}"
                                 onclick="BrowseServer('id_of_the_target_input');" id="image_replace"
                                 style="margin-top:-28px;cursor: pointer;max-height: 200px;width:100%;">
                            <?php
                            }
                            ?>
                            <div class="preview-placeholder" id="replace" style="display:none;">
                                <div>
                                    <i class="fa fa-plus fa-2x"
                                       onclick="BrowseServer('id_of_the_target_input');"></i><br>
                                    <h4 class="text-muted">Bấm vào chọn ảnh</h4>
                                </div>
                            </div>

                        </div><!-- /.box-body -->
                        <input id="id_of_the_target_input" type="hidden" name="thumbnail"
                               value="{{$article->thumbnail}}"/>
                    </div><!-- /.box -->
                    @if($article->thumbnail_extra != '')
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-image"></i>
                                <h3 class="box-title">{{trans('article.thumbnail_extra')}}</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <a class="btn btn-block btn-danger fa fa-trash"
                                   style="width:35px;position: relative;top:0px ;float: right;display: none;"
                                   onclick="javascript:removeImage(this,1);">
                                </a>
                                <?php
                                if (str_contains($article->thumbnail_extra, 'http') === false) {
                                ?>
                                <img onclick="BrowseServer('id_of_the_target_input_extra');"
                                     src="{{ env('MEDIA_PATH') . str_replace($replace_path_thumb_extra , '' , $article->thumbnail_extra )}}"
                                     id="image_replace_extra"
                                     style="margin-top:-28px;cursor: pointer;max-height: 200px;width:100%;">
                                <?php
                                } else {
                                ?>
                                <img src="{{ $article->thumbnail }}"
                                     onclick="BrowseServer('id_of_the_target_input');" id="image_replace"
                                     style="margin-top:-28px;cursor: pointer;max-height: 200px;width:100%;">
                                <?php
                                }
                                ?>

                                <div class="preview-placeholder" id="replace_extra" style="display:none;">
                                    <div>
                                        <i class="fa fa-plus fa-2x"
                                           onclick="BrowseServer('id_of_the_target_input_extra');"></i><br>
                                        <h4 class="text-muted">Bấm vào chọn ảnh</h4>
                                    </div>
                                </div>
                            </div><!-- /.box-body -->
                            <input id="id_of_the_target_input_extra" type="hidden" name="thumbnail_extra"
                                   value="{{$article->thumbnail_extra}}"/>
                        </div><!-- /.box -->
                    @else
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-image"></i>
                                <h3 class="box-title">{{trans('article.thumbnail_extra')}}</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <a class="btn btn-block btn-danger fa fa-trash"
                                   style="width:35px;position: relative;top:0px ;float: right;display: none;">
                                </a>
                                {{--<i class="fa fa-trash" style="margin-left: 94%;cursor: pointer;display: none"></i>--}}
                                <img onclick="BrowseServer('id_of_the_target_input_extra');" src=""
                                     id="image_replace_extra"
                                     style="display:none;margin-top:-28px;cursor: pointer;max-height: 200px;width:100%;">
                                <div class="preview-placeholder" id="replace_extra">
                                    <div>
                                        <i class="fa fa-plus fa-2x"
                                           onclick="BrowseServer('id_of_the_target_input_extra');"></i><br>
                                        <h4 class="text-muted">Bấm Vào! Chọn Ảnh</h4>
                                    </div>
                                </div>

                            </div><!-- /.box-body -->
                            <input id="id_of_the_target_input_extra" type="hidden" name="thumbnail_extra"/>
                        </div><!-- /.box -->
                    @endif
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <i class="fa fa-image"></i>
                            <h3 class="box-title">Tạo gallery</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <a class="btn btn-block btn-danger fa fa-trash"
                               style="width: 35px; position: relative; top: 0px; float: right;">
                            </a>
                            <img onclick="BrowseServer('id_of_the_target_input');" src="" id="image_replace"
                                 style="display:none;margin-top:-28px;cursor: pointer;max-height: 200px;width:100%;">
                            <div class="preview-placeholder" id="replace">
                                <div>
                                    <i class="fa fa-plus fa-2x" onclick="BrowseServer('id_of_the_folder');"></i><br>
                                    <h4 class="text-muted">Bấm vào chọn Folder</h4>
                                </div>
                            </div>

                        </div><!-- /.box-body -->
                        <div id="text-folder"><?php echo isset($review_gallery) ? $review_gallery : ''; ?></div>
                        <input id="id_of_the_folder" type="hidden" class="form-control"
                               value="<?php echo isset($review_gallery) ? $review_gallery : ''; ?>" name="gallery"/>
                    </div><!-- /.box -->
                    @can('PublishArticle')
                        <div class="box box-solid">
                            <div class="box-header">
                                <i class="fa fa-image"></i>
                                <h3 class="box-title">Thời gian xuất bản</h3>
                            </div>
                            <div class="box-body">
                                <!-- Color Picker -->
                                <div class="form-group">
                                    <label>Ngày Xuất Bản:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="datepicker" name="publish_date">
                                        <div class="input-group-addon add-on">
                                            <i class="fa fa-calendar" data-time-icon="icon-time"
                                               data-date-icon="icon-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.form group -->


                                <!-- time Picker -->
                                <div class="bootstrap-timepicker">
                                    <div class="bootstrap-timepicker-widget dropdown-menu">
                                        <table>
                                            <tbody>
                                            <tr>
                                                <td><a href="#" data-action="incrementHour"><i
                                                                class="glyphicon glyphicon-chevron-up"></i></a></td>
                                                <td class="separator">&nbsp;</td>
                                                <td><a href="#" data-action="incrementMinute"><i
                                                                class="glyphicon glyphicon-chevron-up"></i></a></td>
                                                <td class="separator">&nbsp;</td>
                                                <td class="meridian-column"><a href="#" data-action="toggleMeridian"><i
                                                                class="glyphicon glyphicon-chevron-up"></i></a></td>
                                            </tr>
                                            <tr>
                                                <td><span class="bootstrap-timepicker-hour">02</span></td>
                                                <td class="separator">:</td>
                                                <td><span class="bootstrap-timepicker-minute">15</span></td>
                                                <td class="separator">&nbsp;</td>
                                                <td><span class="bootstrap-timepicker-meridian">PM</span></td>
                                            </tr>
                                            <tr>
                                                <td><a href="#" data-action="decrementHour"><i
                                                                class="glyphicon glyphicon-chevron-down"></i></a></td>
                                                <td class="separator"></td>
                                                <td><a href="#" data-action="decrementMinute"><i
                                                                class="glyphicon glyphicon-chevron-down"></i></a></td>
                                                <td class="separator">&nbsp;</td>
                                                <td><a href="#" data-action="toggleMeridian"><i
                                                                class="glyphicon glyphicon-chevron-down"></i></a></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label>Thời gian xuất bản</label>

                                        <div class="input-group">
                                            <input type="text" class="form-control timepicker" name="publish_time">

                                            <div class="input-group-addon">
                                                <i class="fa fa-clock-o"></i>
                                            </div>
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                    <!-- /.form group -->
                                </div>
                            </div>
                            <div class="box-body">
                                <!-- Color Picker -->
                                <div class="form-group">
                                    <label>Trạng Thái:</label>
                                    <div class="input-group">
                                        <select class="form-control" name="status">
                                            <option value="draft" @if($article->status == 'draft' ) selected @endif >
                                                Draft
                                            </option>
                                            <option value="schedule"
                                                    @if($article->status == 'schedule' ) selected @endif >
                                                Schedule
                                            </option>
                                            <option value="pending"
                                                    @if($article->status == 'pending' ) selected @endif >
                                                Verify
                                            </option>
                                            <option value="publish"
                                                    @if($article->status == 'publish' ) selected @endif >
                                                Publish
                                            </option>
                                        </select>
                                        <div class="input-group-addon add-on">
                                            <i class="fa fa-warning" data-time-icon="icon-time"
                                               data-date-icon="icon-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    @else
                        <div class="box box-solid">
                            <div class="box-header">
                                <i class="fa fa-image"></i>
                                <h3 class="box-title">Chọn Trạng Thái Văn Bản</h3>
                            </div>
                            <div class="box-body">
                                <!-- Color Picker -->
                                <div class="form-group">
                                    <label>Trạng Thái:</label>
                                    <div class="input-group">
                                        <select class="form-control" name="status">
                                            <option value="draft">Draft</option>
                                            <option value="pending">Verify</option>
                                        </select>
                                        <div class="input-group-addon add-on">
                                            <i class="fa fa-warning" data-time-icon="icon-time"
                                               data-date-icon="icon-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    @endcan
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <i class="fa fa-book"></i>
                            <h3 class="box-title">Loại bài viết</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <select class="type_article form-control" name="type_article">
                                <option value=""></option>
                                <option value="Video" @if($article->type_article == 'Video') selected @endif>Video
                                </option>
                            </select>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                    <?php
                    $cate_choose = [];
                    foreach ($article->articleCategory as $v) {
                        $cate_choose[] = $v->id;
                    }
                    ?>
                    @foreach(config('admincp.type_category') as $k =>  $items )

                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-book"></i>
                                <h3 class="box-title">{{$items[0]}}</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="category">
                                    @foreach( $$k as $item )
                                        <div class="form-group category-item">
                                            <label>
                                                <input type="checkbox"
                                                       @if(in_array($item->id ,$cate_choose) ) checked
                                                       @endif  class="minimal" name="category[]"
                                                       value="{{ $item->id }}"> {{ $item->title }}
                                            </label>
                                            <i class="fa fa-flag flag_click"
                                               @if($article->parent_category == $item->id ) style="color:red"
                                               @endif  data-id="{{$item->id}}"></i>
                                        </div>
                                    @endforeach
                                    <input type="hidden" name="parent_id" value="{{$article->parent_category}}">
                                </div>
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->

                    @endforeach
                    <input name="id" id="id" type="hidden" value="{{$article->id}}"/>
                    <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-save"></i>
                        Update
                    </button>
            </form>
        </div>
    </section>
@stop

@section('custom_footer')

    <script>


        $('.fa-trash').show();
        var max_len = 200;
        $(document).ready(function () {
            $.getScript("https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js");
            $.getScript("{{ asset('dist/js/module/article.js?v4') }}");
            $("textarea[name=description]").on('keyup', function () {
                var words = 0;
                if (this.value !== '') {
                    var words = this.value.match(/\S+/g).length;
                    if (words > max_len) {
                        // Split the string on first 200 words and rejoin on spaces
                        var trimmed = $(this).val().split(/\s+/, max_len).join(" ");
                        // Add a space at the end to keep new typing making new words
                        $(this).val(trimmed + " ");
                    }
                }
                $('#word_left').text(max_len - words);
            });
            //iCheck for checkbox and radio inputs
            $('input[type="checkbox"].minimal').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
            $(".category").slimScroll({
                height: '250px'
            });
            // setPrimary hover event
            $(".category-item").hover(function () {
                // show
                $(this).find("a").show();
            }, function () {
                // hide
                var child = $(this).find("a");
                if ($(child).data('url') !== $("#inputPrimaryCateUrl").val()) {
                    $(this).find("a").hide();
                }
            });
            // setPrimary func
            $(".primary_category").click(function () {
                var a = $(this).data('url');
                var b = $(this).data('name');
                $(this).css('color', 'red');
                $("#inputPrimaryCateUrl").val(a);
                $("#inputPrimaryCateName").val(b);
                // hide other cate
                $(".primary_category").not(this).css('color', '').hide();
                $(this).show();
            });
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
                $('#image_replace').attr('src', '{{env("MEDIA_PATH")}}' + url.replace('{{env("REPLACE_PATH_2")}}', ''));
                $('#image_replace').show();
                oWindow = null;
            } else if (urlobj == 'id_of_the_target_input_extra') {
                $('#replace_extra').hide();
                $('.fa-trash').show();
                $('#image_replace_extra').attr('src', '{{env("MEDIA_PATH")}}' + url.replace('{{env("REPLACE_PATH_2")}}', ''));
                $('#image_replace_extra').show();
                oWindow = null;
            } else {
                $('#text-folder').html(url);
            }
        }
        $('input[name="tags"]').tagEditor({
            initialTags: [
                @if (!@empty($article->tags->meta_value))
                        @foreach (json_decode($article->tags->meta_value) as $items)
                        @if (!@empty($items))
                        @foreach($items as $k => $v)
                    "{{$v}}",
                @endforeach
                @endif
                @endforeach
                @endif
            ],
            autocomplete: {
                delay: 0, // show suggestions immediately
                source: '/media/article/tag/',
                minLength: 3,
                placeholder: "Enter Tags In Here!",
                position: {collision: 'flip'},
            }
        });
        function removeImage(tag, index) {
            if (index == 1) {
                $('#image_replace').hide();
                $('#replace').show();
                $('#id_of_the_target_input').attr('value', '');
            } else {
                $('#image_replace_extra').hide();
                $('#replace_extra').show();
                $('#id_of_the_target_input_extra').attr('value', '');
            }
            $(tag).hide();

        }
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
        $('#get_url_image_extra').on('click', function () {
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
                            $('#replace_extra').hide();
                            $('.fa-trash').show();
                            $('#id_of_the_target_input_extra').attr('value', inputValue);
                            $('#image_replace_extra').attr('src', inputValue);
                            $('#image_replace_extra').show();

                        }
                    });
                });
        });
        $('.primary_category').each(function () {
            $(this).click(function () {
                var parent = $(this).parents('.form-group.category-item:first');
                var label = parent.find('label');
                var input = parent.find('.icheckbox_square-blue');
                if (input.attr('aria-checked') == 'false') {
                    label.click();
                }
            });
        });
        $('input[name="related"]').tagEditor({
            initialTags: [
                @if (!@empty($article->related->meta_value))
                        @foreach (json_decode($article->related->meta_value) as $items)
                        @if (!@empty($items))
                        @foreach($items as $k => $v)
                    "{{str_replace(',' , '\\\,' ,$v )}}",
                @endforeach
                @endif
                @endforeach
                @endif
            ],
            autocomplete: {
                delay: 0, // show suggestions immediately
                source: '/media/article/related/',
                minLength: 3,
                maxlength: 255,
                placeholder: "Enter Tags In Here!",
                position: {collision: 'flip'},
            }
        });
        $(function () {
            $(".timepicker").timepicker({
                showInputs: false
            }).val('{{date("H:i" , strtotime($article->published_at))}}');
            $('#datepicker').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true
            }).val('{{date("d-m-Y" , strtotime($article->published_at))}}');
        });

    </script>
@stop