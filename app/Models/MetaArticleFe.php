<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaArticleFe extends Model
{
    protected $table = 'meta_article_fe';

    function getArticle()
    {
        return $this->hasOne('App\Models\Article', 'id', 'article_id');
    }
}
