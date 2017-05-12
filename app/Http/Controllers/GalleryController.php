<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class GalleryController extends Controller
{
    function getIndex()
    {

    }

    function getCreate()
    {
        $this->authorize('CreateArticle');
        return view('childs.gallery.create');
    }

    function postCreate()
    {
        dd(\Request::all()) ;
    }

    function getDelete()
    {

    }
}
