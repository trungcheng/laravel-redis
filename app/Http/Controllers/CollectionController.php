<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\MetaCollection;

class CollectionController extends Controller
{

    public function getIndex(Request $request)
    {
        $this->authorize('ViewCollection');
        $user_role = auth()->user()->user_type;
        try {
            $request->flash();

            $start_date = date('Y-m-t 00:00:00', time());
            $start_end = date('Y-m-t 23:59:59', time());

            $start_date = $request->has('start_date') ? $request->old('start_date') : $start_date;
            $end_date = $request->has('end_date') ? $request->old('end_date') : $start_end;

            if (auth()->user()->user_type == 'Admin' || auth()->user()->user_type == 'Editor') {
                $collection = Collection::select('id', 'title', 'description', 'creator', 'type', 'created_at', 'updated_at' , 'status');
            } else {
                $collection = Collection::select('id', 'title', 'description', 'creator', 'type', 'created_at', 'updated_at' , 'status')->where('creator', auth()->user()->email);
            }

            if ($request->has('key')) {
                $keyword = $request->old('key');
                $keyword = preg_replace('/\s\s+/', ' ', trim($keyword));
                $collection = $collection->where('title', 'like', '%' . $keyword . '%');
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $start_date = new \DateTime($start_date);
                $end_date = new \DateTime($end_date);

                $collection = $collection->whereBetween('created_at', array($start_date, $end_date));
            }


            if(\Request::has('order_by_type')) {
                $collection = $collection->orderBy('type', \Request::get('order_by_type') );
            }
            $collection = $collection->orderBy('id', 'desc');
            $request_all = $request->all();

            $collection = $collection->paginate(10);

            if (!empty($collection)) {
                return view('childs.collection.index')->with('collections', $collection)->with('request_all', $request_all)->with('role', $user_role);
            } else {
                echo 'no data';
            }
        } catch (Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function postUpdate()
    {
        $this->authorize('EditArticle');
        try {
            $collection_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $title = isset($_POST['title']) ? $_POST['title'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $collection = Collection::find($collection_id);
            $collection->title = $title;
            $collection->description = $description;
            $collection->updated_at = date('Y-m-d H:i:s');
            $collection->save();
            CacheCollectionHome('Recipe') ;
            CacheCollectionHome('Review') ;
            return json_encode(['status' => 'success', 'msg' => 'Update successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postDelete()
    {
        $this->authorize('DeleteCollection');
        try {
            $collection_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $collection = Collection::find($collection_id);
            $collection->delete();
            CacheCollectionHome('Recipe') ;
            CacheCollectionHome('Review') ;
            return json_encode(['status' => 'success', 'msg' => 'Delete comment successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function getDelete($id = null)
    {
        $this->authorize('DeleteCollection');
        try {
            $collection_id = $id;
            $collection = Collection::find($collection_id);
            $collection->delete();
            return redirect('/collection');
        } catch (\Exception $e) {
            return redirect('/collection');
        }
    }

    function getUpdateStatus($collection_id = null, $status = null)
    {
        $this->authorize('EditArticle');
        $collection = Collection::find($collection_id);
        $collection->status = $status;
        $collection->save();
        CacheCollectionHome('Recipe') ;
        CacheCollectionHome('Review') ;
        return redirect()->back();
    }

    function getDetail($id = null)
    {
        $this->authorize('EditArticle');
        $id = \Request::segment(3);
        $collection = Collection::find($id);
        $data_recipe = array();
        $article = Article::join('meta_collection', 'article.id', '=', 'meta_collection.meta_value')
            ->join('collection', 'meta_collection.collection_id', '=', 'collection.id')
            ->select('article.*', 'collection.creator', 'meta_collection.collection_id')
            ->where('meta_collection.collection_id', $collection->id)
            ->get();
        $data['article'] = $article;
        $data['collection'] = $collection;
        $search = \Request::get('key');
        if ($search != '') {
            $articles = Article::where('title', 'like', '%' . $search . '%')->paginate(20);
            $data['articles_search'] = $articles;
        }
        return view('childs.collection.detail')->with($data);
    }

    function getAddArticle($colllection_id = null, $article_id = null)
    {
        $this->authorize('EditArticle');
        $collection_first = Collection::find($colllection_id);
        $creator_user = $collection_first->creator;
        $collections_of_user = Collection::where('creator', $creator_user)->get();
        MetaCollection::where('meta_key', 'article_of_collection')
            ->where('meta_value', $article_id)
            ->where('collection_id', $colllection_id)
            ->delete();
        $new = new MetaCollection();
        $new->meta_key = 'article_of_collection';
        $new->meta_value = $article_id;
        $new->collection_id = $colllection_id;
        $new->save();
        CacheCollectionHome('Recipe') ;
        CacheCollectionHome('Review') ;
        return redirect()->back();
    }

    function getDeleteArticle($colllection_id = null, $article_id = null)
    {
        MetaCollection::where('meta_key', 'article_of_collection')
            ->where('meta_value', $article_id)
            ->where('collection_id', $colllection_id)
            ->delete();
        CacheCollectionHome('Recipe') ;
        CacheCollectionHome('Review') ;
        return redirect()->back();
    }

}
