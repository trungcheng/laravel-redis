<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MetaArticleFe;
use Illuminate\Http\Request;
use App\Models\Ward;
use App\Http\Controllers\Controller;
use App\Cache\CacheRedis;

class CategoryController extends Controller
{

    public function __construct(Request $request)
    {
        $this->_request = $request;
    }

    public function getIndex()
    {
//        $like = MetaArticleFe::find(7) ;
//        $cache = new CacheRedis() ;
//        $cache->CacheUserFavorite($like);
//        $this->authorize('ReadCategory');

        if (in_array('Admin', config('permission.ViewArticle'))) {
            $parentCats = Category::select('id', 'title', 'slug', 'status', 'created_at')
                ->where('category.type', '!=', 'tag')
                ->orderBy('created_at' , 'desc')
                ->paginate(10);
            if (!empty($parentCats)) {
                return view('childs.category.index')
                    ->with('categories', $parentCats);
            } else {
                echo "No data";
            }
        }
    }

    public function getStreet()
    {
        $this->authorize('AddStreet');
        $wards = Ward::select('wardid', 'name', 'districtid', 'created_at')->paginate(20);

        return view('childs.category.street')->with('wards', $wards);
    }

    public function postStreet()
    {
        $this->authorize('SaveStreet');
        try {
            $data = \Input::only('name', 'district');
            $name = strip_tags($data['name']);
            $district = strip_tags($data['district']);
            $date = date('Y-m-d H:i:s');

            $ward = new Ward();
            $ward->name = $name;
            $ward->type = 'Đường';
            $ward->districtid = $district;
            $ward->created_at = $date;
            $ward->updated_at = $date;

            $ward->save();
            return json_encode(['status' => 'success', 'msg' => 'Post Successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function getCreate()
    {
        $this->authorize('SaveCategory');
        $parentCats = Category::where('type_article', '=', 'recipe')
            ->Orwhere('type_article', '=', 'review')
            ->Orwhere('type_article', '=', 'blog')
            ->OrWhere('type_article', '=', 'tin_tuc')
            ->get();
        return view('childs.category.create')->with('parentCats', $parentCats);
    }

    public function postCreate()
    {
        $this->authorize('SaveCategory');
        try {
            $data = \Input::only('title', 'type' ,'parent_id');
            $category = new Category();
            foreach ($data as $k => $v) {
                if ($v == 'food_1' || $v == 'food_2') {
                    $category->$k = 'food';
                } else {
                    $category->$k = $v;
                }
            }
            $category->parent_id = $data['parent_id'] ;
            $category->type_article = config('admincp.type_category')[$data['type']][1];
            $category->status = 1;
            $category->slug = str_slug($category['title'], '-');
            $category->save();

            $cache = new CacheRedis();
            $cache->CacheCateTags($category);

            return json_encode(['status' => 'success', 'msg' => 'Lưu chuyên mục thành công']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

//    public function getEdit($id) {
//        $this->authorize('SaveCategory');
//        $parentCats = Category::select('id', 'title')
//            ->where('category.type', 'category')
//            ->where('category.parent_id', 0)
//            ->get();
//
//        $category = Category::find($id);
//        return view('childs.category.edit')
//            ->with('category', $category)
//            ->with('parentCats', $parentCats);
//    }
//
//    public function postEdit($id) {
//        $this->authorize('SaveCategory');
//        try {
//            $data = \Input::only('title', 'slug', 'parent_id', 'status');
//            $category = Category::find($id);
//            foreach ($data as $k => $v) {
//                $category->$k = $v;
//            }
//            $category->type = 'category';
//
//            if(trim($category['slug']) == '') {
//                $category->slug = str_slug($category['title'], '-');
//            }
//            $category->save();
//
//            return json_encode(['status' => 'success', 'msg' => 'Post Successfully']);
//        } catch (\Exception $e) {
//            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
//        }
//    }

    public function postUpdateStatus(Request $request)
    {
        $this->authorize('SaveCategory');
        try {
            $id = $request->get('id');
            $category = Category::find($id);
            if ($category->status == 1) {
                $category->status = 0;
                $data['name'] = 'Inactive';
            } else {
                $category->status = 1;
                $data['name'] = 'Active';
            }
            $data['status'] = $category->status;

            $category->save();

            // update lại trạng thái category
            $cache = new CacheRedis();
            $cache->CacheCateTags($category);

            return json_encode(['status' => 'success', 'msg' => 'Post Successfully', 'data' => $data]);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postDelete(Request $request)
    {
        $this->authorize('SaveCategory');
        if (!$request->has('id')) {
            abort(404);
        }
        $id = $request->get('id');
        $category = Category::findOrFail($id);
        $parent_id = $category['parent_id'];

        $result = array(
            'status' => 'error',
            'msg' => trans('category.del_cat_fail')
        );
        try {
            if ($parent_id == 0) {
                //thay doi tat ca cac sub cate co parent_id = id -> 0
                $subCats = Category::select('id', 'parent_id')
                    ->where('category.status', 1)
                    ->where('category.type', 'category')
                    ->where('category.parent_id', $id)->get();
                if (!empty($subCats)) {
                    foreach ($subCats as $item) {
                        $cat = Category::find($item['id']);
                        $cat->parent_id = 0;
                        $cat->save();

                        // update lại trạng thái category
                        $cache = new CacheRedis();
                        $cache->CacheCateTags($cat);
                    }
                }
            }
            if ($category->delete()) {
                $result = array(
                    'status' => 'success',
                    'msg' => trans('category.del_cat_success')
                );
            }

            //xóa key redis khi xóa chuyên mục
            $cache = new CacheRedis();
            $cache->RemoveKey(env('REDIS_PREFIX') . '_category_' . $id);

            return json_encode($result);
        } catch (\Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function postDeleteWard(Request $request)
    {
        $this->authorize('DeleteStreet');
        if (!$request->has('id')) {
            abort(404);
        }
        $id = (int)$request->get('id');
        try {
            if (Ward::where('wardid', $id)->delete()) {
                $result = array(
                    'status' => 'success',
                    'msg' => trans('ward.del_ward_success')
                );
            }
            return json_encode($result);
        } catch (\Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

}
