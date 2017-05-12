<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaArticle extends Model
{
    protected $table = 'meta_article' ;
    function getWard() {
        return $this->hasOne('App\Models\Ward', 'wardid' , 'meta_value');
    }
}
