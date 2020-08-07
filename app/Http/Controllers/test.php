<?php

namespace App\Http\Controllers;

use DateTime;
use \App\Rich;
use \App\User;

class test extends Controller
{
    //

    public function test_login()
    {
        return view('testlogin');
    }
    
    public function test_profile($id)
    {
        $profile = User::where('username', $id)->get();
        var_dump($profile);
    }

    public function dateformat()
    {

        $userName = User::where('userId','Ude92fc5b523ebbfa80a5738ef6cbd495')->where('status', 'ใช้งานอยู่')->get();  
        $sche = $userName[0]['schedula']['monday'][0]['subject'];
        $sche1 = $userName[0]['schedula']['monday'];
        // $sche1 = $sche[0]['subject'];
        print_r("$sche : ");
        // echo("\n");
        print_r($sche1);
        // $array_class = array();
        // $find = json_decode($class);
        // foreach ($find as $row) 

        // for($i=0;$i<4;$i++){
        //     $flex = [
        //         "header"=> [
        //           "type"=> "box",
        //           "contents"=> [
        //               [
        //               "text"=> "85256459",
        //               "align"=> "center",
        //               "weight"=> "bold",
        //               "color"=> "#000000",
        //               "size"=> "xl",
        //               "type"=> "text"
        //           ],
        //             [
        //               "weight"=> "bold",
        //               "align"=> "center",
        //               "type"=> "text",
        //               "size"=> "lg",
        //               "text"=> "Building : A7G9",
        //               "color"=> "#000000"
        //           ],
        //             [
        //               "text"=> "Time : 15.30 - 18.30 น.",
        //               "color"=> "#000000",
        //               "weight"=> "bold",
        //               "align"=> "center",
        //               "type"=> "text",
        //               "size"=> "lg"
        //               ]
        //           ],
        //           "layout"=> "vertical"
        //       ],
        //         "type"=> "bubble",
        //         "direction"=> "ltr",
        //         "styles"=> [
        //           "header"=> [
        //             "backgroundColor"=> "#F8F6D5"
        //             ]
        //           ]
        //     ];

        //     $array_class[] = $flex;
        //     // $flex = '';
        // }

        // print_r($array_class);




        // echo "GG<br>";
        // $RichStu = Rich::where('richId','richmenu-df06b83db820172205af9aa6fabf2871')->get();
        // $RichStuName =  $RichStu[0]['name'];
        // echo "out : $RichStuName";





    //     $date = "22/05/2020";
    //     $today =  date("d/m/Y");

    // if($today > $date) {
    //     echo "$date was in past";
    // } else if ($today == $date) {
    //     echo "It's now";
    // } else {
    //     echo "$date is in future";
    // }
        // $date = "04/15/2020";
        // echo date("d-m-Y", strtotime($date));
        // echo "=>", date("d/m/Y");

        // echo "<br>";

        // $tt1 = "04/17/2020";
        // $ddddddd = date("Y-m-d", strtotime($tt1));

        // $datetime1 = new DateTime();
        // $datetime2 = new DateTime($ddddddd);
        // $difference = $datetime1->diff($datetime2);
        // dd($difference);

        // if ($difference->invert == 1) {
        //     if($difference->days == 0){
        //         //continue
        //     } else {
        //         redirect('/main/Richdata')->withError('กรุณากำหนดเวลาใหม่');
        //     }
        // } else {
        //     //continue
        // }

// $tt = strtotime($tt1);
        // $newformat = date('Y-m-d',$tt);

// $dddd = date("Y-m-d");

// $myDateTime = DateTime::createFromFormat('m/d/Y',$tt);
        // $newDateString = $myDateTime->format('Y-m-d');
        // echo "ggggggg : $ddddddd";

// $tt1 = date_create_from_format('Y/m/d',$tt);
        // echo $tt1;
        // echo "<br>";
        // $dateFormat1 = date("Y-m-d", strtotime($tt));

// echo $dayFormat;

// $datetime2 = new DateTime();
        // $dayForma2 = $datetime2->format('m-d-Y');
        // echo $dayFormat2;

// $datetime2 = new DateTime($dateFormat1);
        // dd($datetime1->format('m-d-Y'));
        // dd($datetime2);

// var_dump();
        // echo $tt;
        // dd($datetime1->format('m-d-Y'));

        // $rich = Rich::all();
        // $rich = Rich::all();
        // $rich_de = json_decode($rich);
        // $date=[];
        // foreach ($rich_de as $row) {
        //     foreach ($row as $key => $val) {
        //         // $date[] = sizeof($row);
                // if($row->name && $row->$key == 'status' && $row->$key == 'richId' ){
                //     $date[] = $row->name;
                // } 
                // else {
                    // $date[] = $row->richId;
                // }
                // if($key == 'richId'){
                //     // $nonId = Rich::where('richId', $val)->where('richId', '!=', 'richmenu-4869991e67b0d8be462b2d52b35ed2c3')->where('richId', '!=', 'richmenu-4869991e67b0d8be462b2d52b35ed2c3')->where('richId', '!=','richmenu-4869991e67b0d8be462b2d52b35ed2c3')->get();
                //     // $gg = json_decode($nonId,true);
                //     $nonId = Rich::where('richId', $val)->where('timeRich', 'exists', false)->get();
                //     $date[] = $nonId;
                // }
                
        //         // $date[] = count($key);
        //         // if($key['name'] == ''){
        //             // $date[] = $row->name;
        //         // }

        // $date[] = count($rich_de->name);
            // }
        // }

        


        // dd($date);
        // if (count($rich_de) == 0) {
        //     //
        //     echo 'not have';
        // } else {
        //     echo 'have';
        // }

        // echo "<br>";

        

        //     }
        // }

        // echo $date;
        // echo "<br>";
        // // $date == $row->timeRich

        // dd($rich_de);
    }

    public function testtime()
    {
        $datetime1 = new DateTime();
        $datetime2 = new DateTime('2020-08-02 15:04:53');
        $interval = $datetime1->diff($datetime2);
        $elapsed = $interval->format('%y years %m months %a days %h hours %i minutes %s seconds');
        $elapsed2 = $interval->format('%i minutes %s seconds');
        // $elapsed2 = $interval->format('u');
        $te = $interval->format('%R%a days');
        $tee = str_replace("+", "", $te);

        // $date = "04/15/2020";

        // echo date("d-m-Y", strtotime($date));
        // dd($elapsed);
        // $arr = array();
        // echo $interval;
        // $arr[] = $datetime2;
        // $js = json_encode($arr);
        // echo implode(" ",$js);
        // $dd = json_decode((string)$interval);

        // echo gettype($js);
        // echo " == ";
        // echo $elapsed;
        // echo $elapsed2;
        // echo "<br";
        // echo $tee;

        $userStudent = array();
        $userPersonnal = array();

        $user = User::where('status', 'ใช้งานอยู่')->get();
        $user1 = json_decode($user);

        foreach ($user1 as $row) {
            foreach ($row as $key => $val) {
                if ($key == "username") {
                    if (is_numeric($val)) {
                        $userStudent[] = $row->userId;
                    }if (!is_numeric($val)) {
                        $userPersonnal[] = $row->userId;
                    }
                }
            }
        }

        $arrayUser = array_values($userStudent);
        $arrayPersonnal = array_values($userPersonnal);
        $gg = json_encode($arrayUser);
        echo $gg;

    }

    public function pass_expire($datetime)
    {
        $datetime1 = new DateTime();
        $datetime2 = new DateTime('2020-08-02 15:04:53');
        // $datetime2 = new DateTime($datetime);
        $interval = $datetime1->diff($datetime2);
        $days = $interval->format('%R%a days');
        $getDays = str_replace("+", "", $days);
        return $getDays;
    }
}
