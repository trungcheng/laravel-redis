<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CacheReview extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = [] ;
        $review = \App\Models\Article::select('*');
        $review = $review->where('type', 'Review')->where(function ($review) {
            $review->where('status', 'publish');
            $review->Orwhere('published_at', '<=', date('Y-m-d H:i'));
        });
        $review = $review->orderBy('published_at', 'desc')->orderBy('id', 'desc');
        $review = $review->skip(0)->take(8)->get();

        foreach ($review as $item) {
            $data[] = $item->id;
        }
        \Cache::forever('review_home', $data);

        $data = [] ;
        foreach (config('admincp.type_category') as $k => $v) {
            if ($v[1] == 'review') {
                $k == 'food_1' ? $type = 'food' : $type = $k;
                $data[$k] = \App\Models\Category::where('category.status', '1')
                    ->where('category.type', $type)
                    ->where('type_article', $v[1])
                    ->limit(12)
                    ->get();
                \Cache::forever('category_type_' . $k, $data[$k]);
            }
        }
    }
}
