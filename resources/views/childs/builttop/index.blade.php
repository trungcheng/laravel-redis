@extends('layouts.master')

@section('main_content')
    <div id="load-built">

    </div>
    <section class="content-header">
        <h1>Tìm kiếm bài viết</h1>
    </section>
    <section class="content">
        <div>
            <div style="padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #ddd">
                <form method="get" action="" autocomplete="off" role="form" class="form-inline">
                    <div class="form-group">
                        <input type="text" class="form-control search_top" name="key" id="key"
                               value="{{ old('key') }}"
                               autocomplete="off" placeholder="{{ trans('article.search_by_title') }}"
                               style="width: 150px;">
                    </div>
                    <input type="submit" class="btn btn-danger" name="search" value="{{ trans('home.search') }}">
            </div>
            @if(isset($articles) )
            <div class="post-container">
                <div class="box box-solid">
                    <div class="box-body no-padding">
                        <div>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>{{ trans('article.title') }}</th>
                                    <th>{{ trans('article.author') }}</th>
                                    <th>{{ trans('article.categories') }}</th>
                                    <th>{{ trans('article.created_at') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($articles as $article)
                                    <tr id="" class="post-item post-id-{{$article['id']}}" >
                                        <td>
                                            <p>{{ $article['title'] }}</p>
                                            <p><a href="#builtop" class="addbuilt" data-href="/media/built-top/add/{{ $article['id'] }}">Add Built Top</a></p>
                                        </td>
                                        <td><p>{{ $article['creator'] }}</p></td>
                                        <td><p>
                                                @foreach($article->articleCategory as $value)
                                                {{ $value->title }}
                                                        <!--{{ @str_slug($value->title) }}-->
                                                @endforeach
                                            </p></td>
                                        <td><p>{{ $article['created_at'] }}</p></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $articles->appends(['key' => \Request::get('key')])->render() !!}

                </form>
            </div>
        </div>
        @endif
    </section>

    <div class="modal fade" id="reviewArticleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div style="width: 70%;" class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button id="close" type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h2 class="col-md-9 modal-title" id="exampleModalLabel">{{ trans('article.review_article') }}</h2>
                    <button id="btn-status" style="width:130px;margin-left: 60px;" status="off"  id="summitToPublish" class="btn btn-success" type="button">
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
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('dist/js/module/article.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/iCheck/icheck.js') }}"></script>
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
    <script>
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
            $('.'+urlobj).attr('src' , '{{env("MEDIA_PATH")}}' + url.replace('{{env("REPLACE_PATH")}}' , '') );
            $('#image_replace').show();
            oWindow = null;
        }
        function publish(id, check){
            var link ='';
            if(check === 1){
                link = "{{URL::to('/media/built-top/publish/1')}}";
            }else{
                link = "{{URL::to('/media/built-top/publish/2')}}"
            }
            $.ajax({
            type: 'POST',
            url: link,
            data: 'id=' + id,
            success: function (obj) {
                if (obj !== null) {
                    obj = $.parseJSON(obj);
                    if(obj.status === 'success'){
                        location.reload();

                    }
                }
            },
            error: function (a, b, c) {
            }
            });
        }
        
        function changepos(id, check){
            $('#btnPostion').attr("disabled", "disabled");
            if (window.confirm('You want change position built top you care?') === true) {
                    var k = 1;
                    var pos = '';
                    var strID = '';
                    var key = 'changepos';
                    $('#sortable tr').each(function (i, row) {
                        var $row = $(row);
                        var family = $row.find('input[name*="select_id"]');
                        family.each(function (i, checkbox) {
                            var chk = $(checkbox).val();
                            strID += chk + ',';
                        });
                        k++;
                    });
                    for (var i = 1; i <= (k - 1); i++) {
                        pos += i + ',';
                    }
                    $.ajax({
                        type: 'POST',
                        url: "{{URL::to('/media/built-top/changepos')}}",
                        data: 'id=' + strID + '&position=' + pos,
                        success: function (obj) {
                            if (obj !== null) {
                                obj = $.parseJSON(obj);
                                if(obj.status === 'success'){
                                    location.reload();

                                }
                            }
                        },
                        error: function (a, b, c) {
                            $('#btnPostion').removeAttr("disabled");
                        }
                    });
                } else {
                    $('#btnPostion').removeAttr("disabled");
                    return false;
                }
        }
        
        function deleteBuilt(id, check){
            $('#btnDelete').attr("disabled", "disabled");
            if (window.confirm('You want delete builttops ?') === true) {
                var strID = '';
                $('#sortable input[type=checkbox]').change(function () {
                    $('#lberror').html('');
                });
                $('#sortable input[type=checkbox]:checked').each(function () {
                    strID += $(this).val().trim() + ',';
                });
                if (strID === '') {
                    $('#lberror').html('* You have not selected an article .');
                } else {
                   $.ajax({
                        type: 'POST',
                        url: "{{URL::to('/media/built-top/delete')}}",
                        data: 'strID=' + strID,
                        success: function (obj) {
                            if (obj !== null) {
                                obj = $.parseJSON(obj);
                                if(obj.status === 'success'){
                                    location.reload();
                                }
                            }
                        },
                        error: function (a, b, c) {
                            $('#btnDelete').removeAttr("disabled");
                        }
                    });
                }
            }else {
                $('#btnDelete').removeAttr("disabled");
                return false;
            }
        }
        
        function submit(id){
            $('#btnSubmit').attr("disabled", "disabled");
            if (window.confirm('You want submit builttops ?') === true) {
                var strID = '';
                var strImg = '';
                $('#sortable input[type=checkbox]').change(function () {
                    $('#lberror').html('');
                });
                $('#sortable input[type=checkbox]:checked').each(function () {
                    strID += $(this).val().trim() + ',';
                    strImg += $('#id_of_the_target_input_'+$(this).val().trim()).val().trim() + ',';
                });
                if (strID === '') {
                    $('#lberror').html('* You have not selected an article .');
                } else {
                   $.ajax({
                        type: 'POST',
                        url: "{{URL::to('/media/built-top/submit')}}",
                        data: 'strID=' + strID+'&strImg='+strImg,
                        success: function (obj) {
                            if (obj !== null) {
                                obj = $.parseJSON(obj);
                                if(obj.status === 'success'){
                                    location.reload();
                                }
                            }
                        },
                        error: function (a, b, c) {
                            $('#btnSubmit').removeAttr("disabled");
                        }
                    });
                }
            }else {
                $('#btnSubmit').removeAttr("disabled");
                return false;
            }
        }
</script>
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
            $('.addbuilt').each(function(){
                $(this).on( 'click' , function (){
                    $.ajax({
                                type: "GET",
                                url: $(this).data('href'),
                                dataType: 'json',
                                success: function (res) {
                                    event.preventDefault();
                                    swal({title: res.msg, type: res.status} , function(isConfirm){
                                        if(isConfirm){
                                            $('#load-built').load('{{URL::to('/media/built-top/list')}} #list-built' );
                                        }
                                    });
                                }
                            }
                    );
                });
            });
            $('#load-built').load('{{URL::to('/media/built-top/list')}} #list-built' , function (){
                $("#sortable").sortable();
                $("#sortable").disableSelection();
                 
            });

        });
       
    </script>
@stop