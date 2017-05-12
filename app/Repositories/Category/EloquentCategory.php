<?php
namespace App\Repositories\Category;

use App\Models\Category;

class EloquentCategory implements CategoryRepository {

    public function __construct()
    {
        $this->category = new \App\Models\Category();
    }

    public function getAllCategory() {

    }

    public function findCategorybyId($id) {
        $category = $this->category->find($id) ;
        if($category) {
            return $category;
        }
        return false;

    }


    public function UpdateCategory($id, $array) {

    }

    public function DeleteCategory($id, $array) {

    }

    public function getCategoryWhere( $where , $take , $order_by ) {

    }

    public function getCategoryPaginate ($array , $paginate ) {

    }

    public function InsertCategory($array) {

    }

    public function getCategorybyCategory() {

    }
}