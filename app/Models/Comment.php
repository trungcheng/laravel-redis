<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;
    protected $table = 'rate';
    protected $dates = ['deleted_at'];

    function articleComment()
    {
        return $this->hasOne('App\Models\Article', 'id', 'article_id');
    }

}
