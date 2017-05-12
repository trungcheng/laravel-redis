<?php
/**
 * Created by PhpStorm.
 * User: phucnt
 * Date: 18/12/2015
 * Time: 13:18
 */

namespace App\Repositories\Post;
interface PostRepository
{
    public function getAllPost();

    function getAllPostPage($page, $user);

    public function findPostbyId($id);

    public function UpdatePost($id, $array);

    public function DeletePost($id);

    public function getPostWhere($where, $take, $order_by);

    public function getPostPaginate($array, $paginate);

    public function InsertPost($array);

    public function getPostbyCategory();

    public function getSearch($keyword, $page, $user);

    public function ActivePost($id, $status);

    public function getById($id);


}