<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Ur;
use Illuminate\Http\Request;

class TopBarController extends Controller
{
    public function isAdmin()
    {
        $isAdmin = session()->get('menu')['admin'];
//        $isAdmin= false;
        return compact('isAdmin');
    }

}
