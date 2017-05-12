<?php
namespace App\Repositories\Category;

interface CategoryRepository {

    public function getAllCategory();

    public function findCategorybyId($id);

    public function UpdateCategory($id, $array);

    public function DeleteCategory($id, $array);

    public function getCategoryWhere( $where , $take , $order_by );

    public function getCategoryPaginate ($array , $paginate ) ;

    public function InsertCategory($array) ;

    public function getCategorybyCategory() ;
}