<?php

namespace App\Http\Controllers\ApiProcess;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Question;

class QuestionProcess extends Controller {

    function anyListQuestion() {
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

    function postDelete() {
        $data = json_decode(\Request::get('data'));
        $question = Question::where('id', $data->question_id)->where('creator', $data->email)->get();
        if (!empty($question)) {
            if ($question->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 400]);
            }
        } else {
            return response()->json(['status' => 400]);
        }
    }

}
