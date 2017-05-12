<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Category;
use App\Models\Question;
use App\Models\Comment;

use App\Cache\CacheRedis;

class QuestionsController extends Controller
{
    function getIndex(Request $request)
    {
        $this->authorize('ViewQuestions');
        $data['request_all'] = $request->all();
        $data['category'] = Category::where('type_article', 'review')->orWhere('type_article', 'recipe')->get();
        if (\Request::has('order_type')) {
            $order_name = 'parent_id';
            $order_type = \Request::get('order_type');
        } elseif (\Request::has('order_id')) {
            $order_name = 'id';
            $order_type = \Request::get('order_id');
        } elseif (\Request::has('order_created')) {
            $order_name = 'created_at';
            $order_type = \Request::get('order_created');
        } else {
            $order_name = 'created_at';
            $order_type = 'desc';
        }
        $questions = Comment::orderBy($order_name, $order_type)->paginate(10);
        $data['questions'] = $questions;
        return view('childs.questions.index')->with($data);
    }

    function postEdit()
    {
        $this->authorize('EditQuestions');
        $input = \Request::except('_token', 'id');
        $id = \Request::get('id');
        $cache = new CacheRedis();
        $comment = Comment::find($id);
        $cache->CacheComment($comment,'remove');
        foreach ($input as $k => $item) {
            $comment->$k = trim($item);
        }
        $comment->save();
        $cache->CacheComment($comment,'create');
        return redirect()->back();
    }

    function postReply()
    {
        $this->authorize('EditQuestions');
        $input = \Request::except('_token', 'id');
        $parent_id = \Request::get('parent_id');
        $comment = new Comment ();
        foreach ($input as $k => $item) {
            $comment->$k = trim($item);
        }
        $comment->save();
        return redirect()->back();
    }

    function getStatus($status, $id)
    {
        $this->authorize('ActiveQuestions');
        $comment = Comment::findOrFail($id);
        $comment->status = $status;
        $comment->save();
        return redirect()->back();
    }

    function getDelete($id)
    {
        $this->authorize('DeleteQuestions');
        $comment = Comment::findOrFail($id);
        $cache = new CacheRedis();
        $cache->CacheComment($comment,'remove');
        $comment->delete();

        return redirect()->back();
    }
}
