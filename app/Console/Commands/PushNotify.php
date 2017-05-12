<?php

namespace App\Console\Commands;

use App\Models\JobPush;
use App\Models\User;
use App\Models\Device;
use Illuminate\Console\Command;

class PushNotify extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pushnotify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto push notify';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $arrUser = [];
        $jobpush = JobPush::where('date_push', '<=', date('Y-m-d H:i'))->where('status', 1)->first();
        if (!empty($jobpush) && $jobpush != null) {
            $id = $jobpush->id;
            $title = $jobpush->title;
            $content = $jobpush->content;
            $push_type = $jobpush->push_type;
            $article_id = $jobpush->article_id;
            $devicesname = $jobpush->devicesname;

            if ($jobpush->type == 'items') {
                $arrUser = array_filter(explode(',', $jobpush->users_id));
                $jobpush->status = 0;
                $jobpush->save();
            } else {
                $users = User::join('devices', 'users.id', '=', 'devices.user_id')
                        ->select('users.id', 'users.name', 'users.email', 'users.thumbnail', 'users.user_type')
                        ->where('users.flag', '!=', $id);
                if (!empty($jobpush->name)) {
                    $users->where('users.name', 'LIKE', "%$jobpush->name%");
                }
                $users = $users->orderBy('id', 'desc')->groupBy('users.id')->paginate(10000);

                if (!empty($users) && $users != null) {
                    foreach ($users as $user) {
                        $arrUser[] = (int) $user->id;
                        $user->flag = $jobpush->id;
                        $user->save();
                    }
                } else {
                    $jobpush->status = 0;
                    $jobpush->save();
                }
            }

            $data_devices = $this->getDevices($arrUser, $devicesname, $jobpush);
            $this->processPushGCM($data_devices, $title, $content, $push_type, $article_id);
        } else {
            echo 'Không có thông báo !';
        }
    }

    function getDevices($arrUser, $devicesname, $jobpush) {
        try {
            $data_devices = [];
            if (!empty($arrUser)) {
                $devices = Device::select('token', 'user_id', 'devicename', 'os')->whereIn('user_id', $arrUser);

                if ($devicesname == 'android') {
                    $devices = $devices->where('os', 'Android')->where('active', 1)->get();
                } else if ($devicesname == 'ios') {
                    $devices = $devices->where('os', '!=', 'Android')->where('active', 1)->get();
                } else {
                    $devices = $devices->where('active', 1)->get();
                }

                if (!empty($devices) && $devices != null) {
                    foreach ($devices as $device) {
                        if (isset($device->token) && $device->token != null) {
                            $data_devices[] = $device->token;
                        }
                    }
                }
            }
            return $data_devices;
        } catch (Exception $ex) {
            echo $ex->getMessage();
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
            echo json_encode($result);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

}
