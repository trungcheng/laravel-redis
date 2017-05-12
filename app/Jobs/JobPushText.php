<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class JobPushText extends Job implements ShouldQueue {

    use InteractsWithQueue,
        SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $devices;
    protected $title;
    protected $content;
    protected $push_type;
    protected $article_id;

    public function __construct($devices, $title, $content, $push_type, $article_id) {
        $this->title = $title;
        $this->content = $content;
        $this->push_type = $push_type;
        $this->article_id = $article_id;
        $this->devices = $devices;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        try {
            $notif = array(
                'title' => $this->title,
                'text' => $this->content,
                'sound' => 'default',
                'badge' => '1'
            );
            $data = array(
                'title' => $this->title,
                'action' => 'VIEW_RECIPE',
                'id' => $this->article_id
            );
            $post = array(
                'notification' => $notif,
                'registration_ids' => $this->devices,
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

//    }
}
