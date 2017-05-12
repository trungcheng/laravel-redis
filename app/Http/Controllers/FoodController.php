<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FoodController extends Controller
{
    public function getIndex() {
        $this->authorize('ReadCategory');

        if(in_array('Admin' ,config('permission.ViewArticle') ) ) {
            $parentCats = Category::select('id', 'title', 'slug', 'status','created_at')
                ->where('category.type', 'food')
                ->where('category.parent_id', 0)
                ->get();

            $subCats = Category::select('id', 'title', 'slug', 'parent_id', 'status', 'created_at')
                ->where('category.type', 'food')
                ->where('category.parent_id', '!=', 0)
                ->get();

            if(!empty($parentCats)) {
                $categories = array();
                foreach($parentCats as $item) {
                    $categories[] = $item;
                    $parent_id    = $item['id'];
                    if(!empty($subCats)) {
                        foreach($subCats as $subItem) {
                            if($parent_id == $subItem['parent_id']) {
                                $subItem['title'] = '__ '.$subItem['title'];
                                $categories[] = $subItem;
                            }
                        }
                    }
                }

                return view('childs.food.index')
                    ->with('categories', $categories);
            } else {
                echo "No data";
            }
        }
    }

    public function getCreate()
    {
        $this->authorize('SaveCategory');

        $parentCats = Category::select('id', 'title')
            ->where('category.type', 'food')
            ->where('category.parent_id', 0)
            ->get();

        return view('childs.food.create')->with('parentCats', $parentCats);
    }

    public function postCreate() {
        $this->authorize('SaveCategory');
        try {
            $data = \Input::only('title', 'slug', 'parent_id');
            $category = new Category();
            foreach ($data as $k => $v) {
                $category->$k = $v;
            }
            $category->type = 'food';
            $category->status = 1;
            if(trim($category->slug) == '') {
                $category->slug = str_slug($category['title'], '-');
            }
            $category->save();

            return json_encode(['status' => 'success', 'msg' => 'Post Successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function getEdit($id) {
        $this->authorize('SaveCategory');
        $parentCats = Category::select('id', 'title')
            ->where('category.type', 'food')
            ->where('category.parent_id', 0)
            ->get();

        $category = Category::find($id);
        return view('childs.food.edit')
            ->with('category', $category)
            ->with('parentCats', $parentCats);
    }

    public function postEdit($id) {
        $this->authorize('SaveCategory');
        try {
            $data = \Input::only('title', 'slug', 'parent_id', 'status');
            $category = Category::find($id);
            foreach ($data as $k => $v) {
                $category->$k = $v;
            }
            $category->type = 'food';

            if(trim($category['slug']) == '') {
                $category->slug = str_slug($category['title'], '-');
            }
            $category->save();

            return json_encode(['status' => 'success', 'msg' => 'Post Successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postUpdateStatus(Request $request) {
        $this->authorize('SaveCategory');
        try {
            $id = $request->get('id');
            $category = Category::find($id);
            if($category->status == 1) {
                $category->status = 0;
                $data['name'] =  'Inactive';
            } else {
                $category->status = 1;
                $data['name'] =  'Active';
            }
            $data['status'] = $category->status;

            $category->save();

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
            if($parent_id == 0) {
                //thay doi tat ca cac sub cate co parent_id = id -> 0
                $subCats = Category::select('id', 'parent_id')
                    ->where('category.status', 1)
                    ->where('category.type', 'food')
                    ->where('category.parent_id', $id)->get();
                if(!empty($subCats)) {
                    foreach ($subCats as $item) {
                        $cat = Category::find($item['id']);
                        $cat->parent_id = 0;
                        $cat->save();
                    }
                }
            }
            if ($category->delete()) {
                $result = array(
                    'status' => 'success',
                    'msg' => trans('category.del_cat_success')
                );
            }
            return json_encode($result);
        } catch (\Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }
}
