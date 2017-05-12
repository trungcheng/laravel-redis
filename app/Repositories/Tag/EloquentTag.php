<?php
namespace App\Repositories\Tag;

use App\Models\Tags;

class EloquentTag implements  TagRepository {

    public function __construct()
    {
        $this->tag = new Tags();
    }

    public function getAllTag() {
        $tags = $this->tag->get();
        if($tags) {
            return $tags;
        }
        return false;
    }

    public function getTagbyTagName($tag_mame) {
        $tag = $this->tag->where('name', '=', $tag_mame)->first();
        if($tag) {
            return $tag;
        }
        return false;
    }

    public function getTaglikeTagName($tag_name, $order, $limit) {
        $tag = Tags::where('name', 'regexp', '/'.$tag_name.'/')
            ->orderBy($order['col'], $order['mode'])
            ->get();
        if($tag) {
            return $tag;
        }
        return false;
    }

    public function findTagbyId($id) {

    }

    public function UpdateTag($id, $array) {

    }

    public function DeleteTag($id, $array) {

    }

    public function getTagWhere( $where , $take , $order_by ) {

    }

    public function getTagPaginate ($array , $paginate ) {

    }

    public function InsertTag($array) {
        $post = $this->tag ;
        foreach($array as $k => $v) {
            $post->$k = $v ;
        }
        if( $post->save() ){
            return true  ;
        }
        return false ;
    }

    public function getTagbyCategory() {

    }
}