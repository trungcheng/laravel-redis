<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'district';
    function getProvince() {
        return  $this->hasOne('\App\Models\Province' , 'provinceid'  , 'provinceid') ;
    }
}
