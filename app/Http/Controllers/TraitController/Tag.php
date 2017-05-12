<?php
namespace App\Http\Controllers\TraitController;

use Illuminate\Http\Request;
use App\Jobs\ActionContent;
use App\Jobs\ReleaseContent;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\Article;
use App\Models\Category ;
use App\Http\Requests;
use App\Models\Tags;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

trait Tag
{

    function getTag()
    {
        $tag = $this->_request->get('term');
        $find_tag = Category::where('title', 'like', "%$tag%")->where('type' , 'tags')->take(4)->get();
        foreach ($find_tag as $items) {
            $array[] = $items->title;
        }
        return $array;
    }
    function getRelated(){
        $tag = $this->_request->get('term');
        $find_tag = Article::where('title', 'like', "%$tag%")->take(4)->get();
        foreach ($find_tag as $items) {
            $array[] = str_replace(',' , '\,' , $items->title);
        }
        return $array;
    }
}