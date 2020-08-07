<?php

namespace App\Http\Controllers;

use Config;
use Illuminate\Http\Request;
use \App\ConfigAT;
use \App\User;
use \RecursiveArrayIterator;
use \RecursiveIteratorIterator;

class line_login extends Controller
{

    private $access_token;
    public $Rich_Stu;
    public $Rich_Personnal;

    public function index()
    {
        return view('line_login');
    }

    public function close()
    {
        return view('line_login_close');
    }

    public function insert($arrData, $uidline, $picline, $displayname)
    {
        $total = User::count();
        $data = new User;
        $data->_id = $total + 1;
        $data->picture = $picline;
        $data->displayName = $displayname;
        $data->status = 'ใช้งานอยู่';
        $data->password_expire = '2020-08-30';

        foreach ($arrData as $key => $val) {
            if (is_array($val)) {
            } else {
                $data->$key = $val;
            }
        }
        $data->save();
        return "Success";
    }

    public function insertAgain($idStu, $arrData, $uidline, $picline, $displayname)
    {
        $user = User::where('username', $idStu)->where('status', 'ใช้งานอยู่')->get();

        $profile = array();
        foreach ($arrData as $key => $val) {
            if (is_array($val)) {
            } else {
                $profile[$key] = $val;
            }
        }

        $profile['userId'] = $uidline;
        $profile['picture'] = $picline;
        $profile['displayName'] = $displayname;
        $profile['status'] = 'ใช้งานอยู่';
        $profile['password_expire'] = '2020-08-30';

        $dt = User::where('username', $idStu)->where('status', 'ไม่ได้ใช้งาน');
        $dt->update($profile, ['upsert' => false]);
        $this->Out_rich($uidline);
    }

    public function pushMsg($arrayPostData)
    {
        $strUrl = "https://api.line.me/v2/bot/message/push";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $strUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer $this->access_token",
            "cache-control: no-cache",
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayPostData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function rich($userline)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.line.me/v2/bot/richmenu/bulk/link",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(array(
                "richMenuId" => "$this->Rich_Stu",
                "userIds" => ["$userline"],
            )),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer $this->access_token",
                "cache-control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }

    public function richTech($userline)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.line.me/v2/bot/richmenu/bulk/link",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(array(
                "richMenuId" => "$this->Rich_Personnal",
                "userIds" => ["$userline"],
            )),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer $this->access_token",
                "cache-control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }

    public function encodeData($userId, $username)
    {
        $digit = date('d') % 7 + 5;

        $ref = $this->getRef($digit);
        $str = $this->getRef($digit) . $ref . $this->getRef($digit + 1) . "|$userId|$username|" . $ref;
        return base64_encode($str);
    }

    public function getRef($length)
    {
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);
        $refcode = null;
        for ($i = 0; $i < $length; $i++) {
            $refcode .= $codeAlphabet[rand(0, $max - 1)];
        }
        return $refcode;
    }

    public function sendData($userid, $username)
    {
        $text = $this->encodeData($userid, $username);

        $url = "https://myid.buu.ac.th/lineAccount/" . $text;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    function exit($username) {
        $user = User::where('username', $username)->where('status', 'ใช้งานอยู่')->get();
        $count = count($user);
        if ($count == 0) {
            //
        } else {
            $this->logout($id);
        }
    }

    public function logout($uid)
    {
        $gg = array('status' => 'ไม่ได้ใช้งาน');
        $dt = User::where('userId', $uid);
        $dt->update($gg, ['upsert' => false]);
        $this->Out_rich($uid);
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
                "Authorization: Bearer $this->access_token",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }

    public function outUser($uid)
    {
        $richAll = ConfigAT::where('_id', 1)->get();
        $this->access_token = $richAll[0]['channelAccessToken'];

        $nameWhere = User::where('userId', $uid)->where('status', 'ใช้งานอยู่')->get();
        $name = $nameWhere[0]['username'];

        $gg = array('status' => 'ไม่ได้ใช้งาน');
        $dt = User::where('userId', $uid)->where('status', 'ใช้งานอยู่');
        $dt->update($gg, ['upsert' => true]);
        $this->Out_rich($uid);

        return response()->json("Logout : $name");
    }

    public function showUser()
    {
        $user = User::all();
        return response()->json(array($user));
    }

    public function getUserByID($id)
    {
        $user = User::where('username', $id)->get();
        return response()->json(array($user));
    }

    public function loginAgain($idStu)
    {
        $stu = User::where('username', $idStu)->where('status', 'ใช้งานอยู่')->get();
        $uid = $stu[0]['userId'];
        if (count($stu) != 0) {
            $this->Out_rich($uid);
            //
        } else {
            //
        }
    }

    public function PostApi(Request $req)
    {

        $richAll = ConfigAT::where('_id', 1)->get();

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

        $this->access_token = Config::get('linebot.ACCESS_TOKEN');
        $this->Rich_Stu = Config::get('linebot.RICHMENU_STUDENT');
        $this->Rich_Personnal = Config::get('linebot.RICHMENU_PERSONNAL');

        // $idStu = $req->input('userN');
        // $passStu = $req->input('passW');

        $idStu = $req['userN'];
        $passStu = $req['passW'];
        $userline = $req['u1'];
        $picline = $req['u2'];
        $displayline = $req['u3'];

        // echo '<script type="text/javascript">alert("'.$userline.'");</script>';
        // $userline = $req->input('u1');
        // $picline = $req->input('u2');
        // $displayline = $req->input('u3');
        $auth = false;

        $url = 'https://buu-api.buu.ac.th/api/version1/authBuu';
        $data_array = array(
            'username' => $idStu,
            'password' => $passStu);

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/json",
                'content' => json_encode($data_array)),
        ));

        $response = file_get_contents('https://buu-api.buu.ac.th/api/version1/authBuu', false, $context);

        if ($response === false) {
            echo "Failed";
            die('Error');
        }

        $responseData = new RecursiveIteratorIterator(new RecursiveArrayIterator(json_decode($response, true)), RecursiveIteratorIterator::SELF_FIRST);

        $nameFull = null;
        $surFull = null;

        //MongoDB
        $dataStu = array();
        $foo = true;

        foreach ($responseData as $key => $val) {
            if (is_array($val)) {
            } else {
                if ($key == 'status') {
                    if ($val == 'fail') {
                        // redirect()->back()->with('message', 'กรุณาพิมพ์ไอดีหรือรหัสผ่านให้ถูกต้อง');
                        return response()->json(['error' => 'Failed']);
                        break;
                    }if ($val == 'success') {
                        $auth = true;
                        $foo = false;
                        // $this->exit($idStu);
                        $dataStu['userId'] = $userline;
                    }
                }
                if ($key == 'username') {
                    $dataMy[$key] = $val;
                }

                if ($key == 'status' || $key == 'study_year') {
                    //
                } else {
                    $dataStu[$key] = $val;
                    if ($key == 'name_thai') {
                        $na = $val;
                    }
                    if ($key == 'surname_thai') {
                        $sur = $val;
                    }
                }
            }
        }

        $nameFull = $na;
        $surFull = $sur;

        if (is_numeric($idStu)) {
            $useAccount = User::where('username', $idStu)->where('status', 'ใช้งานอยู่')->get();
            if (count($useAccount) == 1) {
                $uId = $useAccount[0]['userId'];
                $this->logout($uId);
            } else {
                //
            }

            $user = User::where('username', $idStu)->where('status', 'ไม่ได้ใช้งาน')->get();
            if (count($user) == 1) {
                $this->insertAgain($idStu, $dataStu, $userline, $picline, $displayline);
            } else {
                $this->insert($dataStu, $userline, $picline, $displayline);
            }

            $this->sendData($userline, $idStu);
            if ($foo == false) {

                $data_schedula = array();
                $data_schedula['schedula'] = [
                    'monday' => [
                        [
                            'subject' => "84520459",
                            'building' => "IF-3M210",
                            'time_start' => "10:00",
                            'time_end' => "12:00",
                        ],
                        [
                            'subject' => "84520459",
                            'building' => "IF-5T01",
                            'time_start' => "13:00",
                            'time_end' => "15:00",
                        ],
                        [
                            'subject' => "84549159",
                            'building' => "IF-3M210",
                            'time_start' => "16:00",
                            'time_end' => "18:00",
                        ],
                    ],
                    'tuesday' => [
                        [
                            'subject' => "88620259",
                            'building' => "IF-6T05",
                            'time_start' => "10:00",
                            'time_end' => "12:00",
                        ],
                        [
                            'subject' => "88620259",
                            'building' => "IF-3C01",
                            'time_start' => "13:00",
                            'time_end' => "15:00",
                        ],
                    ],
                    'wednesday' => [
                        [
                            'subject' => "88636159",
                            'building' => "IF-5T05",
                            'time_start' => "10:00",
                            'time_end' => "12:00",
                        ],
                        [
                            'subject' => "88648159",
                            'building' => "IF-4M210",
                            'time_start' => "13:00",
                            'time_end' => "15:00",
                        ],
                        [
                            'subject' => "88634559",
                            'building' => "IF-6T05",
                            'time_start' => "15:00",
                            'time_end' => "17:00",
                        ],
                        [
                            'subject' => "88634559",
                            'building' => "IF-4C04",
                            'time_start' => "18:00",
                            'time_end' => "20:00",
                        ],
                    ],
                    'thursday' => [],
                    'friday' => [
                        [
                            'subject' => "99930459",
                            'building' => "KB-508",
                            'time_start' => "10:00",
                            'time_end' => "12:00",
                        ],
                        [
                            'subject' => "88643159",
                            'building' => "IF-7T05",
                            'time_start' => "13:00",
                            'time_end' => "15:00",
                        ],
                    ],
                    'saturday' => [],
                    'sunday' => [],
                ];
                $data_schedula['cost'] = [
                        'register' => 15000,
                        'library' => 0,
                        'rental' => 300,
                        'other' => 0,
                ];

                $update_sche = User::where('username', $idStu);
                $update_sche->update($data_schedula, ['upsert' => false]);

                //rich menu
                $this->rich($userline);
                //sendMgs
                $arrayPostData['to'] = $userline;
                $arrayPostData['messages'][0]['type'] = "text";
                $arrayPostData['messages'][0]['text'] = "สวัสดีคุณ$nameFull $surFull";
                $arrayPostData['messages'][1]['type'] = "text";
                $arrayPostData['messages'][1]['text'] = "ยินดีต้อนรับเข้าสู่ระบบ มาเริ่มใช้งานกันเลย!";
                $this->pushMsg($arrayPostData);
            } else {
                //

            }
        } else if (!is_numeric($idStu)) {

            $user = User::where('username', $idStu)->where('status', 'ไม่ได้ใช้งาน')->get();
            if (count($user) == 1) {
                $this->insertAgain($idStu, $dataStu, $userline, $picline, $displayline);
            } else {
                $this->insert($dataStu, $userline, $picline, $displayline);
            }
            $this->sendData($userline, $idStu);
            if ($foo == false) {

                $data_schedula = array();
                $data_schedula['schedula_edu'] = [
                    'monday' => [
                        [
                            'subject' => "84520459",
                            'building' => "IF-3M210",
                            'time_start' => "10:00",
                            'time_end' => "12:00",
                        ],
                        [
                            'subject' => "84520459",
                            'building' => "IF-5T01",
                            'time_start' => "13:00",
                            'time_end' => "15:00",
                        ],
                        [
                            'subject' => "84520459",
                            'building' => "IF-5T01",
                            'time_start' => "18:00",
                            'time_end' => "20:00",
                        ]
                    ],
                    'tuesday' => [
                        [
                            'subject' => "88620259",
                            'building' => "IF-6T05",
                            'time_start' => "10:00",
                            'time_end' => "12:00",
                        ],
                        [
                            'subject' => "88620259",
                            'building' => "IF-3C01",
                            'time_start' => "13:00",
                            'time_end' => "15:00",
                        ],
                    ],
                    'wednesday' => [
                        [
                            'subject' => "88636159",
                            'building' => "IF-5T05",
                            'time_start' => "10:00",
                            'time_end' => "12:00",
                        ],
                        [
                            'subject' => "88636159",
                            'building' => "IF-5T05",
                            'time_start' => "16:00",
                            'time_end' => "18:00",
                        ]
                    ],
                    'thursday' => [],
                    'friday' => [
                        [
                            'subject' => "88643159",
                            'building' => "IF-7T05",
                            'time_start' => "13:00",
                            'time_end' => "15:00",
                        ]
                    ],
                    'saturday' => [],
                    'sunday' => [],
                ];
                $data_schedula['schedula_test'] = [
                    [
                        'days' => '24/08/2563',
                        'building' => 'IF-4M210',
                        'time_start' => '10:00',
                        'time_end' => '12:00',
                    ],
                    [
                        'days' => '25/08/2563',
                        'building' => 'IF-6T05',
                        'time_start' => '13:00',
                        'time_end' => '15:00',
                    ],
                    [
                        'days' => '26/08/2563',
                        'building' => 'IF-3M210',
                        'time_start' => '16:00',
                        'time_end' => '18:00',
                    ],
                ];

                $update_sche = User::where('username', $idStu);
                $update_sche->update($data_schedula, ['upsert' => false]);

                //rich menu
                $this->richTech($userline);
                //sendMgs
                $arrayPostData['to'] = $userline;
                $arrayPostData['messages'][0]['type'] = "text";
                $arrayPostData['messages'][0]['text'] = "สวัสดีคุณ$nameFull $surFull";
                $arrayPostData['messages'][1]['type'] = "text";
                $arrayPostData['messages'][1]['text'] = "ยินดีต้อนรับเข้าสู่ระบบ มาเริ่มใช้งานกันเลย!";
                $this->pushMsg($arrayPostData);
            } else {
                //
            }
        }
        return response()->json(['success' => 'successfully']);

    }
}
