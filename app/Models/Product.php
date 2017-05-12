<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    function getUser()
    {
        return $this->hasOne('App\Models\User', 'email', 'creator');
    }
}
