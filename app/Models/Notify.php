<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notify extends Model
{
    use SoftDeletes;

    protected $table = 'push_notification';
    protected $dates = ['deleted_at'];

    function getUser()
    {
        return $this->hasOne('App\Models\User', 'email', 'creator');
    }

    function getArticle()
    {
        return $this->hasOne('App\Models\Article', 'id', 'article_id')->orderBy('id', 'desc');
    }
}
