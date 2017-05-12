<div id="viewReviewArticle">
    <div class="row">
        <div class="col-md-8">
            <div>
                <h4>{{$article->title}}</h4>
            </div>
            <div style="padding-top: 3px; color: #999; font-size: 14px;">{{ trans('article.by') }}
                <strong>{{$article->creator['name']}}</strong> {{ trans('article.at') }} {{ $article->created_at}}
            </div>
            <div>
                <h4>{{$article->description}}</h4>
            </div>
            <div>
                <h4>{!!$article->content!!}</h4>
            </div>
            <div>
            @foreach($article->primary_category as $category)
                <h5>{{ trans('article.category') }} : {{$category['category_name']}}</h5>
            @endforeach
            </div>
            <div>
                @foreach($article->tags as $tag)
                    <h5>{{ trans('article.tag') }} : {{$tag['tag_name']}}</h5>
                @endforeach
            </div>
        </div>
        <div class="col-md-4">
            <div>
                <img style="width: 250px;" src="{{$article->thumbnail}}">
                <h4><center> {{ trans('article.image_thumb') }}</center></h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1"  >
            <button style="width: 130px;" class="btn @if($article->status =="draft"){{"btn-danger"}}@else{{"btn-primary"}}@endif" onclick="activeArticle(this)" data-status="@if($article->status =="draft"){{"published"}}@else{{"draft"}}@endif" data-article_id="{{$article->id}}" type="submit">@if($article->status =="draft"){{ trans('article.active') }}@else{{ trans('article.deactive') }}@endif</button>
        </div>
    </div>
</div>

<div style="display: none" id="webPublish">
    <div class="row">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="col-md-5">
                    <center>{{ trans('article.name') }}</center>
                </th>
                <th class="col-md-5">
                    <center>{{ trans('article.url') }}</center>
                </th>
                <th class="">
                    <center>{{ trans('article.check') }}</center>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <center>vieclamnambo.vn</center>
                </td>
                <td> <center><a href="http://vieclamnambo.vn/">vieclamnambo.vn</a></center></td>
                <td>
                    <center>
                        <label>
                            <input type="checkbox" class="minimal listWeb" name="listWeb[]" value="http://vieclamnambo.vn/">
                        </label>
                    </center>
                </td>
            </tr>
            <tr>
                <td>
                    <center>bongdaquocte.vn</center>
                </td>
                <td> <center><a href="http://bongdaquocte.vn/">bongdaquocte.vn</a></center></td>
                <td>
                    <center>
                        <label>
                            <input type="checkbox" class="minimal listWeb" name="listWeb[]" value="http://bongdaquocte.vn/">
                        </label>
                    </center>
                </td>
            </tr>
            <tr>
                <td>
                    <center>inter.vn</center>
                </td>
                <td> <center><a href="http://inter.vn/">inter.vn</a></center></td>
                <td>
                    <center>
                        <label>
                            <input type="checkbox" class="minimal listWeb" name="listWeb[]" value="http://inter.vn/">
                        </label>
                    </center>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div style="float: right; margin-right:35px; " class="col-md-2"  >
            <button  onclick="summitToWebPublish(this)" data-article_id="{{$article->id}}" data-type="article" style="width: 130px" class="btn btn-success btn-block" type="submit">Publish now</button>
        </div>
    </div>
</div>

<script>
    $('input[type="checkbox"].minimal').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });


</script>





