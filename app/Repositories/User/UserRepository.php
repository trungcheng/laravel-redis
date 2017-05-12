<?php
/**
 * Created by PhpStorm.
 * User: ger2017
 * Date: 12/8/15
 * Time: 2:41 PM
 */

namespace App\Repositories\User;


interface UserRepository
{
    public function findAll();

    public function insertNewUser($params);

    public function updateUser($user_id, $params);
}