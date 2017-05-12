<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ward extends Model
{
    use SoftDeletes;
    protected $table = 'ward';
    protected $dates = ['deleted_at'];

    function getDistrict() {
        return  $this->hasOne('\App\Models\District' , 'districtid'  , 'districtid') ;
    }
}
