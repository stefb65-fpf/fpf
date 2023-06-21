<?php

namespace App\Http\Controllers;

use App\Concern\Tools;
use App\Http\Requests\LoginRequest;
use App\Models\Commune;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use Tools;
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

            $action = 'lorem ipsume dolor sic emet';

            $this->registerAction(1,4,"dfhsfhsfhs");
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

    public function registerAbonnement() {
        $communes = Commune::orderBy('nom')->get();
        return view('auth.registerAbonnement', compact('communes'));
    }

//    public function test(Request $request) {
//        dd($request->session()->get('user'));
//    }
}
