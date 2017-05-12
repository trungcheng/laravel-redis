<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\MetaArticle;
use App\Models\MetaUser;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use App\Models\User;
use App\Models\Comment;
use App\Models\Collection;
use App\Models\MetaCollection;
class ApiController extends Controller
{
    function store()
    {
        $input = \Input::all();
        $data = \Input::except('function');
        if ($input['function'] === 'insert_rating') {
            $number_rate = $data['rate'];
            $article_id = $data['article_id'];
            $key = 'rate_article';

            //------------------ADD JOB INSERT RATE-------------------------------------//
            $job = (new \App\Jobs\JobRate($key, $article_id, $number_rate))->onConnection('Rate');
            $this->dispatch($job);
            return response()->json(['status' => http_response_code()]);

        } elseif ($input['function'] === 'insert_follow') {
            $user_id = \Auth::guard('api')->user()->id;
            $user_id_follow = $data['user_id_follow'];
            $key = 'follow_user';

            //------------------ADD JOB INSERT FOLLOW-------------------------------------//

            $job = (new \App\Jobs\JobFollow($key, $user_id_follow, $user_id));
            $this->dispatch($job);
            return response()->json(['status' => http_response_code()]);


        } elseif ($input['function'] === 'insert_like') {
            $user_id = \Auth::guard('api')->user()->id;
            $article_id = $data['article_id'];
            $key = 'like_article';

            //------------------ADD JOB INSERT LIKE-------------------------------------//

            $job = (new \App\Jobs\JobLike($key, $article_id, $user_id))->onConnection('Like');
            $this->dispatch($job);
            return response()->json(['status' => http_response_code()]);

        } elseif ($input['function'] === 'insert_user') {
            $data = json_decode($data['data']);
            $data = (get_object_vars(json_decode($data->data)));
            if (!in_array($data['user_type'], config('admincp.user_type_check'))) {

                //------ADD JOB INSERT USER-----------------------////
                $new = new User();
                foreach ($data as $k => $v) {
                    if ($v == 'Admin' || $v == 'Editor' || $v == 'Normal') {
                        return response()->json(['status' => 403, 'state' => 'CA']);
                    }
                    if ($k != 'password') {
                        $new->$k = $v;
                    } else {
                        $new->$k = $v;
                    }
                }
                $new->api_token = str_random(10);
                if ($new->save()) {
                    return response()->json(['status' => http_response_code() , 'id' => $new->id ]);
                } else {
                    return response()->json(['status' => 404]);
                }
            } else {
                return response()->json(['status' => 403]);
            }
        } elseif ($input['function'] === 'insert_recipe') {

        } elseif ($input['function'] === 'insert_view') {
            $article_id = $data['article_id'];
            $key = 'view_article';
            //------------------ADD JOB INSERT VIEW-------------------------------------//
            $job = (new \App\Jobs\JobView($key, $article_id))->onConnection('View');
            $this->dispatch($job);
            return response()->json(['status' => http_response_code()]);
        } elseif ($input['function'] === 'create-comment') {
            //---------------------------INSERT COMMENT ---------------------------------//
            $job = (new \App\Jobs\JobComment($input['data'], \Auth::guard('api')->user()->email))->onConnection('InsertComment');
            $this->dispatch($job);
            return response()->json(['status' => http_response_code()]);
        } elseif ($input['function'] == 'create_collection') {
            //---------------------------INSERT COLLECTION ---------------------------------//
            $collection = new Collection();
            $data = json_decode($input['data']);
            foreach (get_object_vars(json_decode($data->data)) as $k => $v) {
                $collection->$k = $v;
            }

            $collection->creator = $data->user ;
            if ($collection->save()) {
                return response()->json(['status' => http_response_code()]);
            } else {
                return response()->json(['status' => 404]);
            }
        } elseif ($input['function'] == 'insert_relation_collect') {
            //---------------------------Relation COLLECTION ---------------------------------//
            $job = (new \App\Jobs\JobRelaCollection($input['data'], \Auth::guard('api')->user()->email))->onConnection('UpdateCollection');
            $this->dispatch($job);
            return response()->json(['status' => http_response_code()]);
        } elseif ($input['function'] === 'update_user') {
            $data = json_decode($input['data']);
            if (\Auth::guard('api')->user()->id == $data->data->id) {
                $job = (new \App\Jobs\JobUser($data, 'update', $data->data->id))->onConnection('UpdateUser');;
                $this->dispatch($job);
            } else {
                return response()->json(['status' => 403]);
            }
            return response()->json(['status' => http_response_code()]);
        } elseif ($input['function'] === 'create-recipe') {
            //---------------------------CREATE RECIPE ---------------------------------//
            $data_article = json_decode($input['data'])->data_article;
            $meta_data = json_decode($input['data'])->meta_data;
            $job = (new \App\Jobs\JobRecipe($data_article, $meta_data, \Auth::guard('api')->user()->email))->onConnection('Recipe');;
            $this->dispatch($job);
            return response()->json(['status' => http_response_code()]);
        } elseif ($input['function'] === 'save_collection') {
            //---------------------------SAVE COLLECTION ---------------------------------//
            $collection_id = json_decode($input['data'])->collection_id;
            $job = (new \App\Jobs\JobSaveCollection($collection_id, \Auth::guard('api')->user()->email))->onConnection('UpdateCollection');;
            $this->dispatch($job);
            return response()->json(['status' => http_response_code()]);
        } elseif ($input['function'] === 'delete_collection') {
            //---------------------------CREATE RECIPE ---------------------------------//
            $collection_id = json_decode($input['data'])->collection_id;
            $job = (new \App\Jobs\JobDeleteCollection($collection_id, \Auth::guard('api')->user()->email))->onConnection('DeleteCollection');;
            $this->dispatch($job);
            return response()->json(['status' => http_response_code()]);
        }

    }

    function show()
    {

    }

    function update($id)
    {

        $input = \Input::all();
        $data = \Input::except('function');
        if ($input['function'] === 'update_rating') {

        } elseif ($input['function'] === 'update_user') {
            $this->authorize('UpdateUser');
            $this->authorize('UserofUser', User::find($id));
            if (isset($data['user_type']) && in_array($data['user_type'], config('admincp.user_type'))) {
                return response()->json(['status' => 403]);
            }
            $job = (new \App\Jobs\JobUser($data, 'update', $id))->delay(1);
            $this->dispatch($job);
            return response()->json(['status' => http_response_code()]);
        } elseif ($input['function'] === 'update_recipe') {

        } else {

        }
    }

    function destroy()
    {

    }

}
