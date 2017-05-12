<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CacheBlogs extends Job implements ShouldQueue
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
        $blog = \App\Models\Article::select('id')
            ->where('type', 'Blogs')
            ->where(function ($reviews) {
                $reviews->where('status', 'publish');
                $reviews->Orwhere('published_at', '<=', date('Y-m-d H:i'));
            })
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')->get();
        foreach ($blog as $item) {
            $data[] = $item->id;
        }
        \Cache::forever('blog_all', $data);

        $data = [] ;
        $blogs = \App\Models\Article::select('id', 'title', 'slug', 'thumbnail', 'description', 'type', 'creator');
        $blogs = $blogs->where('type', 'Blogs')->where(function ($blogs) {
            $blogs->where('status', 'publish');
            $blogs->Orwhere('published_at', '<=', date('Y-m-d H:i'));
        });
        $blogs = $blogs->orderBy('published_at', 'desc')->orderBy('id', 'desc');
        $blogs = $blogs->skip(0)->take(8)->get();
        foreach ($blogs as $item) {
            $data[] = $item->id;
        }
        \Cache::forever('blog_home', $data);
    }
}
