<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\MetaUser;

class JobFollow extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $user_id ;
    protected $user_id_follow;
    protected $key;

    public function __construct($key, $user_id_follow, $user_id)
    {
        $this->key = $key;
        $this->user_id_follow = $user_id_follow;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $key = $this->key;
        $user_id_follow = $this->user_id_follow;
        $user_id = $this->user_id;
        if (MetaUser::where('meta_key', $key)->where('user_id', $user_id)->where('meta_value', $user_id_follow)->count() > 0) {
            MetaUser::where('meta_key', $key)->where('user_id', $user_id)->where('meta_value', $user_id_follow)->delete();
            return response()->json(['status' => http_response_code()]);
        } else {
            $meta_fe = new MetaUser();
            $meta_fe->meta_key = $key;
            $meta_fe->meta_value = $user_id_follow;
            $meta_fe->user_id = $user_id;
            $meta_fe->save();
            return response()->json(['status' => http_response_code()]);
        }
    }
}
