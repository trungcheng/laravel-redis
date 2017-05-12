<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'collection';

    function collectionRelate()
    {
        return $this->hasMany('App\Models\MetaCollection', 'collection_id')->where('meta_key', 'article_of_collection');
    }

    function getArticle()
    {
        return $this->belongsToMany('App\Models\Article', 'meta_collection', 'collection_id', 'meta_value')->where('meta_key', '=', 'article_of_collection');
    }

    function getUser()
    {
        return $this->hasOne('App\Models\User', 'email', 'creator');
    }

    function getTotalCollectionArticleAttribute()
    {
        $count = MetaCollection::where('collection_id', $this->id)->where('meta_key', 'article_of_collection')->count();
        return $count;
    }

}
