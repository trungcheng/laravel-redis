<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Article;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use App\Policies\UserPolicy;
use App\Policies\ReportPolicy;
use App\Policies\ArticlePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    public $policies = [
        \App\Models\User::class => \App\Policies\PostPolicy::class
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
        foreach (config('permission') as $k => $v) {
            $gate->define($k, function ($user) use ($k) {
                if (in_array($user->user_type, config('permission.' . $k))) {
                    return true;
                }
            });
        };

        $gate->define('PostOfUser', function ($user, $post) {
            if($user->user_type != 'Admin' && $user->user_type != 'Editor' ) {
				if($post->status != 'publish' ) { 
					return $user->email === $post->creator;
				}else { 
					return false ; 
				}
            }else {
                return true ;
            }
        });
    }
}
