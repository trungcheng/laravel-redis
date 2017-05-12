@extends('layouts.master')

@section('main_content')
<div id="list-built">
    <section class="content-header">
        <h1>List Built Top</h1>
    </section>
    <section class="content">
        <div>
            @if(isset($articles) )
            <div class="post-container">
                <div class="box box-solid">
                    <div class="box-body no-padding">
                        <div>
                            <label id="lberror" name="lberror" style="color: red;"></label>
                            <input type="button" id="btnPostion" name="btnPostion" class="button action" value="Change postion" onclick="changepos();">
                            <input type="button" id="btnDelete" name="btnDelete" class="button action" style="margin: 10px 5px;" value="Delete" onclick="deleteBuilt();">
                            <input type="button" id="btnSubmit" name="btnSubmit" class="button action" style="margin: 10px 5px;" value="Submit" onclick="submit();">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Avatar</th>
                                        <th>{{ trans('article.title') }}</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable" data-wp-lists="list:built-top">
                                    @foreach($articles as $article)
                                    <tr id="" class="post-item post-id-{{$article['id']}}">
                                        <td scope="row" class="check-column">
                                            <input type="checkbox" name="select_id[{{$article['id']}}]"
                                                   value="{{$article['id']}}" id="cb-select-{{$article['id']}}">
                                        </td>
                                        <td>
                                            <img width="150" class='id_of_the_target_input_{{$article->id}}'  onclick="BrowseServer('id_of_the_target_input_{{$article->id}}');" src="{{ env('MEDIA_PATH') . str_replace(env('REPLACE_PATH') , '' , $article['link_img'] )}}"  >
                                            <input type="hidden" name="thumbnail" id="id_of_the_target_input_{{$article->id}}" value="{{ URL::to('/').'/'.$article['link_img'] }}" />
                                        </td>
                                        <td>
                                            <p>{{ $article['name'] }}</p>
                                        </td>
                                        <td>
                                            <?php
                                            if ($article['status'] === 2) {
                                                ?><p>Publish</p><?php
                                            } elseif ($article['status'] === 1) {
                                                ?><p>Verify</p><?php
                                            } else {
                                                ?><p>Draft</p><?php
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <!--<button id="btnEdit" name="btnEdit">Edit</button>-->
                                            <!--<button id="btnCreateFile" name="btnCreateFile">Create file</button>-->
                                            <?php
                                            if ($article['status'] === 2) {
                                                ?>
                                                <button id="btnVerify" name="btnVerify"
                                                        onclick="publish('{{ $article['id'] }}', 1);">Verify
                                                </button>
                                                <?php
                                            } else {
                                                ?>
                                                <button id="btnPublish" name="btnPublish"
                                                        onclick="publish('{{ $article['id'] }}', 2);">Publish
                                                </button>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <style type="text/css">
                    table tr td button, .button {
                        background: #E68447;
                        border: 0px;
                        border-radius: 1px;
                        color: #fff;
                        padding: 2px 10px;
                        font-weight: bold;
                    }

                    table tr td button:hover, .button:hover {
                        color: #333;
                    }

                    .button{
                        padding: 5px 10px;
                        float: right;
                        margin: 10px 40px 10px 5px;
                    }
                </style>
                {!! $articles->render() !!}
            </div>
        </div>
        @endif
    </section>
</div>
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
            <div class="modal-body" id="reviewArticleModalBody">

            </div>
        </div>
    </div>
</div>

@stop

@section('custom_header')
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css') }}">
<link href="{{ asset('plugins/iCheck/minimal/blue.css') }}" rel="stylesheet">
@stop

@section('custom_footer')
<script type="text/javascript" src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('dist/js/module/article.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/iCheck/icheck.js') }}"></script>
<script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
<script>
                                                                                    $(document).ready(function () {

                                                                            $('#reportrange').daterangepicker(
                                                                            {
                                                                            ranges: {
                                                                            '{{ trans('article.today') }}': [moment(), moment()],
                                                                                    '{{ trans('article.yesterday') }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                                                                    '{{ trans('article.last_7_day') }}': [moment().subtract(6, 'days'), moment()],
                                                                                    '{{ trans('article.last_30_day') }}': [moment().subtract(29, 'days'), moment()],
                                                                                    '{{ trans('article.this_month') }}': [moment().startOf('month'), moment().endOf('month')],
                                                                                    '{{ trans('article.last_month') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                                                                            },
                                                                                    startDate: moment('{{ date('Y - m - 01 00:00:00', time()) }}'),
                                                                                    endDate: moment('{{ date('Y - m - t 23:59:59', time()) }}')
                                                                            },
                                                                                    function (start, end) {
                                                                                    $('#reportrange span').html(start.format('D MMMM, YYYY') + ' - ' + end.format('D MMMM, YYYY'));
                                                                                            $('#start_date').val(start.format('YYYY-MM-DD HH:mm:ss'));
                                                                                            $('#end_date').val(end.format('YYYY-MM-DD HH:mm:ss'));
                                                                                    }
                                                                            );
                                                                                    var timeSelect = moment('{{date('Y - m - 01 00:00:00', time())}}').format('D MMMM, YYYY') + ' - ' + moment('{{date('Y - m - t 23:59:59', time())}}').format('D MMMM, YYYY');
                                                                                    $('#time_select').html(timeSelect);
                                                                                    $('#time_select2').html(timeSelect);
                                                                                    $('.addbuilt').each(function () {
                                                                            $(this).on('click', function () {
                                                                            $.ajax({
                                                                            type: "GET",
                                                                                    url: $(this).data('href'),
                                                                                    dataType: 'json',
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
                                                                            });
                                                                            });
                                                                                    function BrowseServer(obj)
                                                                                    {
                                                                                    urlobj = obj;
                                                                                            OpenServerBrowser(
                                                                                                    "{{url('/')}}" + '/filemanager/index.html',
                                                                                                    screen.width * 0.7,
                                                                                                    screen.height * 0.7);
                                                                                    }
                                                                            function OpenServerBrowser(url, width, height)
                                                                            {
                                                                            var iLeft = (screen.width - width) / 2;
                                                                                    var iTop = (screen.height - height) / 2;
                                                                                    var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes";
                                                                                    sOptions += ",width=" + width;
                                                                                    sOptions += ",height=" + height;
                                                                                    sOptions += ",left=" + iLeft;
                                                                                    sOptions += ",top=" + iTop;
                                                                                    var oWindow = window.open(url, "BrowseWindow", sOptions);
                                                                            }

                                                                            function SetUrl(url, width, height, alt)
                                                                            {
                                                                            document.getElementById(urlobj).value = url;
                                                                                    $('#replace').hide();
                                                                                    $('.fa-trash').show();
                                                                                    $('.'+urlobj).attr('src', url);
                                                                                    //$('#image_replace').show();
                                                                                    oWindow = null;
                                                                            }

</script>
@stop