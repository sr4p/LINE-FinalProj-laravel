<?php

namespace App\Http\Controllers;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Http\Request;
use \App\User;

class line_studentcard extends Controller
{
    public function index()
    {
        return view('line_card');
    }
    //
    public function showProfile(Request $req){
        $idStu = $req->input('u1');
        $dt = User::where('userId',$idStu)->where('status','ใช้งานอยู่')->get();
        $a2 = json_decode($dt);

        foreach($a2 as $row) {
            foreach($row as $key => $val) {
                if($key == 'username'){
                    $iddd = $val;
            } if($key == 'prefix_eng'){
                $pe1 = $val;
            } if($key == 'name_eng'){
                $pe2 = $val;
            } if($key == 'surname_eng'){
                $pe3 = $val;
            } if($key == 'prefix_thai'){
                $pt1 = $val;
            } if($key == 'name_thai'){
                $pt2 = $val;
            } if($key == 'surname_thai'){
                $pt3 = $val;
            } if($key == 'faculty_eng'){
                $fe = $val;
            } 
            if($key == 'faculty_thai'){
                $ft = $val;
            }
            }
        }

        $data = array('iddd'=>$iddd,'pe1'=>$pe1,'pe2'=>$pe2,'pe3'=>$pe3,'pt1'=>$pt1,'pt2'=>$pt2,'pt3'=>$pt3,'fe'=>$fe,'ft'=>$ft);
        return view('line_card_srudent')->with($data);
    }
}
