<?php

namespace App\Models;


use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Model;

class ShopList extends Model
{
    protected $table = 'shop_list';

    function getIngreDient()
    {
        return $this->hasOne('App\Models\Ingredients', 'id', 'ingredient_id')->select();
    }
}


