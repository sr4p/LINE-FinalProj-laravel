<?php

namespace App\Http\Controllers;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Http\Request;
use \RecursiveIteratorIterator;
use \RecursiveArrayIterator;
use \App\Admin;
use SebastianBergmann\Environment\Console;
use Symfony\Component\HttpFoundation\Session\Session;

class processLogin extends Controller
{

    public function checkUser(Request $req)
    {

        $idStu = $req->input('userN');
        $passStu = $req->input('passW');

        $url = 'https://buu-api.buu.ac.th/api/version1/authBuu';
        $data_array =  array(
            'username' => $idStu,
            'password' => $passStu
        );

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/json",
                'content' => json_encode($data_array)
            )
        ));
        $response = file_get_contents('https://buu-api.buu.ac.th/api/version1/authBuu', FALSE, $context);
        $check = json_decode($response, true);
        if ($check['status'] == "success") {
            $a2 = Admin::where('username', $idStu)
                ->get();
            if (sizeof($a2) == 0) {
                return redirect()->back()->with('message', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
            } else {
                session(['username' => $idStu]);
                $data = array('date' => date('Y-m-d H:i:s'));
                Admin::where('username', $idStu)
                    ->update($data, ['upsert' => true]);
                $role = $a2[0]['type'];
                if ($a2[0]['status'] == "ใช้งานอยู่") {
                    if ($role == "เจ้าหน้าที่") {
                        session(['role' => 'roleStaff']);
                        return redirect('/main')->with('roleStaff', $role);
                    } else {
                        session(['role' => 'roleAdmin']);
                        return redirect('/main')->with('roleAdmin', $role);
                    }
                } else {
                    return redirect()->back()->with('message', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
                }
            }
        } else {
            return redirect()->back()->with('message', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
        }


        // $url = 'http://10.80.6.79:27018/';
        // $data_array =  array(
        //     'username' => $idStu,
        //     'password' => $passStu
        // );

        // $context = stream_context_create(array(
        //     'http' => array(
        //         'method' => 'POST',
        //         'header' => "Content-Type: application/json",
        //         'content' => json_encode($data_array)
        //     )
        // ));

        $a2 = Admin::where('username', $idStu)
            ->where('password', $passStu)
            ->get();

        if (sizeof($a2)==0) {
            return redirect()->back()->with('message', 'กรุณาพิมพ์ชื่อผู้ใช้หรือรหัสให้ถูกต้อง');
        }else{
            session(['username' => $idStu]);
            return redirect('/main');
        }

        // $responseData = new RecursiveIteratorIterator(
        //     new RecursiveArrayIterator(json_decode($a2, TRUE)),
        //     RecursiveIteratorIterator::SELF_FIRST
        // );
        
    }
    public function checkLogin()
    {
        $usname = session("username");
        if ($usname) {
            return  redirect('main');
        } else {
            return redirect('/');
        }
    }
}
