<?php

namespace App\Models;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Model;

class MediaUser extends Model implements AuthorizableContract
{
    use Authorizable;

    protected $table = 'media_user';
}
