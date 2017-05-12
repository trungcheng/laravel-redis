<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Notify;
use App\Models\JobPush;
use App\Http\Controllers\Controller;

class NotificationController extends Controller {

    public function getIndex(Request $request) {
        try {
            $key = !empty(request()->get('key')) ? preg_replace('/\s\s+/', ' ', trim(request()->get('key'))) : '';
            $name = !empty(request()->get('name')) ? preg_replace('/\s\s+/', ' ', trim(request()->get('name'))) : '';
            $user_type = !empty(request()->get('user_type')) ? preg_replace('/\s\s+/', ' ', trim(request()->get('user_type'))) : '';

            if (!empty($key)) {
                $notifys = Notify::whereRaw("title LIKE '%$key%'")->orderBy('id', 'decs')->take(15)->skip(0)->get();
            } else {
                $notifys = Notify::orderBy('id', 'decs')->take(15)->skip(0)->get();
            }

            $users = User::join('devices', 'users.id', '=', 'devices.user_id')
                    ->select('users.id', 'users.name', 'users.email', 'users.thumbnail', 'users.user_type');
            if ($name != null) {
                $users = $users->whereRaw("users.name LIKE '%$name%'");
            }
            if ($user_type != null) {
                $users = $users->where('users.user_type', $user_type);
            }

            $users = $users->orderBy('users.name')->groupBy('users.id')->paginate(10);

            if (!empty($notifys)) {
                $data = [
                    'notifys' => $notifys,
                    'users' => $users,
                ];
            }
            return view('childs.notify.index')->with($data);
        } catch (\Exception $ex) {
            abort(400);
        }
    }

    function getCreate() {
        try {
            return view('childs.notify.create');
        } catch (\Exception $ex) {
            abort(400);
        }
    }

    function postCreate() {
        try {
            $data = request()->all();
            $title = isset($data['title']) ? strip_tags($data['title']) : '';
            $content = isset($data['content']) ? strip_tags($data['content']) : '';
            $type = isset($data['type']) ? strip_tags($data['type']) : '';
            $time_push = isset($data['time_push']) ? strip_tags($data['time_push']) : '';
            $date = date('Y-m-d H:i:s');
            $author = Auth::user()->email;

            $notify = new Notify();
            $notify->title = $title;
            $notify->content = $content;
            $notify->creator = $author;
            $notify->type = $type;
            $notify->date_push = $time_push;
            $notify->updated_at = $date;
            $notify->created_at = $date;

            if ($notify->save()) {
                echo json_encode(['status' => 200]);
            } else {
                echo json_encode(['status' => 400]);
            }
        } catch (\Exception $ex) {
            abort(400);
        }
    }

    function getEdit($id = null) {
        try {
            $notify = Notify::find($id);
            return view('childs.notify.edit')->with('notify', $notify);
        } catch (\Exception $ex) {
            abort(400);
        }
    }

    function postEdit() {
        try {
            $data = request()->all();
            $id = isset($data['id']) ? strip_tags($data['id']) : '';
            $title = isset($data['title']) ? strip_tags($data['title']) : '';
            $content = isset($data['content']) ? strip_tags($data['content']) : '';
            $type = isset($data['type']) ? strip_tags($data['type']) : '';
            $time_push = isset($data['time_push']) ? strip_tags($data['time_push']) : '';
            $date = date('Y-m-d H:i:s');
            $author = Auth::user()->email;

            $notify = Notify::find($id);
            $notify->title = $title;
            $notify->content = $content;
            $notify->creator = $author;
            $notify->type = $type;
            $notify->date_push = $time_push;
            $notify->updated_at = $date;

            if ($notify->save()) {
                echo json_encode(['status' => 200]);
            } else {
                echo json_encode(['status' => 400]);
            }
        } catch (\Exception $ex) {
            abort(400);
        }
    }

    function postDelete() {
        try {
            $data = request()->all();
            $id = isset($data['id']) ? strip_tags($data['id']) : '';
            if (!empty($id)) {
                $notify = Notify::find($id);
                if ($notify->delete()) {
                    echo json_encode(['status' => 200]);
                } else {
                    echo json_encode(['status' => 400]);
                }
            } else {
                echo json_encode(['status' => 400]);
            }
        } catch (\Exception $ex) {
            abort(400);
        }
    }

    function PushNotify() {
        try {
            $devices = array();
            $data_devices = array();

            $devicesname = request()->has('device') ? strip_tags(request()->get('device')) : '';
            $article_id = request()->has('article_id') ? (int) strip_tags(request()->get('article_id')) : 0;
            $strID = request()->has('id') ? strip_tags(request()->get('id')) : '';
            $push_type = request()->has('push_type') ? strip_tags(request()->get('push_type')) : '';
            $strUsersID = request()->has('users_id') ? strip_tags(request()->get('users_id')) : '';
            $time_push = request()->get('time_push') ? strip_tags(request()->get('time_push')) : '';
            $arrID = array_filter(explode(',', $strID));

            foreach ($arrID as $id) {
                if (strlen($id) > 0) {
                    $notify = Notify::find($id);
                    $title = $notify->title;
                    $content = $notify->content;
                    $this->insertJobPush($devicesname, $title, $content, $article_id, $push_type, $time_push, $strUsersID, '', 'items');
                }
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    function PushNotifyAll() {
        try {
            $devices = array();
            $data_devices = array();

            $name = !empty(request()->get('name')) ? preg_replace('/\s\s+/', ' ', trim(request()->get('name'))) : '';
            $devicesname = request()->has('device') ? strip_tags(request()->get('device')) : '';
            $article_id = request()->has('article_id') ? (int) strip_tags(request()->get('article_id')) : 0;
            $strID = request()->has('id') ? strip_tags(request()->get('id')) : '';
            $push_type = request()->has('push_type') ? strip_tags(request()->get('push_type')) : '';
            $time_push = request()->get('time_push') ? strip_tags(request()->get('time_push')) : '';
            $arrID = array_filter(explode(',', $strID));

            foreach ($arrID as $id) {
                if (strlen($id) > 0) {
                    $notify = Notify::find($id);
                    $title = $notify->title;
                    $content = $notify->content;

                    $this->insertJobPush($devicesname, $title, $content, $article_id, $push_type, $time_push, '', $name, 'all');
                }
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

//    function processPush($title, $content, $push_type, $article_id, $time) {
//        $job = new \App\Jobs\JobPushText($data_devices, $title, $content, $push_type, $article_id);
//        $job->onConnection('processPush')->onQueue('push-notify');
////        $job->delay(Carbon::now()->addSeconds($time));
//        $this->dispatch($job);
//    }

    function insertJobPush($devicesname, $title, $content, $article_id, $push_type, $time_push, $strUsersID, $name, $type) {
        try {
            $jobPush = new JobPush();
            $jobPush->title = $title;
            $jobPush->content = $content;
            $jobPush->article_id = $article_id;
            $jobPush->push_type = $push_type;
            $jobPush->type = $type;
            $jobPush->name = $name;
            $jobPush->devicesname = $devicesname;
            $jobPush->users_id = $strUsersID;
            $jobPush->date_push = $time_push;
            $jobPush->status = 1;
            $jobPush->created_at = strtotime(date('Y/m/d H:i:s'));
            $jobPush->updated_at = strtotime(date('Y/m/d H:i:s'));
            if ($jobPush->save()) {
                echo json_encode(array('status' => 200));
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    function getPushUser() {
        try {
            $clubs = Team::where('type', 'Club')->get();
            $nationals = Team::where('type', 'National')->get();
            $players = Player::get();
//            $devices = Device::where('active', 1)->get();

            $name = !empty(request()->get('key')) ? preg_replace('/\s\s+/', ' ', trim(request()->get('key'))) : '';
            $user_type = !empty(request()->get('user_type')) ? strip_tags(request()->get('user_type')) : '';
            $favorite_player = !empty(request()->get('favorite_player')) ? strip_tags(request()->get('favorite_player')) : '';
            $favorite_club = !empty(request()->get('favorite_club')) ? strip_tags(request()->get('favorite_club')) : '';
            $favorite_national = !empty(request()->get('favorite_national')) ? strip_tags(request()->get('favorite_national')) : '';
            $status = !empty(request()->get('status')) ? strip_tags(request()->get('status')) : '';

            $users = $this->_notify->getUser($name, $user_type, $favorite_player, $favorite_club, $favorite_national, $status);
            if (!empty($users)) {
                $data = [
                    'clubs' => $clubs,
                    'nationals' => $nationals,
                    'players' => $players,
                    'users' => $users
                ];
            }
            $request_all = request()->all();
            return view('notify::user')->with('request_all', $request_all)->with($data);
        } catch (\Exception $ex) {
            abort(400);
        }
    }

    function processPushGCM($devices, $title, $content, $push_type, $article_id) {
        try {
            $notif = array(
                'title' => $title,
                'text' => $content,
                'sound' => 'default',
                'badge' => '1'
            );
            $data = array(
                'title' => $title,
                'action' => $push_type,
                'id' => $article_id
            );
            $post = array(
                'notification' => $notif,
                'registration_ids' => $devices,
                'data' => $data
            );

            $data_string = json_encode($post);

            $ch = curl_init('https://gcm-http.googleapis.com/gcm/send');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: key=AIzaSyB_K2yvrUtTCjVJY_xGnMzgJRucpw4sk_U',
                'Content-Length: ' . strlen($data_string))
            );
            $result = curl_exec($ch);
            echo json_encode($data_string);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

}
