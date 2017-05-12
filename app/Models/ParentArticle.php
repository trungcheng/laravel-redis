<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentArticle extends Model
{
    protected $table = 'parent_article';


    function getCategory()
    {
        return $this->hasOne('\App\Models\Category', 'id', 'category_id');
    }

    function getArticle()
    {
        return $this->hasOne('App\Models\Article', 'id', 'article_id');
    }
}
