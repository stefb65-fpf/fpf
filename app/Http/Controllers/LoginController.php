<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(LoginRequest $request) {
        if ($request->email !== 'stephane.closse@gmail.com') {
            return redirect()->route('login')->with('error', "Email incorrect");
        }
            $user = array(
                'id' => 1,
                'name' => 'Closse',
                'firtsname' => 'Stéphane',
                'email' => 'stephane.closse@gmail.com'
            );
            $request->session()->put('user', $user);
//        $user = array(
//            'id' => 1,
//            'name' => 'Closse',
//            'firtsname' => 'Stéphane',
//            'email' => 'stephane.closse@gmail.com'
//        );
//        session('user', $user);
//        $others = array(
//            'name' => 'titi',
//            'slug' => 'toto',
//        );
//        $request->session()->put('user', $user);
//        $request->session()->put('page', $others);
//
        return view('pages.welcome');
    }

//    public function test(Request $request) {
//        dd($request->session()->get('user'));
//    }
}
