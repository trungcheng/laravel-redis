<?php

namespace App\Models;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Watson\Rememberable\Rememberable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model implements AuthorizableContract
{

    use Rememberable;

    use Authorizable;

    use SoftDeletes;

    protected $table = 'article';
    protected $dates = ['deleted_at'];

    function getParent()
    {
        return $this->hasOne('App\Models\ParentArticle', 'article_id');
    }

    function articleCategory()
    {
        return $this->belongsToMany('App\Models\Category', 'meta_article', 'article_id', 'meta_value')->where('meta_key', '=', 'relation_category');
    }

    function articleSteps()
    {
        return $this->hasMany('App\Models\Step', 'article_id')->orderBy('number_step', 'asc');
    }

    function articleLocation()
    {
        return $this->belongsToMany('App\Models\Category', 'meta_article', 'article_id', 'meta_value')->where('meta_key', '=', 'relation_location');
    }

    function articleGuess()
    {
        return $this->belongsToMany('App\Models\Category', 'meta_article', 'article_id', 'meta_value')->where('meta_key', '=', 'relation_guess');
    }

    function articlegallery()
    {
        return $this->hasOne('App\Models\MetaArticle', 'article_id')->where('meta_key', 'like', '%gallery');
    }

    function articleOtherInfoReview()
    {
        return $this->hasMany('App\Models\MetaArticle', 'article_id')->where('meta_key', 'like', 'review_%');
    }

    function articleOtherInfoRecipe()
    {
        return $this->hasMany('App\Models\MetaArticle', 'article_id')->where('meta_key', 'like', 'recipe_%');
    }

    function articleFollow()
    {
        return $this->hasMany('App\Models\MetaUser', 'meta_value')->where('meta_key', 'follow_article');
    }

    function articleLike()
    {
        return $this->hasMany('App\Models\MetaUser', 'meta_value')->where('meta_key', 'like_article');
    }

    function tags()
    {
        return $this->hasOne('App\Models\MetaArticle', 'article_id')->where('meta_key', 'tags');
    }

    function articleRating()
    {
        return $this->hasOne('App\Models\MetaArticleFe', 'article_id')->where('meta_key', 'rate_article');
    }

    function articleView()
    {
        return $this->hasOne('App\Models\MetaArticleFe', 'article_id')->where('meta_key', 'view_article');
    }

    function articleComment()
    {
        return $this->hasMany('App\Models\Comment', 'article_id');
    }

    function getUser()
    {
        return $this->hasOne('App\Models\User', 'email', 'creator');
    }

    function articleAdress()
    {
        return $this->hasOne('App\Models\MetaArticle', 'article_id')->where('meta_key', '=', 'review_address');

    }

    function related()
    {
        return $this->hasOne('App\Models\MetaArticle', 'article_id')->where('meta_key', 'related');
    }

    function BuiltTopArticle()
    {
        return $this->hasOne('App\Models\BuiltTop', 'post_id');
    }

    function getWardName()
    {
        return $this->hasOne('App\Models\MetaArticle', 'article_id')->where('meta_key', 'review_ward');
    }

    function getWard()
    {
        return $this->hasOne('App\Models\MetaArticle', 'article_id')->where('meta_key', 'review_ward');
    }

    function getIngredients()
    {
        return $this->belongsToMany('\App\Models\Ingredients', 'ingredients_relation', 'article_id', 'ingredient_id')->withPivot('quanlity', 'quanlity_type');
    }

    function getEvent()
    {
        return $this->hasOne('App\Models\Event', 'id', 'event_id');
    }

}
