<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Article;
use Illuminate\Http\Request;
use Datatables;
use App\Http\Controllers\Controller;

class UserController extends Controller {

    public function getQuanTriVien() {
        $this->authorize('ReadUser');

        return view('childs.user.index');
    }

     public function getThanhVien() {
        if (!\Request::has('user_fe')) {
            return redirect(url('/thanh-vien?user_fe=true'));
        }
        $this->authorize('ReadUser');

        return view('childs.user.index');
    }

    public  function getUserData() {
        $this->authorize('ReadUser');
        if (\Request::get('user_fe') == '') {
            $users = User::where('user_type', 'not like', 'User_%')->get();
        } else {
            $users = User::where('user_type', 'like', 'User_%')->get();
        }
        return Datatables::of($users)
                        ->addColumn('avatar', function ($user) {
                            return '<img class="img-circle" alt="User Image" src="' . get_gravatar($user->email) . '"/>';
                        })
                        ->addColumn('facebook', function ($user) {
                            $user_id = str_replace('@facebook.vn', '', $user->email);
                            if (str_contains($user->email, '@facebook.vn') == true) {
                                return '<a href="https://www.facebook.com/app_scoped_user_id/' . $user_id . '">' . $user->name . '</a>';
                            } else {
                                return '';
                            }
                        })
                        ->addColumn('action', function ($user) {
                            if ($user->email == auth()->user()->email) {
                                return '';
                            }
                            $action_btn = '<div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            ' . trans('user.action') . ' <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span></button>
                            <ul class="dropdown-menu" role="menu">';
                            $action_btn .= '<li><a onclick="editUser(this);" href="#edit-user" data-uid="' . $user->id . '"><i class="fa fa-edit"></i>' . trans('user.edit_user') . '</a></li>';
                            if ($user->status == 'Active') {
                                $action_btn .= '<li><a onclick="updateStatusUser(this);" href="#lock-user" data-status="Inactive" data-uid="' . $user->id . '"><i class="fa fa-lock"></i>' . trans('user.lock_user') . '</a></li>';
                            } else {
                                $action_btn .= '<li><a onclick="updateStatusUser(this);" href="#unlock-user" data-status="Active" data-uid="' . $user->id . '"><i class="fa fa-unlock"></i>' . trans('user.unlock_user') . '</a></li>';
                            }
                            $action_btn .= '<li class="divider"></li><li><a onclick="deleteUser(this);" href="#delete-user" data-uid="' . $user->id . '"><i class="fa fa-remove"></i>' . trans('user.delete_user') . '</a></li></ul></div>';
                            return $action_btn;
                        })
                        ->make(true);
    }

    public function getProfile() {
        try {
            $article = array();
            $user = auth()->user();

            foreach ($user->articleFollow as $value) {
                $article[] = Article::find((int) $value->meta_value);
            }
            foreach ($user->articleLike as $value) {
                $article[] = Article::find((int) $value->meta_value);
            }

            $article = array_unique($article);
            $article = array_slice($article, 0, 10);
            return view('childs.user.profile')->with('articles', $article);
        } catch (Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function postProfile(Request $request) {
        try {
            $status = 'error';
            $msg = trans('user.update_profile_fail');
            $pass = auth()->user()->password;


            $user = auth()->user();

            if ($request->has('name')) {
                $user->name = strip_tags($request->get('name'));
            }

            if ($request->has('email')) {
                $user->email = strip_tags($request->get('email'));
            }
            if ($request->has('thumbnail')) {
                $user->thumbnail = strip_tags($request->get('thumbnail'));
            }

            if ($request->has('description')) {
                $user->description = strip_tags($request->get('description'));
            }

            if ($request->has('pass')) {
                $user->password = bcrypt(strip_tags($request->get('pass')));
            }
            if ($user->save()) {
                $status = 'success';
                $msg = trans('user.update_profile_success');

                //cache user
                $cache = new \App\Cache\CacheRedis();
                $cache->CacheUser($user);

                return json_encode(['status' => $status, 'msg' => $msg]);
            }
        } catch (\Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function anyUpdateStatus(Request $request) {
        $this->authorize('ActiveUser');

        if (!$request->has('id') || !$request->has('st')) {
            abort(404);
        }

        $editedUser = User::findOrFail($request->get('id'));

        $status = 'error';
        $msg = trans('user.update_status_fail');
        try {
            $editedUser->status = strip_tags($request->get('st'));
            if ($editedUser == 'Normal') {
                $editedUser->user_type = 'Booster';
            }
            if ($editedUser->save()) {
                $status = 'success';
                $msg = trans('user.update_status_success');
            }

            //cache user
            $cache = new \App\Cache\CacheRedis();
            $cache->CacheUser($editedUser);

            return json_encode(['status' => $status, 'msg' => $msg]);
        } catch (\Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function postDelete(Request $request) {
        $this->authorize('DeleteUser');

        if (!$request->has('id')) {
            abort(404);
        }
        $deletedUser = User::findOrFail($request->get('id'));

        $status = 'error';
        $msg = trans('user.delete_fail');
        try {
            if ($deletedUser->delete()) {
                $status = 'success';
                $msg = trans('user.delete_success');
            }

            //cache user
            $cache = new \App\Cache\CacheRedis();
            $cache->RemoveKey(env('REDIS_PREFIX') . '_user_' . $request->get('id'));

            return json_encode(['status' => $status, 'msg' => $msg]);
        } catch (\Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function getEdit($user_id) {
        $this->authorize('EditUser');

        $user = User::findOrFail($user_id);

        return view('childs.user.edit')->with(['user' => $user]);
    }

    public function postEdit(Request $request) {
        $this->authorize('EditUser');

        if (!$request->has('uid') || !$request->has('name') || !$request->has('email')) {
            abort(500);
        }

        $user = User::find($request->get('uid'));

        $status = 'error';
        $msg = trans('user.edit_fail');
        try {
            $user->name = strip_tags($request->get('name'));
            $user->email = strip_tags($request->get('email'));
            $user->user_type = strip_tags($request->get('user_type'));
            $user->description = strip_tags($request->get('description'));
            if ($request->get('password') !== '') {
                $user->password = bcrypt(strip_tags($request->get('password')));
            }
            if ($user->save()) {
                $status = 'success';
                $msg = trans('user.edit_success');
            }

            //cache user
            $cache = new \App\Cache\CacheRedis();
            $cache->CacheUser($user->id);

            return json_encode(['status' => $status, 'msg' => $msg]);
        } catch (\Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function postCreate(Request $request) {
        $this->authorize('CreateUser');

        if (!$request->has('name') || !$request->has('email') || !$request->has('password')) {
            abort(500);
        }
        $status = 'error';
        $msg = trans('user.create_fail');
        try {
            $user = new User;
            $user->name = strip_tags($request->get('name'));
            $user->email = strip_tags($request->get('email'));
            $user->user_name = str_slug(strip_tags($request->get('name')));
            $user->user_type = strip_tags($request->get('user_type'));
            $user->description = strip_tags($request->get('description'));
            $user->password = bcrypt(strip_tags($request->get('password')));
            $user->status = 'Active';
            if ($user->save()) {
                $status = 'success';
                $msg = trans('user.create_success');
            }

            //cache user
            $cache = new \App\Cache\CacheRedis();
            $cache->CacheUser($user);

            return json_encode(['status' => $status, 'msg' => $msg]);
        } catch (\Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    function getEndPoint($username, $password) {
        $credentials = [
            'email' => $username,
            'password' => $password,
        ];

        if (\Auth::once($credentials)) {
            return \Auth::user()->id;
        } else {
            return false;
        }
    }

    function getClassRegister(Request $request) {
        $this->authorize('ClassRegister');
        try {
            $request->flash();
            $users = User::join('meta_user', 'users.id', '=', 'meta_user.user_id')
                    ->select('users.id', 'users.name', 'users.email', 'users.thumbnail', 'users.status', 'users.user_type', 'meta_user.meta_value')
                    ->where('meta_user.meta_key', '=', 'data_register');

            if ($request->has('key')) {
                $keyword = $request->get('key');
                $keyword = preg_replace('/\s\s+/', ' ', trim($keyword));
                $users = $users->whereRaw("users.name LIKE '%$keyword%'");
            }
//
            if ($request->has('status')) {
                $status = strip_tags($request->get('status'));
                $users = $users->where('users.status', $status);
            }
//            
            if ($request->has('user_type')) {
                $type = strip_tags($request->get('user_type'));
                $users = $users->where('users.user_type', $type);
            }
//          
            $users = $users->orderBy('meta_user.created_at', 'desc');
            $request_all = $request->all();
            $users = $users->paginate(10);

            if (!empty($users)) {
                return view('childs.user.class_register')->with('users', $users)->with('request_all', $request_all);
            } else {
                echo 'no data';
            }
        } catch (Exception $ex) {
            
        }
    }

}
