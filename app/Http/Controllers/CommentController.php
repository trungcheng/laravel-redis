<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Comment;
use App\Http\Controllers\Controller;

class CommentController extends Controller {

    public function getIndex(Request $request) {
        $this->authorize('ViewComment');
        $user_role = auth()->user()->user_type;
        try {
            $request->flash();

            $start_date = date('Y-m-t 00:00:00', time());
            $start_end = date('Y-m-t 23:59:59', time());

            $start_date = $request->has('start_date') ? $request->old('start_date') : $start_date;
            $end_date = $request->has('end_date') ? $request->old('end_date') : $start_end;

            if (auth()->user()->user_type == 'Admin' || auth()->user()->user_type == 'Editor') {
                $comment = Comment::select('id', 'title', 'content', 'creator', 'article_id', 'ratings', 'created_at', 'status');
            } else {
                $comment = Comment::select('id', 'title', 'content', 'creator', 'article_id', 'ratings', 'created_at', 'status')->where('creator', auth()->user()->email);
            }

            if ($request->has('key')) {
                $keyword = $request->old('key');
                $keyword = preg_replace('/\s\s+/', ' ', trim($keyword));
                $comment = $comment->where('title', 'like', '%' . $keyword . '%');
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $start_date = new \DateTime($start_date);
                $end_date = new \DateTime($end_date);

                $comment = $comment->whereBetween('created_at', array($start_date, $end_date));
            }

            if ($request->has('status')) {
                $comment = $comment->where('status', (int) $request->get('status'));
            } else {
                $comment = $comment->where('status', '!=', 2);
            }

            if ($request->has('creator')) {
                $comment = $comment->where('creator', $request->get('creator'));
            }

            $comment = $comment->orderBy('created_at', 'desc');

            $request_all = $request->all();

            $comment = $comment->paginate(10);

            if (!empty($comment)) {
                return view('childs.comment.index')->with('comments', $comment)->with('request_all', $request_all)->with('role', $user_role);
            } else {
                echo 'no data';
            }
        } catch (Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function postUpdate() {
        $this->authorize('EditComment');
        try {
            $comment_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $title = isset($_POST['title']) ? $_POST['title'] : '';
            $content = isset($_POST['content']) ? $_POST['content'] : '';
            $comment = Comment::find($comment_id);
            $comment->title = $title;
            $comment->content = $content;
            $comment->save();
            return json_encode(['status' => 'success', 'msg' => 'Update successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postShow() {
        $this->authorize('EditComment');
        try {
            $comment_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $comment = Comment::find($comment_id);
            $comment->status = 1;
            $comment->save();
            return json_encode(['status' => 'success', 'msg' => 'Show successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postHide() {
        $this->authorize('EditComment');
        try {
            $comment_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $comment = Comment::find($comment_id);
            $comment->status = 0;
            $comment->save();
            return json_encode(['status' => 'success', 'msg' => 'Hide successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postDelete() {
        $this->authorize('DeleteComment');
        try {
            $comment_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $comment = Comment::find($comment_id);
            $comment->status = 2;
            $comment->save();
            return json_encode(['status' => 'success', 'msg' => 'Delete comment successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

}
