<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model ;

class Category extends Model
{
    protected $table = 'category';

    function getMetaCategory ()  {
        return $this->hasMany('App\Models\MetaCategory' , 'category_id') ;
    }
    function categoryArticle(){
        return $this->belongsToMany('App\Models\Article', 'meta_article' , 'meta_value', 'article_id' )->where('meta_key' , '=' , 'relation_category');
    }
    function locationArticle(){
        return $this->belongsToMany('App\Models\Article', 'meta_article' , 'meta_value', 'article_id' )->where('meta_key' , '=' , 'relation_location');
    }
    function guessArticle(){
        return $this->belongsToMany('App\Models\Article', 'meta_article' , 'meta_value', 'article_id' )->where('meta_key' , '=' , 'relation_guess');
    }
    function getChild() {
        return $this->hasMany('App\Models\Category' , 'parent_id' , 'id')  ;
    }
}
