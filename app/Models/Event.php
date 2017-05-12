<?php

namespace App\Models;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model implements AuthorizableContract {

    use Authorizable;

use SoftDeletes;

    protected $table = 'events';
    protected $dates = ['deleted_at'];

}
