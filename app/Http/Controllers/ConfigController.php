<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ConfigController extends Controller
{
    function postCreate()
    {
        $data = request()->except('_token');
        $cache_key = json_encode($data);
        CreateFile($data);
        \Cache::forever('config_web_video', $cache_key);
        return json_encode(['status' => 'success', 'msg' => 'Post Successfully']);
    }

    function getCreate()
    {
        return view('childs.configs.config');
    }
}
