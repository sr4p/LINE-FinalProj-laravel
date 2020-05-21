<?php

namespace App\Http\Controllers;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Http\Request;
use \App\User;
use \App\Admin;
use GPBMetadata\Google\Monitoring\V3\Alert;

class Usercontroller extends Controller
{
    //
    public function showUser(Request $req)
    {
        // $a2 = User::where('status', 'ใช้งานอยู่')->paginate(5);
        $a2 = User::where('status', 'ใช้งานอยู่')->get();
        return view('user', compact('a2'));
    }

    public function showInfoUser(Request $req)
    {
        $username = $req['username'];
        $a4 = User::where('username', $username)->get();
        return response()->json($a4);
    }

    public function showAdmin(Request $req)
    {
        $a3 = Admin::all();
        return view('admin', compact('a3'));
    }

    public function changeStatus(Request $req)
    {
        $idStu = $req['un'];
        $uId = $req['uid'];
        $data = array('status' => 'ไม่ได้ใช้งาน');
        User::where('username', $idStu)
            ->update($data, ['upsert' => true]);
        $this->Out_rich($uId);
    }

    public function Out_rich($userline)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.line.me/v2/bot/user/" . $userline . "/richmenu",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer NAEP1E2z0BM725TTUZZRLWtNH8R0r0FUqvoIl7QYA1Yi5XuKyUU7XcyAMfojc0JgXELn6ok3bxFspM+91tPd/ylST/Wq/+0FB+4ieaf+Y7gid2A40Rq52CAr8HMIfTa3kvDi2QiFQwvJQe1tKcXzRlGUYhWQfeY8sLGRXgo3xvw=",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }

    public function push_msg(Request $req)
    {
        $uId = $req['uid'];
        $msgInput = $req['msg'];
        $msg = [
            'type' => 'text',
            'text' => $msgInput,
            ];
        $data = [
            'to' => $uId,
            'messages' => [$msg]
        ];
       
        $strUrl = "https://api.line.me/v2/bot/message/push";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $strUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer NAEP1E2z0BM725TTUZZRLWtNH8R0r0FUqvoIl7QYA1Yi5XuKyUU7XcyAMfojc0JgXELn6ok3bxFspM+91tPd/ylST/Wq/+0FB+4ieaf+Y7gid2A40Rq52CAr8HMIfTa3kvDi2QiFQwvJQe1tKcXzRlGUYhWQfeY8sLGRXgo3xvw=",
            "cache-control: no-cache"
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        if($result){
            return redirect()->back();
        }
    }
}
