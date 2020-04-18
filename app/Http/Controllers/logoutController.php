<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class logoutController extends Controller
{
    //
    public function Logout(Request $req)
    {
        printf($req);
        session()->pull('username');
        $req->session()->flush();
        return redirect('/');
    }
}
