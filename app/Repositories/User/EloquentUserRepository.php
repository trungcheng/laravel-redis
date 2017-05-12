<?php
/**
 * Created by PhpStorm.
 * User: ger2017
 * Date: 12/8/15
 * Time: 2:42 PM
 */

namespace App\Repositories\User;


use App\Models\User;

class EloquentUserRepository implements UserRepository
{
    public function findAll()
    {
        // TODO: Implement findAll() method.
        return User::all();
    }

    public function insertNewUser($params)
    {
        // TODO: Implement insertNewUser() method.
        $user = new User;
        $user->name = $params['name'];
        $user->email = $params['email'];
        $user->password = bcrypt($params['password']);

        $user->save();
    }

    public function updateUser($user_id, $params)
    {
        // TODO: Implement updateUser() method.
        $user = User::find($user_id);
        foreach ($params as $key => $value) {
            $user_id->$key = $value;
        }
        $user->save();
    }
}