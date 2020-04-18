<?php

namespace App\Http\Controllers;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Http\Request;
use \RecursiveIteratorIterator;
use \RecursiveArrayIterator;
use \App\User;
use \App\ConfigAT;
use Config;
use Carbon\Carbon;

class line_login extends Controller
{

    private $access_token;
    public $Rich_Stu;
    public $Rich_Personnal;

    public function index(){
        return view('line_login');
    }
    public function insert($arrData,$uidline,$picline,$displayname){
        $total = User::count();
        $data = new User;
        $data->_id = $total+1;
        $data->picture = $picline;
        $data->displayName = $displayname;
        $data->status = 'ใช้งานอยู่';
        
        foreach ($arrData as $key => $val) {
            if(is_array($val)) {
            } else {
                $data->$key = $val;
            }
        }
        $data->save();
        return "Success";
    }

    public function insertAgain($idStu,$arrData,$uidline,$picline,$displayname){
        $user = User::where('username',$idStu)->where('status','ใช้งานอยู่')->get();

        $profile = array(); 
        foreach ($arrData as $key => $val) {
            if(is_array($val)) {
            } else {
                $profile[$key] = $val;
            }
        }

        $profile['userId'] = $uidline;
        $profile['picture'] = $picline;
        $profile['displayName'] = $displayname;
        $profile['status'] = 'ใช้งานอยู่';


        $dt = User::where('username',$idStu)->where('status','ไม่ได้ใช้งาน');
        $dt->update($profile, ['upsert' => true]);
        $this->Out_rich($uidline);
    }

    

    public function pushMsg($arrayPostData){
        $strUrl = "https://api.line.me/v2/bot/message/push";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$strUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer $this->access_token",
            "cache-control: no-cache"
          ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayPostData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close ($ch);
    }

    public function rich($userline){
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
            "richMenuId"=>"$this->Rich_Stu",
            "userIds"=>["$userline"]
          )),
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer $this->access_token",
            "cache-control: no-cache"
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }

    public function richTech($userline){
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
            "richMenuId"=>"$this->Rich_Personnal",
            "userIds"=>["$userline"]
          )),
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer $this->access_token",
            "cache-control: no-cache"
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }

    function encodeData($userId,$username){
        $digit = date('d')%7+5;
        
		$ref = $this->getRef($digit);
		$str = $this->getRef($digit).$ref.$this->getRef($digit+1)."|$userId|$username|".$ref;
		return base64_encode($str);
	}
	
	function getRef($length){
		 $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		 $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		 $codeAlphabet.= "0123456789";
		 $max = strlen($codeAlphabet);
        $refcode=null;
		for ($i=0; $i < $length; $i++) {
			$refcode .= $codeAlphabet[rand(0, $max-1)];
		}
		return $refcode;
	}
    
    public function sendData($userid,$username){
        $text = $this->encodeData($userid,$username); 
    
        $url = "https://myid.buu.ac.th/lineAccount/".$text;	
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function exit($username){
        $user = User::where('username',$username)->where('status','ใช้งานอยู่')->get();
        $count = count($user);
            if($count == 0){
                //
            } else {
                $this->logout($id);
            }
    }

    public function logout($uid)
    {
        $gg = array('status' => 'ไม่ได้ใช้งาน');
        $dt = User::where('userId', $uid);
        $dt->update($gg, ['upsert' => true]);
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

    public function outUser($uid){
        $richAll = ConfigAT::where('_id', 1)->get();
        $this->access_token = $richAll[0]['channelAccessToken'];

        $nameWhere = User::where('userId', $uid)->where('status','ใช้งานอยู่')->get();
        $name = $nameWhere[0]['username'];

        $gg = array('status' => 'ไม่ได้ใช้งาน');
        $dt = User::where('userId', $uid)->where('status','ใช้งานอยู่');
        $dt->update($gg, ['upsert' => true]);
        $this->Out_rich($uid);
        
        return response()->json("Logout : $name");
    }


    public function loginAgain($idStu)
    {
        $stu = User::where('username', $idStu)->where('status','ใช้งานอยู่')->get();
        $uid = $stu[0]['userId'];
        if(count($stu) != 0){
            $this->Out_rich($uid);
            //
        } else {
            //
        }
    }
    
    public function PostApi(Request $req) {
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

        $idStu = $req->input('userN');
        $passStu = $req->input('passW');
        $userline = $req->input('u1');
        $picline = $req->input('u2');
        $displayline = $req->input('u3');

        

        $url = 'https://buu-api.buu.ac.th/api/version1/authBuu';
        $data_array =  array(
            'username' => $idStu,
            'password' => $passStu);

            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-Type: application/json",
                    'content' => json_encode($data_array))
            ));

            $response = file_get_contents('https://buu-api.buu.ac.th/api/version1/authBuu', FALSE, $context);

            if($response === FALSE){
                echo "Failed";
                die('Error');
            }

             $responseData = new RecursiveIteratorIterator(new RecursiveArrayIterator(json_decode($response, TRUE)),RecursiveIteratorIterator::SELF_FIRST);
            
            $nameFull = null;
            $surFull = null;

            //MongoDB
            $dataStu = array();
            $foo = true;
            
                foreach ($responseData as $key => $val) {
                    if(is_array($val)) {
                    }
                    else {
                        if($key == 'status' ){
                            if($val == 'fail'){
                                return redirect()->back()->with('message', 'กรุณาพิมพ์ไอดีหรือรหัสผ่านให้ถูกต้อง');
                                break;
                            } if($val == 'success'){
                                $foo = false;
                                $this->exit($idStu);
                                $dataStu['userId'] = $userline;
                            }
                        }
                        if($key == 'username') {
                            $dataMy[$key] = $val;
                        }
    
                        if($key == 'status' || $key == 'study_year'){
                            //
                        }
                        else {
                          $dataStu[$key] = $val;
                          if($key == 'name_thai'){
                            $na = $val;
                            }
                            if($key == 'surname_thai'){
                              $sur = $val;
                            }
                        }
                    }
                }
            
            $nameFull = $na;
            $surFull = $sur;



            if(is_numeric($idStu)){
                $useAccount = User::where('username',$idStu)->where('status','ใช้งานอยู่')->get();
                if(count($useAccount) == 1){
                    $uId = $useAccount[0]['userId'];
                    $this->logout($uId);
                } else {
                    //
                }

                $user = User::where('username',$idStu)->where('status','ไม่ได้ใช้งาน')->get();    
                if(count($user) == 1){
                    $this->insertAgain($idStu,$dataStu,$userline,$picline,$displayline);
                } else {
                    $this->insert($dataStu,$userline,$picline,$displayline);
                }

                


                $this->sendData($userline,$idStu);
                if($foo == false){
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
            } else if(!is_numeric($idStu)) {
                $user = User::where('username',$idStu)->where('status','ไม่ได้ใช้งาน')->get();
                if(count($user) == 1){
                    $
                    $this->insertAgain($idStu,$dataStu,$userline,$picline,$displayline);
                } else {
                    $this->insert($dataStu,$userline,$picline,$displayline);
                }

                // $this->insert($dataStu,$userline,$picline,$displayline);
                $this->sendData($userline,$idStu);
                if($foo == false){
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
           

            //close LIFF
            echo '<script src="https://static.line-scdn.net/liff/edge/2.0/sdk.js"></script>';
            echo "<script type='text/javascript'>";
            echo 'liff.init({liffId:"1653845388-2mPZP8OR"}, () => {}, err => console.error(err.code, error.message));';
            echo "</script>";

            echo "<script type='text/javascript'>";
            echo "liff.closeWindow();";
            echo "</script>";
    }
}
