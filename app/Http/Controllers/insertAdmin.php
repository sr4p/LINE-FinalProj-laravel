<?php

namespace App\Http\Controllers;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Http\Request;
use \RecursiveIteratorIterator;
use \RecursiveArrayIterator;
use \App\Admin;
use SebastianBergmann\Environment\Console;
use Illuminate\Http\Response;

class insertAdmin extends Controller
{
    public function insertform(){
        return view('admin');
    }

    public function insert(Request $req, Response $res){
        $admin->username = $req['username'];
        $admin->type = $req['type'];
        $admin->name_thai = $req['name_thai'];
        $admin->surname_thai = $req['surname_thai'];
        $admin->email = $req['email'];
        $admin->tel = $req['tel'];
        $admin->status = $req['status'];
        $check = $admin->save();
        if ($check) {
            return response('success', 200);
        } else {
            return response('unsuccess', 404);
        }
     }

     public function update(Request $req, Response $res)
    {
        $data = array(
            'type' => $req['type'], 'name_thai' => $req['name_thai'],
            'surname_thai' => $req['surname_thai'],
            'email' => $req['email'], 'tel' => $req['tel'],'status' => $req['status'],
        );
        $check = Admin::where('username', $req['username'])
            ->update($data, ['upsert' => true]);
        if ($check) {
            return response('success', 200);
        } else {
            return response('unsuccess', 404);
        }
    }

    public function editAdmin(Request $req)
    {
        $username = $req['username'];
        $a4 = Admin::where('username', $username)->get();
        return response()->json($a4);
    }

    public function showAdminInfo(Request $req)
    {
        $username = $req['username'];
        $a5 = Admin::where('username', $username)->get();
        return response()->json($a5);
    }


}