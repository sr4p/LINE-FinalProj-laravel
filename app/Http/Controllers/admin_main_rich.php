<?php

namespace App\Http\Controllers;

use Config;
use Illuminate\Http\Request;
use \App\ConfigAT;
use \App\Rich;

class admin_main_rich extends Controller
{
    private $access_token;
    //
    public function showRich(Request $req)
    {
        $countId = ConfigAT::count();
        $richAll = ConfigAT::where('_id', $countId)->get();
        $ac_token = $richAll[0]['channelAccessToken'];
        $ac_secret = $richAll[0]['channelSecret'];
        $cLogin = $richAll[0]['richmenu_login'];
        $cStudent = $richAll[0]['richmenu_student'];
        $cPersonnal = $richAll[0]['richmenu_personnal'];

        Config::set('linebot.ACCESS_TOKEN', $ac_token);
        Config::set('linebot.CHANNEL_SECRET', $ac_secret);
        Config::set('linebot.RICHMENU_LOGIN', $cLogin);
        Config::set('linebot.RICHMENU_STUDENT', $cStudent);
        Config::set('linebot.RICHMENU_PERSONNAL', $cPersonnal);

        $rl = null;
        $rs = null;
        $rp = null;

        $bl = false;
        $bs = false;
        $bp = false;

        $rich1 = json_decode($richAll);
        foreach ($rich1 as $row) {
            foreach ($row as $key => $val) {
                if ($key == "richmenu_login") {
                    $rl = $val;
                }if ($key == "richmenu_student") {
                    $rs = $val;
                }if ($key == "richmenu_personnal") {
                    $rp = $val;
                }
            }
        }


        
        $countId = Rich::count();

        if($countId == 0){
            $rich = null;
            $richL = null;
            $richS = null;
            $richP = null;
            $all = ['richL', 'richS', 'richP', 'rich'];
            return view('admin_rich', compact($all));
        } else if($countId != 0) {
            $rich = Rich::all();
            $richDisable = Rich::where('status','ยังไม่ได้ใช้งาน')->get();
            $richL = null;
            $richS = null;
            $richP = null;

            if($rl != ""){
                $richL = Rich::where('richId', $rl)->get();
            } else {
                $richL = array("name" => '',"status" => '');
            }

            if($rs != ""){
                $richS = Rich::where('richId', $rs)->get();
            } else {
                $richS = array("name" => '',"status" => '');
            }

            if($rp != ""){
                $richP = Rich::where('richId', $rp)->get();
            } else {
                $richP = array("name" => '',"status" => '');
            }


            // $richL = Rich::where('richId', $rl)->get();
            // $richS = Rich::where('richId', $rs)->get();
            // $richP = Rich::where('richId', $rp)->get();
            $all = ['richL', 'richS', 'richP', 'rich','richDisable'];
            return view('admin_rich', compact($all));
        }

        
        

        // $richCount = Rich::count();
        // if ($richCount == 0) {
        //     // return view('admin_rich');
        //     $rich = null;
        // $richL = array("name" => '',"status" => '');
        // $richS = array("name" => '',"status" => '');
        // $richP = array("name" => '',"status" => '');
        // $all = [];
        // } else {
        //     $rich = Rich::all();
        //     $richL = Rich::where('richId', $rl)->get();
        //     $richS = Rich::where('richId', $rs)->get();
        //     $richP = Rich::where('richId', $rp)->get();
        //     $all = ['richL', 'richS', 'richP', 'rich'];
        //     return view('admin_rich', compact($all));
        // }

           

    }

    public function useRich(Request $req)
    {
        $rich = Rich::all();
        return view('admin_rich', compact('rich', ));
    }

    // public function useRich(Request $req)
    // {
    //     $rich = Rich::where('status','ยังไม่ได้ใช้งาน')->get();
    //     // $rich = Rich::all();
    //     return view('admin_rich', compact('rich',));
    // }

    // public function showUseRich(Request $req)
    // {
    //     $rich = Rich::where('status','ยังไม่ได้ใช้งาน')->get();
    //     return view('admin_rich', compact('rich',));
    // }
}
