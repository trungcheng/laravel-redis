<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class User extends Model implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword , Rememberable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function accessMediasAll()
    {
        return false;
    }
    public function accessMediasFolder()
    {
        return true ;
    }
    
    function articleFollow()
    {
        return $this->hasMany('App\Models\MetaUser', 'user_id')->where('meta_key', 'follow_article');
    }
    function articleLike()
    {
        return $this->hasMany('App\Models\MetaUser', 'user_id')->where('meta_key', 'like_article');
    }

    function shopList(){
        return $this->hasMany('App\Models\ShopList','user_id');
    }

    function getDevice()
    {
        return $this->hasMany('App\Models\Device', 'user_id');
    }
}
