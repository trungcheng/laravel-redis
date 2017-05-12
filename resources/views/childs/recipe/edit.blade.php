@extends('layouts.master')
@section('main_content')
    <?php
    $replace_path_thumb = str_contains($article->thumbnail, 'zdata') ? env('REPLACE_PATH_2') : env('REPLACE_PATH');
    $replace_path_thumb_extra = str_contains($article->thumbnail_extra, 'zdata') ? env('REPLACE_PATH_2') : env('REPLACE_PATH');
    ?>
    <section class="content-header">
        <h1>Edit Article Review</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{ trans('menu.media_zone') }}</li>
            <li class="active">{{ trans('menu.edit_recipe') }}</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form role="form" id="recipe_form_edit" method="post">
                {{csrf_field()}}
                <div class="col-md-2">
                    <ul class="nav nav-tabs-custom nav-stacked" role="tablist">
                        <li role="presentation" class="active">
                            <a aria-controls="article_panel" role="tab" data-toggle="tab" href="#article_panel">
                                <i class="fa fa-bars"></i>
                                <strong>Bài viết</strong>
                            </a>
                        </li>
                        <li role="presentation">
                            <a aria-controls="seo_panel" role="tab" data-toggle="tab" href="#seo_panel">
                                <i class="fa fa-bars"></i>
                                <strong>Tùy chọn SEO</strong>
                            </a>
                        </li>
                        <li role="presentation">
                            <a aria-controls="related_panel" role="tab" data-toggle="tab" href="#related_panel">
                                <i class="fa fa-bars"></i>
                                <strong>Tùy chọn khác</strong>
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
                                                   placeholder="Nhap tieu de">
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
                                        <div class="form-group">
                                            <label><i class="fa fa-youtube"></i> Video Youtube</label>
                                            <input type="text" class="form-control" name="youtube"
                                                   value="{{\Cache::get('youtube_'.$article->id)}}"
                                                   placeholder="Embed Youtube">
                                        </div>

                                        @if(  count($article->articleSteps) > 0  )
                                            <div id="append_steps">
                                                @foreach($article->articleSteps as $step )
                                                    <?php
                                                    $gallery = [];
                                                    if (json_decode($step->gallery)) {
                                                        $gallery = json_decode($step->gallery);
                                                    }
                                                    ?>
                                                    <div id="step_{{$step->number_step}}">
                                                        <div class="form-group" id="steps_{{$step->number_step}}"
                                                             data-step="{{$step->number_step}}">
                                                            <label><i class="fa fa-paragraph"></i> Hướng Dẫn
                                                                Bước {{$step->number_step}} </label>
                                            <textarea class="form-control" name="steps[]" rows="4"
                                                      placeholder="Bước 1">{{strip_tags($step->content)}}</textarea>
                                                        </div>
                                                        <div style="width:800px;margin-bottom:20px;"
                                                             id="fileupload{{$step->number_step}}">
                                                            <div class="row fileupload-buttonbar">
                                                                <div class="col-lg-7">
                                                                    <!-- The fileinput-button span is used to style the file input field as button -->
                                                    <span class="btn btn-success fileinput-button">
                                                        <i class="glyphicon glyphicon-upload"></i>
                                                        <span>Thêm Ảnh</span>
                                                        <input type="file" name="files[]" multiple>
                                                    </span>
                                                                    <span class="fileupload-process"></span>
                                                                </div>
                                                            </div>
                                                            <table role="presentation" style="width:600px;"
                                                                   class="table table-striped">
                                                                <tbody class="files">
                                                                @if(!empty($gallery))
                                                                    @foreach($gallery as $image )
                                                                        <tr class="template-download fade in">
                                                                            <td>
                                                                    <span class="preview">
                                                                           <img src="{{$image}}" width="400">
                                                                    </span>
                                                                                <input type="hidden"
                                                                                       name="files_step_{{$step->number_step}}[]"
                                                                                       value="{{str_replace('///', '/', $image)}}">
                                                                            </td>
                                                                            <?php if (explode('/', str_replace('///', '/', $image))) $array_image = explode('/', str_replace('///', '/', $image));
                                                                            if (!isset($array_image[4])) {
                                                                                $delete_image = '';
                                                                            } else {
                                                                                $delete_image = $array_image[4];
                                                                            }
                                                                            ?>
                                                                            <td>
                                                                                <button class="btn btn-danger delete"
                                                                                        data-type="DELETE"
                                                                                        data-url="/delete-image/{{trim($delete_image)}}">
                                                                                    <i class="glyphicon glyphicon-trash"></i>
                                                                                    <span>Xóa Ảnh</span>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach

                                                                @endif
                                                                </tbody>
                                                            </table>
                                                            @if($step->number_step > 1 )
                                                                <div class="remove_steps"
                                                                     onclick="remove_steps('{{$step->number_step}}')"
                                                                     style="margin-bottom:30px;cursor: pointer;"><i
                                                                            class="fa fa-close"></i>Xóa
                                                                    Bước {{$step->number_step}} </div>
                                                            @endif
                                                        </div>

                                                    </div>
                                                @endforeach
                                            </div>
                                            <div id="button_steps">
                                                <div class="add_steps" onclick="add_steps()"
                                                     data-step="{{$step->number_step}}"
                                                     style="margin-top:10px;cursor: pointer;">
                                                    <i class="fa fa-plus"></i>Thêm Bước
                                                </div>
                                            </div>
                                        @else
                                            <div class="form-group">
                                                <label><i class="fa fa-paragraph"></i> Nội dung
                                                </label>
                                                <textarea class="form-control" name="content"
                                                          id="editor">{!! $article->content !!}</textarea>
                                            </div>
                                        @endif
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
                                               value="<?php echo isset($recipe_seo_title) ? $recipe_seo_title : '' ?>"
                                               placeholder="{{ trans('article.seo_title_ph') }}">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-list-ul"></i> {{ trans('article.seo_meta') }}</label>
                                        <input type="name" class="form-control" name="seo_meta"
                                               placeholder="{{ trans('article.seo_meta_ph') }}"
                                               value="<?php echo isset($recipe_seo_meta) ? $recipe_seo_meta : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-paragraph"></i> {{ trans('article.seo_description') }}
                                        </label>
                                    <textarea class="form-control" name="seo_description" rows="4"
                                              name="seo_description"
                                              placeholder="{{ trans('article.seo_description_ph') }}"><?php echo isset($recipe_seo_description) ? $recipe_seo_description : '' ?></textarea>
                                    </div>
                                </div><!-- /.box-body -->
                            </div>
                        </div>


                        <div role="tabpanel" class="tab-pane" id="related_panel">
                            <div class="box box-info">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label><i class="fa fa-clock-o"></i> Thời gian chuẩn bị</label>
                                        <select name="prep_time" class="slPrepTime">
                                            <?php
                                            $i = 15;
                                            while ($i <= 105) {
                                            if ($i === 105) {
                                            ?>
                                            <option @if(isset($recipe_prep_time) && $recipe_prep_time === '> 90 phút') selected
                                                    @endif value="{{'> 90 phút'}}">{{'> 90 phút'}}</option>
                                            <?php
                                            } else {
                                            ?>
                                            <option @if(isset($recipe_prep_time) && $recipe_prep_time === "$i phút") selected
                                                    @endif value="{{$i.' phút'}}">{{$i.' phút'}}</option>
                                            <?php
                                            }
                                            $i = $i + 15;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-clock-o"></i> Thời gian nấu</label>
                                        <select name="cook_time" class="slPrepTime">
                                            <?php
                                            $i = 15;
                                            while ($i <= 105) {
                                            if ($i === 105) {
                                            ?>
                                            <option @if(isset($recipe_cook_time) && $recipe_cook_time === '> 90 phút') selected
                                                    @endif value="{{'> 90 phút'}}">{{'> 90 phút'}}</option>
                                            <?php
                                            } else {
                                            ?>
                                            <option @if(isset($recipe_cook_time) && $recipe_cook_time === "$i phút") selected
                                                    @endif value="{{$i.' phút'}}">{{$i.' phút'}}</option>
                                            <?php
                                            }
                                            $i = $i + 15;
                                            }
                                            ?>
                                        </select>

                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-users"></i> Số Người Dùng Bữa</label>
                                        <input class="form-control" name="number_people"
                                               value="{{$article->number_people}}"
                                               placeholder="Số Người Dùng Bữa">
                                    </div>
                                    <div class="form-group" id="append_ingredients">
                                        <label><i class="fa fa-balance-scale"></i> Nguyên Liệu</label>
                                        @foreach($article->getIngredients as $items )
                                            <div class="ig_{{$items->id}}">
                                                <br>
                                                <input class="form-control" style="height: 30px;;margin-top: 10px;"
                                                       name="ingredients[]"
                                                       value="{{$items->name}}"
                                                       placeholder="Nguyên liệu">
                                                <input class="" type="number" step="0.01" min="0"
                                                       style="height: 30px;width:80px;margin-top:10px;"
                                                       value="{{$items->pivot->quanlity}}"
                                                       placeholder="Số lượng" name="quanlity[]">
                                                <input class="" style="height: 30px;width:80px;" name="quanlity_type[]"
                                                       value="{{$items->pivot->quanlity_type}}"
                                                       placeholder="Đơn vị">
                                                <br>
                                                <div onclick="RemoveClass('.ig_{{$items->id}}')"
                                                     style="margin-top:10px;cursor: pointer"><i class="fa fa-close"></i>Xóa
                                                    nguyên liệu
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                    <div class="add_ingredients" style="margin-top:10px;cursor: pointer">
                                        <i class="fa fa-plus"></i>Thêm nguyên liệu
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
                            <h3 class="box-title">Tạo Gallery</h3>
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
                                    <h4 class="text-muted">Bấm vào chọn folder</h4>
                                </div>
                            </div>

                        </div><!-- /.box-body -->
                        <div id="text-folder"><?php echo isset($recipe_gallery) ? $recipe_gallery : ''; ?></div>
                        <input id="id_of_the_folder" type="hidden" class="form-control" value="" name="gallery"/>
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
                                        <option value="draft" @if($article->status == 'draft' ) selected @endif >Draft
                                        </option>
                                        <option value="schedule" @if($article->status == 'schedule' ) selected @endif >
                                            Schedule
                                        </option>
                                        <option value="pending" @if($article->status == 'pending' ) selected @endif >
                                            Verify
                                        </option>
                                        <option value="publish" @if($article->status == 'publish' ) selected @endif >
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
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-book"></i>
                                <h3 class="box-title">Sự kiện</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <select class="event form-control" name="event">
                                    <option value="0"></option>
                                    @if(!empty($events))
                                        @foreach($events as $event)
                                            <option value="{{$event->id}}"
                                                    @if($event->id == $article->event_id) selected @endif>{{$event->event_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->

                        @foreach(config('admincp.type_category') as $k =>  $items )
                            <div class="box box-solid">
                                <div class="box-header with-border">
                                    <i class="fa fa-book"></i>
                                    <h3 class="box-title">{{$items[0]}}</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <div class="category">
                                        @foreach( $$k as $item )
                                            <?php
                                            $cat_array = array();
                                            if ($article->articleCategory) {
                                                foreach ($article->articleCategory as $cat_item) {
                                                    $cat_array[] = $cat_item->id;
                                                }
                                            }
                                            ?>
                                            <div class="form-group category-item">
                                                <label>
                                                    <input @if(in_array($item->id , $cat_array))checked
                                                           @endif type="checkbox" class="minimal" name="category[]"
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

                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-book"></i>
                                <h3 class="box-title"> Độ khó </h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="category">
                                    <select class="slLevel" name="slLevel">
                                        @foreach( $level as $item )
                                            <?php
                                            $cat_array = array();
                                            if ($article->articleCategory) {
                                                foreach ($article->articleCategory as $cat_item) {
                                                    $cat_array[] = $cat_item->id;
                                                }
                                            }
                                            ?>
                                            <option value="{{ $item->id }}"
                                                    @if(in_array($item->id , $cat_array))selected @endif>{{ $item->title }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->

                        <input name="id" id="id" type="hidden" value="{{$article->id}}"/>
                        <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-save"></i>
                            Update
                        </button>
            </form>
            <style type="text/css">
                .slLevel, .slPrepTime {
                    width: 100%;
                    padding: 5px;
                    border-radius: 5px;
                    font-size: 16px;
                    border: 1px solid #dadada;
                }
            </style>
        </div>
    </section>
@stop

@section('custom_footer')

    <script>
        $('.fa-trash').show();
        var max_len = 200;
        $(document).ready(function () {
            $.getScript("https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js");
            $.getScript("{{ asset('dist/js/module/article.js?v6') }}");
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

        @if(!request()->has('member') && request()->has('member')=='thanhvien')
            $('input[name="ingredients[]"]').each(function () {
            $(this).tagEditor({
                autocomplete: {
                    delay: 0,
                    source: '/media/article/search-ingredients/',
                    minLength: 3,
                    maxlength: 255,
                    position: {collision: 'flip'},
                },
                maxTags: 1,
                style_height: true
            });
        });
        @endif


    $('.add_ingredients').on('click', function () {
            var str = makeid();
            $html = '<div class="ig_' + str + '"><br><input class="form-control" style="height: 30px;" name="ingredients[]" id="' + str + '" placeholder="Nguyên liệu"> <input class="" type="number" style="height: 30px;width:80px;margin-top:10px;" step="0.01" min="0" name="quanlity[]" placeholder="Số lượng"> <input class="" style="height: 30px;width:80px;" name="quanlity_type[]" placeholder="Đơn vị"><br><div onclick="RemoveClass(' + "'.ig_" + str + "'" + ')" style="margin-top:10px;cursor: pointer"> <i class="fa fa-close"></i>Xóa nguyên liệu </div></div>' +
                    '<script>' +
                    'createTag("' + str + '") ; ' +
                    '<\/script>';
            $('#append_ingredients').append($html);
            $(this).attr('data-id', str + 1);

        });
        function createTag(str) {
            $(document).ready(function () {
                $.getScript("{{ asset('dist/js/jquery-ui.js') }}", function () {
                    $.getScript("{{ asset('dist/js/caret.js') }}", function () {
                        $.getScript("{{ asset('dist/js/tag.js') }}", function () {
                            $('#' + str).tagEditor({
                                autocomplete: {
                                    delay: 0,
                                    source: '/media/article/search-ingredients/',
                                    minLength: 3,
                                    maxlength: 255,
                                    position: {collision: 'flip'},
                                },
                                maxTags: 1,
                                style_height: true
                            });
                        });

                    });
                });
            });

        }
        function add_steps() {
            var str = makeid();
            $id = parseInt($('.add_steps').data('step')) + 1;
            $('.add_steps').remove();
            $button = '<div class="add_steps" onclick="add_steps()" data-step="' + $id + '" style="margin-top:10px;cursor: pointer;"> <i class="fa fa-plus"></i>Thêm Bước </div>'
            $('#button_steps').append($button);
            $file_path = "{{env('REPLACE_PATH').'user'.auth()->user()->id}}";
            $html = '<div id="step_' + $id + '"><div class="form-group"> <label><i class="fa fa-paragraph"></i> Hướng Dẫn Bước ' + $id + ' </label> ' +
                    '<textarea class="form-control" name="steps[]" rows="4" placeholder="Bước ' + $id + '"></textarea> ' +
                    '</div> <div style="width:800px;margin-bottom:20px;" id="fileupload' + $id + '"> ' +
                    '<div class="row fileupload-buttonbar"> ' +
                    '<div class="col-lg-7"> ' +
                    '<span class="btn btn-success fileinput-button"> <i class="glyphicon glyphicon-upload"></i> ' +
                    '<span>Thêm Ảnh</span> ' +
                    '<input type="file" name="files[]" multiple> ' +
                    '</span>' +
                    '<span class="fileupload-process"></span> ' +
                    '</div> ' +
                    '</div> ' +
                    '<table role="presentation" style="width:600px;" class="table table-striped"> <tbody class="files"></tbody> </table> </div><div class="remove_steps" onclick="remove_steps(\'' + $id + '\')" style="margin-bottom:30px;cursor: pointer;"> <i class="fa fa-close"></i>Xóa Bước ' + $id + ' </div></div>' +
                    "<script> " +
                    "createUpload('" + $id + "' , '" + $file_path + "') " +
                    "<\/script>";
            $js = $('#js').html();
            $temp = '<script id="template-download-' + $id + '" type="text/x-tmpl"> ' +
                    '{% for (var i=0, file; file=o.files[i]; i++) { %} ' +
                    '<tr class="template-download fade"> ' +
                    '<td> ' +
                    '<span class="preview"> ' +
                    '{% if (file.thumbnailUrl) { %} ' +
                    '<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img width="300" src="{%=file.thumbnailUrl%}"></a> {% } %} </span> ' +
                    '<input type="hidden" name="files_step_' + $id + '[]" value="{%=file.url%}"> ' +
                    '</td><td> {% if (file.deleteUrl) { %} <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} {% } %}> <i class="glyphicon glyphicon-trash"></i> <span>Xóa Ảnh</span> </button> {% } else { %} <button class="btn btn-warning cancel"> <i class="glyphicon glyphicon-ban-circle"></i> <span>Cancel</span> </button> {% } %} </td> </tr> {% } %} <\/script>';
            $('#append_script').append($js);
            $('#append_steps').append($html + $temp);
        }
        function remove_steps(id) {
            $('#step_' + id).remove();
            $id = parseInt($('.add_steps').data('step')) - 1;
            $('.add_steps').remove();
            $button = '<div class="add_steps" onclick="add_steps()" data-step="' + $id + '" style="margin-top:10px;cursor: pointer;"> <i class="fa fa-plus"></i>Thêm Bước </div>'
            $('#button_steps').append($button);
        }
        $('#fileupload').fileupload({
            url: '/upload-image'
        });

    </script>

    @if(  count($article->articleSteps) > 0  )
        @foreach($article->articleSteps as $step )
            <script id="template-download-{{$step->number_step}}" type="text/x-tmpl">
            {% for (var i=0, file; file=o.files[i]; i++) { %}
                <tr class="template-download fade">
                    <td>
                        <span class="preview">
                            {% if (file.thumbnailUrl) { %}
                                <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img width="300"  src="{%=file.thumbnailUrl%}"></a>
                            {% } %}
                        </span>
                        <input type="hidden" name="files_step_{{$step->number_step}}[]" value="{%=file.url%}">
                    </td>
                    <td>
                        {% if (file.deleteUrl) { %}
                            <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                                <i class="glyphicon glyphicon-trash"></i>
                                <span>Xóa Ảnh</span>
                            </button>
                        {% } else { %}
                            <button class="btn btn-warning cancel">
                                <i class="glyphicon glyphicon-ban-circle"></i>
                                <span>Cancel</span>
                            </button>
                        {% } %}
                    </td>
                </tr>
            {% } %}



















            </script>
            <script>
                createUpload("{{$step->number_step}}", '');
            </script>
        @endforeach
    @endif
@stop
@section('script_upload')
    <script src="{{ asset('js/jquery.ui.widget.js') }}"></script>
    <script src="/js/tmpl.min.js"></script>
    <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
    <script src="/js/load-image.all.min.js"></script>
    <!-- The Canvas to Blob plugin is included for image resizing functionality -->
    <script src="/js/canvas-to-blob.min.js"></script>
    <!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
    <script src="/js/bootstrap.min.js"></script>
    <!-- blueimp Gallery script -->
    <script src="/js/jquery.blueimp-gallery.min.js"></script>
    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="{{ asset('js/jquery.iframe-transport.js') }}"></script>
    <!-- The basic File Upload plugin -->
    <script src="{{ asset('js/jquery.fileupload.js') }}"></script>
    <!-- The File Upload processing plugin -->
    <script src="{{ asset('js/jquery.fileupload-process.js') }}"></script>
    <!-- The File Upload image preview & resize plugin -->
    <script src="{{ asset('js/jquery.fileupload-image.js') }}"></script>
    <!-- The File Upload audio preview plugin -->
    <script src="{{ asset('js/jquery.fileupload-audio.js') }}"></script>
    <!-- The File Upload video preview plugin -->
    <script src="{{ asset('js/jquery.fileupload-video.js') }}"></script>
    <!-- The File Upload validation plugin -->
    <script src="{{ asset('js/jquery.fileupload-validate.js') }}"></script>
    <!-- The File Upload user interface plugin -->
    <script src="{{ asset('js/jquery.fileupload-ui.js') }}"></script>
    <!-- The main application script -->
    <script src="{{ asset('js/main.js') }}"></script>
@stop