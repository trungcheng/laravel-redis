<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CacheRecipes extends Job implements ShouldQueue
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
        $data = [];
        $recipe = \App\Models\Article::select('id')
            ->where('type', 'Recipe')
            ->where(function ($reviews) {
                $reviews->where('status', 'publish');
                $reviews->Orwhere('published_at', '<=', date('Y-m-d H:i'));
            })
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')->get();
        foreach ($recipe as $item) {
            $data[] = $item->id;
        }
        \Cache::forever('recipe_all', $data);

        $data = [];
        $recipe = \App\Models\Article::select('id', 'title', 'slug', 'thumbnail', 'description', 'type', 'creator');
        $recipe = $recipe->where('type', 'Recipe')->where(function ($recipe) {
            $recipe->where('status', 'publish');
        });
        $recipe = $recipe->orderBy('published_at', 'desc')->orderBy('id', 'desc');
        $recipe = $recipe->paginate(6);
        foreach ($recipe as $item) {
            $data2[] = $item->id;
        }
        \Cache::forget('recipe_home');
        \Cache::forever('recipe_home', $data2);


    }
}
