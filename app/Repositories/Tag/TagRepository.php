<?php
namespace App\Repositories\Tag;

interface TagRepository {

    public function getAllTag();

    public function getTagbyTagName($tag_mame);

    public function getTaglikeTagName($tag_name, $order, $limit);

    public function findTagbyId($id);

    public function UpdateTag($id, $array);

    public function DeleteTag($id, $array);

    public function getTagWhere( $where , $take , $order_by );

    public function getTagPaginate ($array , $paginate ) ;

    public function InsertTag($array) ;

    public function getTagbyCategory() ;
}