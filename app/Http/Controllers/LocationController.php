<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MetaArticle;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationController extends Controller {

    public function __construct(Request $request) {
        $this->_request = $request;
    }

    public function getIndex() {
        if (in_array('Admin', config('permission.ViewArticle'))) {
            $parentCats = Category::select('id', 'title', 'slug', 'status', 'created_at')
                    ->where('category.type', 'location')
                    ->where('category.parent_id', 0)
                    ->get();

            $subCats = Category::select('id', 'title', 'slug', 'parent_id', 'status', 'created_at')
                    ->where('category.type', 'location')
                    ->where('category.parent_id', '!=', 0)
                    ->get();

            if (!empty($parentCats)) {
                $categories = array();
                foreach ($parentCats as $item) {
                    $categories[] = $item;
                    $parent_id = $item['id'];
                    if (!empty($subCats)) {
                        foreach ($subCats as $subItem) {
                            if ($parent_id == $subItem['parent_id']) {
                                $subItem['title'] = '__ ' . $subItem['title'];
                                $categories[] = $subItem;
                            }
                        }
                    }
                }

                return view('childs.location.index')
                                ->with('categories', $categories);
            } else {
                echo "No data";
            }
        }
    }

    public function getCreate() {
        $this->authorize('SaveCategory');

        $parentCats = Category::select('id', 'title')
                ->where('category.type', 'location')
                ->where('category.parent_id', 0)
                ->get();

        return view('childs.location.create')->with('parentCats', $parentCats);
    }

    public function postCreate() {
        $this->authorize('SaveCategory');
        try {
            $data = \Input::only('title', 'slug', 'parent_id');
            $category = new Category();
            foreach ($data as $k => $v) {
                $category->$k = $v;
            }
            $category->type = 'location';
            $category->status = 1;
            if (trim($category->slug) == '') {
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
        $parentCats = Category::select('id', 'title', 'status')
                ->where('category.type', 'location')
                ->where('category.parent_id', 0)
                ->get();

        $category = Category::find($id);
        return view('childs.location.edit')
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
            $category->type = 'location';

            if (trim($category['slug']) == '') {
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
            if ($category->status == 1) {
                $category->status = 0;
                $data['name'] = 'Inactive';
            } else {
                $category->status = 1;
                $data['name'] = 'Active';
            }
            $data['status'] = $category->status;

            $category->save();

            return json_encode(['status' => 'success', 'msg' => 'Post Successfully', 'data' => $data]);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postDelete(Request $request) {
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
                                ->where('category.type', 'location')
                                ->where('category.parent_id', $id)->get();
                if (!empty($subCats)) {
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

    public function getCoordinates() {
        try {
            $map_lat = '';
            $map_lng = '';

            $data = MetaArticle::join('article', 'meta_article.article_id', '=', 'article.id')
                            ->select('meta_article.article_id', 'meta_article.meta_value')
                            ->where('meta_article.meta_key', 'review_address')
                            ->whereRaw("article.longitude IS NULL AND article.latitude IS NULL AND article.deleted_at IS NULL")
                            ->groupBy('meta_article.article_id')->orderBy('meta_article.article_id', 'asc')
                            ->skip(0)->take(5)->get();

            if (!empty($data)) {
                foreach ($data as $value) {
                    $articleid = $value->article_id;
                    $address = $value->meta_value;

                    if (strlen($address) > 0) {
                        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $address;
                        $url = str_replace(' ', '%20', $url);
                        $content = file_get_contents($url);
                        $coordinates = json_decode($content);

                        if (!empty($coordinates)) {
                            foreach ($coordinates as $results) {
                                if (!empty($results)) {
                                    $map_lat = isset($results[0]->geometry->location->lat) ? $results[0]->geometry->location->lat : '';
                                    $map_lng = isset($results[0]->geometry->location->lng) ? $results[0]->geometry->location->lng : '';
                                    if (strlen($map_lat) > 0 && strlen($map_lng) > 0) {
                                        $this->updateCoordinates($map_lat, $map_lng, $articleid);
                                        break;
                                    }
                                } else {
                                    $arrNotFor[] = array(
                                        'id' => $articleid,
                                        'address' => $address,
                                    );
                                }
                            }
                        }
                    }
                }
                echo 'Update location coordinates successful<br />';
                foreach ($arrNotFor as $key => $value) {
                    echo ($key + 1) . '. ' . $value['id'] . ' - ' . $value['address'] . ' => (not fomat)<br />';
                }
            } else {
                echo 'Data empty';
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function updateCoordinates($map_lat, $map_lng, $articleid) {
        $date = date('Y-m-d H:i:s');

        $update_ar = Article::find($articleid);
        $update_ar->latitude = $map_lat;
        $update_ar->longitude = $map_lng;
        $update_ar->updated_at = $date;

        return $update_ar->save();
    }

}
