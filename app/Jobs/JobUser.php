<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use App\Models\MetaUser;

class JobUser extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data;
    protected $type;
    protected $id;

    public function __construct($data, $type, $id = null)
    {
        $this->data = $data;
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {
        try {
            if ($this->type == 'insert') {
                $new = new User();
                foreach ($this->data as $k => $v) {
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

                }
                $client = new \Hoa\Websocket\Client(
                    new \Hoa\Socket\Client('ws://42.112.25.22:8889/')
                );
                $client->setHost('42.112.25.22');
                $client->on('open', function (\Hoa\Event\Bucket $bucket) {
                    $bucket->getSource()->send('reload');
                    return;
                });
                $client->connect();
                $client->close();
                return response()->json(['status' => http_response_code(), 'state' => 'CA']);
            } elseif ($this->type == 'update') {
                $data = $this->data;
                $user = User::find($data->data->id);
                if (isset($data->data->name)) {
                    $user->name = $data->data->name;
                } else {
                    $user->password = bcrypt($data->data->password);
                }
                if (isset($data->data->thumbnail)) {
                    $user->thumbnail = $data->data->thumbnail;
                }
                $user->save();
                if (isset($data->meta)) {
                    $meta = $data->meta;
                    $meta_user = MetaUser::where('meta_key', 'like', '%info_%')->where('user_id', $data->data->id)->delete();
                    foreach ($meta as $k => $v) {
                        $meta_user = new MetaUser ();
                        $meta_user->meta_key = $k;
                        $meta_user->meta_value = $v;
                        $meta_user->user_id = $data->data->id;
                        $meta_user->save();
                    }
                }
                $client = new \Hoa\Websocket\Client(
                    new \Hoa\Socket\Client('ws://42.112.25.22:8889/')
                );
                $client->setHost('42.112.25.22');
                $client->on('open', function (\Hoa\Event\Bucket $bucket) {
                    $bucket->getSource()->send('loading'.$this->data->data->id);
                    return;
                });
                $client->connect();
                $client->close();
            } else {

            }
        } catch (\Exception $e) {
            return response()->json(['status' => 404, 'state' => 'CA']);
        }
    }
}
