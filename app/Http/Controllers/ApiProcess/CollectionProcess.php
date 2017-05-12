<?php
namespace App\Http\Controllers\ApiProcess;

use App\Models\User;
use App\Models\Article;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Datatables;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\MetaCollection;

class CollectionProcess extends Controller
{
    function anyCreate()
    {
        $input = \Input::all();
        $data = \Input::except('function');
        $collection = new Collection();
        $data = json_decode($input['data']);
        foreach (get_object_vars(json_decode($data->data)) as $k => $v) {
            $collection->$k = $v;
        }
        $collection->creator = $data->user;
        if ($collection->save()) {
            return response()->json(['status' => http_response_code() , 'collection_id' => $collection->id ]);
        } else {
            return response()->json(['status' => 404]);
        }
    }

    function postUpdateCollection()
    {
        $input = \Input::all();
        $data = \Input::except('function');
        $data = json_decode($input['data']);
        $obj = json_decode($data->data);
        $update_data = get_object_vars(json_decode($data->data));
        $collection = Collection::find($obj->collection_id);
        foreach ($update_data as $k => $v) {
            if ($k != 'collection_id') $collection->$k = $v;
        }
        if ($collection->save()) {
            return response()->json(['status' => http_response_code()]);
        } else {
            return response()->json(['status' => 404]);
        }
    }

    function anySub()
    {
        $collections = json_decode(\Request::get('data'));
        $collections_array = (json_decode($collections)->collections);
        $article_id = json_decode($collections)->article_id;
        $creator = json_decode($collections)->creator;
        if (!empty($collections_array)) {
            $collection_all = json_decode($collections_array);
            if (!empty($collection_all[0])) {
                foreach ($collection_all[0] as $items) :
                    MetaCollection::where('meta_key', 'article_of_collection')
                        ->where('meta_value', $article_id)
                        ->where('collection_id', $items)
                        ->delete();
                endforeach;
                foreach ($collection_all[0] as $items) {
                    $new = new MetaCollection();
                    $new->meta_key = 'article_of_collection';
                    $new->meta_value = $article_id;
                    $new->collection_id = $items;
                    $new->save();
                }
                return response()->json(['status' => http_response_code()]);
            };
        }

    }

    function postSaveCollection()
    {
        $data = json_decode(\Request::get('data'));
        if (isset($data->user) && $data->data->collection_id) {
            $user = $data->user;
            $collection_id = $data->data->collection_id;
            $check = Collection::where('id', $collection_id)->where('co-own', 'like', '%' . $user . '%')->count();
            if ($check >= 1) {
                return response()->json(['status' => '200', 'status_collection' => 'have-collection']);
            } else {
                $collection = Collection::find($collection_id);
                $obj = 'co-own';
                $collection->$obj = $collection->$obj . ',' . $user;
                $collection->save();
                return response()->json(['status' => '200', 'status_collection' => 'save-collection']);
            }

        } else {
            return response()->json(['status' => '404']);
        }

    }

    function postDeleteCollection()
    {
        $data = json_decode(\Request::get('data'));
        if (isset($data->user) && isset($data->data->collection_id)) {
            $collection = Collection::find($data->data->collection_id);
            if ($collection->creator == $data->user) {
                $collection->delete();
            } else {
                $own = 'co-own';
                $collection->$own = str_replace($data->user, '', $collection->$own);
                $collection->save();
            }
            return response()->json(['status' => '200']);
        } else {
            return response()->json(['status' => '404']);
        }

    }

    function postDeleteArticle()
    {
        $data = @json_decode(\Request::get('data'));
        $data = $data->data;
        if (isset($data->article_id) && isset($data->collection_id)) {
            if (MetaCollection::where('collection_id', $data->collection_id)
                ->where('meta_value', $data->article_id)
                ->delete()
            ) {
                return response()->json(['status' => '200']);
            } else {
                return response()->json(['status' => '404']);
            }

        } else {
            return response()->json(['status' => '404']);
        }

    }


}