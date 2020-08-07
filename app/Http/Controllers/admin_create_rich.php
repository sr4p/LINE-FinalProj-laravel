<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Config;
use DateTime;
use File;
use Illuminate\Http\Request;
use Redirect;
use \App\ConfigAT;
use \App\Notification;
use \App\Rich;
use \App\User;

class admin_create_rich extends Controller
{
    //

    private $access_token;
    private $channelSecret;
    public $Rich_Login;
    public $Rich_Stu;
    public $Rich_Personnal;

    public function index()
    {
        return view('admin_richmenu');
    }

    public function CancelTimeRich(Request $req)
    {
        $id = $req->input('richId');
        $this->updateStatusAfterCancel($id);
        redirect()->back()->withSuccess('ยกเลิกเมนูเรียบร้อย');
    }

    public function CreateRichmenu(Request $req)
    {

        $countId = ConfigAT::count();
        $richAll = ConfigAT::where('_id', $countId)->get();

        $input = $req->all();
        $this->access_token = $richAll[0]['channelAccessToken'];
        $this->channelSecret = $richAll[0]['channelSecret'];
        $secret = $richAll[0]['channelSecret'];
        $err_json = [];
        $err_img = [];
        $err_json_detail = [];

        $json = $req->input('json');
        $file = $req->file('file');
        $name = $req->input('name');

        $filename = $file->getClientOriginalName();
        $location = 'images';
        $file->move($location, $filename);

        $pa = $file->getClientOriginalName();

        $ty = $file->getClientMimeType();

        $output = public_path("images\\" . $pa);

        list($width, $height) = getimagesize($output);
        // $err_json = [];
        // $err_img = [];

        // finish ---create Rich
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.line.me/v2/bot/richmenu");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer $this->access_token",
            "cache-control: no-cache",
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        // $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $you = json_decode($result, true);
        $richid = null;
        if (!empty($you['richMenuId'])) {
            $richid = $you['richMenuId'];
        } else {
            //
        }

        if (array_key_exists('message', $you)) {
            File::delete('images/' . $filename);
            $err_json_detail = $result;
            $err_json = ['js' => ['โค้ด JSON สำหรับสร้างริชเมนู ไม่ถูกต้อง']];
            return response()->json(['error_ceateImg' => $err_img, 'error_ceate' => $err_json, 'error_detail' => $err_json_detail]);
        } else {
            //finish ---upload img Rich
            $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient("$this->access_token");
            $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $secret]);
            $imagePath = $output;
            $contentType = $ty;
            $response = $bot->uploadRichMenuImage($richid, $imagePath, $contentType);
            $getMsg = $response->getRawBody();
            $arr = json_decode($getMsg, true);

            if (!empty($arr['message'])) {
                $this->RemoveRich($richid);
                File::delete('images/' . $filename);
                $err_img = ['img' => ['ไฟล์รูปไม่สามารถใช้งานได้ โปรดเลือกไฟล์รูปใหม่']];
                return response()->json(['error_ceateImg' => $err_img, 'error_ceate' => $err_json, 'error_detail' => $err_json_detail]);
            } else {

                $this->insertRich($richid, $name);

                File::delete('images/' . $filename);

                // response()->json(['error_ceateImg' => $err_img,'error_ceate' => $err_json,'error_detail' => $err_json_detail]);
                redirect('/main/Richdata')->withSuccess('เพิ่มริชเมนูเรียบร้อย');
            }
        }

        // response()->json(['error_ceateImg' => $err_img,'error_ceate' => $err_json,'error_detail' => $err_json_detail]);
        // return redirect('/main/Richdata')->withSuccess('เพิ่มริชเมนูเรียบร้อย');
    }

    public function insertRich($richid, $nameRich)
    {
        $data = new Rich;
        $data->name = $nameRich;
        $data->status = 'ยังไม่ได้ใช้งาน';
        $data->richId = $richid;
        $data->timeRich = '-';
        $data->timeType = '-';
        $data->save();
        return "Success";
    }

    public function UseRichmenu(Request $req)
    {
        $countId = ConfigAT::count();
        $richAll = ConfigAT::where('_id', $countId)->get();

        $this->access_token = $richAll[0]['channelAccessToken'];

        $input = $req->all();
        $login = $req['rich_login'];
        $student = $req['rich_student'];
        $personnal = $req['rich_personnal'];

        $time1 = $req['time_stu'];
        $time2 = $req['time_per'];

        // if($time1 == "non"){
        //     $time1 = "";
        // } if($time2 == "non"){
        //     $time2 = "";
        // } else {
        //     //
        // }


        $dateDays = date("d-m-Y");



        

        // $datetime = new DateTime();
        // $datetime1 = new DateTime($day1);
        // $datetime2 = new DateTime($day2);
        // $difference1 = $datetime->diff($datetime1);
        // $difference2 = $datetime->diff($datetime2);
        // $checkTime = false;
        // $useRichMenu = false;

        
        
        

        $timeRich1 = false;
        $timeRich2 = false;

        $date = date("m/d/Y");

        $config_flag = false; //เริ่มต้น
        $check_rich = ConfigAT::where('_id', $countId)->get();

        if ($check_rich[0]['richmenu_login'] == '' || $check_rich[0]['richmenu_student'] == '' || $check_rich[0]['richmenu_personnal'] == '') {
            $config_flag = true;
        } else {
            $r1 = $check_rich[0]['richmenu_login'];
            $r2 = $check_rich[0]['richmenu_student'];
            $r3 = $check_rich[0]['richmenu_personnal'];
            Config::set('linebot.RICHMENU_LOGIN', $r1);
            Config::set('linebot.RICHMENU_STUDENT', $r2);
            Config::set('linebot.RICHMENU_PERSONNAL', $r3);
            $config_flag = false;
        }

        $this->Rich_Login = Config::get('linebot.RICHMENU_LOGIN');
        $this->Rich_Stu = Config::get('linebot.RICHMENU_STUDENT');
        $this->Rich_Personnal = Config::get('linebot.RICHMENU_PERSONNAL');
        $userStudent = array();
        $userPersonnal = array();

        $user = User::where('status', 'ใช้งานอยู่')->get();
        $user1 = json_decode($user);

        $flag = false;

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

        if (empty($time1) && empty($time2)) {
            //ทำทันทีทั้ง stu || per

            if ($config_flag == true) {

                Config::set('linebot.RICHMENU_LOGIN', $login);

                $this->RichDefault($login);
                $name_rl = "เมนูเริ่มต้น";
                $this->updateStatus($name_rl, $login);

                Config::set('linebot.RICHMENU_STUDENT', $student);
                $name_rs = "นิสิต";
                $this->updateStatus($name_rs, $student);

                Config::set('linebot.RICHMENU_PERSONNAL', $personnal);
                $name_rp = "บุคลากร";
                $this->updateStatus($name_rp, $personnal);

                $this->Rich_Login = Config::get('linebot.RICHMENU_LOGIN');
                $this->Rich_Stu = Config::get('linebot.RICHMENU_STUDENT');
                $this->Rich_Personnal = Config::get('linebot.RICHMENU_PERSONNAL');

                $this->update();
            } else {
                //เปลี่ยนจากเดิม
                Config::set('linebot.RICHMENU_LOGIN', $login);
                $this->Rich_Login = Config::get('linebot.RICHMENU_LOGIN');
                $this->RichCancelDefault();
                $this->RichDefault($login);
                $name_rl = "เมนูเริ่มต้น";
                $this->updateStatus($name_rl, $login);

                Config::set('linebot.RICHMENU_STUDENT', $student);
                $this->Rich_Stu = Config::get('linebot.RICHMENU_STUDENT');

                $this->changeRichStu($arrayUser);
                $name_rs = "นิสิต";
                $this->updateStatus($name_rs, $student);

                //student
                // $statusRichStu = $this->changeRichStu($arrayUser);
                // $RichStu = Rich::where('richId', $student )->get();
                //  $RichStuName =  $RichStu[0]['name'];
                // if($statusRichStu == '{}'){
                //     $this->updateStatus("นิสิต", $student);
                //     $this->insertNotify('success',"ริชเมนูของนิสิต เปลี่ยนเป็น $RichStuName เรียบร้อยแล้ว");
                // } else {
                //     $this->insertNotify('fail',"ริชเมนูของนิสิต เปลี่ยนเป็น $RichStuName ไม่สำเร็จ");
                //     $this->updateStatus("ยังไม่ได้ใช้งาน", $student);
                // }

                Config::set('linebot.RICHMENU_PERSONNAL', $personnal);
                $this->Rich_Personnal = Config::get('linebot.RICHMENU_PERSONNAL');

                $this->changeRichPer($arrayPersonnal);
                $name_rp = "บุคลากร";
                $this->updateStatus($name_rp, $personnal);

                //personnal
                // $statusRichPer = $this->changeRichPer($arrayPersonnal);
                // $RichPer = Rich::where('richId', $personnal )->get();
                //  $RichPerName =  $RichPer[0]['name'];
                // if($statusRichPer == '{}'){
                //     $this->updateStatus("บุคลากร", $personnal);
                //     $this->insertNotify('success',"ริชเมนูของนิสิต เปลี่ยนเป็น $RichPerName เรียบร้อยแล้ว");
                // } else {
                //     $this->insertNotify('fail',"ริชเมนูของนิสิต เปลี่ยนเป็น $RichPerName ไม่สำเร็จ");
                //     $this->updateStatus("ยังไม่ได้ใช้งาน", $personnal);
                // }

                $this->Rich_Login = Config::get('linebot.RICHMENU_LOGIN');

                $non = Rich::where('richId', '!=', $login)->where('richId', '!=', $student)->where('richId', '!=', $personnal)->get();
                $non1 = json_decode($non);

                foreach ($non1 as $row) {
                    foreach ($row as $key => $val) {

                        if ($key == "richId") {
                            $nonStatus = array('status' => 'ยังไม่ได้ใช้งาน');
                            $nonId = Rich::where('richId', $val)->where('status', '!=', 'กำหนดเวลาใช้งาน');
                            $nonId->update($nonStatus, ['upsert' => false]);
                        }
                    }
                }
                $this->update();
            }

        } else if (empty($time1) && !empty($time2)) {
            //ทำ stu
        $dateFormat2 = date("d-m-Y", strtotime($time2));
        // $day2 = date("Y-m-d", strtotime($time2));

            Config::set('linebot.RICHMENU_LOGIN', $login);
            $this->Rich_Login = Config::get('linebot.RICHMENU_LOGIN');
            $this->RichCancelDefault();
            $this->RichDefault($login);
            $name_rl = "เมนูเริ่มต้น";
            $this->updateStatus($name_rl, $login);

            Config::set('linebot.RICHMENU_STUDENT', $student);
            $this->Rich_Stu = Config::get('linebot.RICHMENU_STUDENT');

            $this->changeRichStu($arrayUser);
            $name_rs = "นิสิต";
            $this->updateStatus($name_rs, $student);
            
            //student
            // $statusRichStu = $this->changeRichStu($arrayUser);
            // $RichStu = Rich::where('richId', $student )->get();
            //  $RichStuName =  $RichStu[0]['name'];
            // if($statusRichStu == '{}'){
            //     $this->updateStatus("นิสิต", $student);
            //     $this->insertNotify('success',"ริชเมนูของนิสิต เปลี่ยนเป็น $RichStuName เรียบร้อยแล้ว");
            // } else {
            //     $this->insertNotify('fail',"ริชเมนูของนิสิต เปลี่ยนเป็น $RichStuName ไม่สำเร็จ");
            //     $this->updateStatus("ยังไม่ได้ใช้งาน", $student);
            // }

            if ($dateDays == $strFormatPer) {
                Config::set('linebot.RICHMENU_PERSONNAL', $personnal);
                $this->Rich_Personnal = Config::get('linebot.RICHMENU_PERSONNAL');
                $this->changeRichPer($arrayPersonnal);
                $name_rp = "บุคลากร";
                $this->updateStatus($name_rp, $personnal);
            } else {
                //ตั้งเวลา
                $this->Rich_Personnal = Config::get('linebot.RICHMENU_PERSONNAL');
                $strFormat2 = str_replace("-", "/", $dateFormat2);
                $this->setTimeRich($strFormat2, 'บุคลากร', $personnal);
                $name_rp = "กำหนดเวลาใช้งาน";
                $this->updateStatus($name_rp, $personnal);
            }

            $this->Rich_Login = Config::get('linebot.RICHMENU_LOGIN');
            $non = Rich::where('richId', '!=', $login)->where('richId', '!=', $student)->where('richId', '!=', $personnal)->whereNull('timeRich')->get();
            $non1 = json_decode($non);

            foreach ($non1 as $row) {
                foreach ($row as $key => $val) {
                    if ($key == "richId") {
                        $nonStatus = array('status' => 'ยังไม่ได้ใช้งาน');
                        $nonId = Rich::where('richId', $val)->where('status', '!=', 'กำหนดเวลาใช้งาน');
                        $nonId->update($nonStatus, ['upsert' => false]);
                    }
                }
            }

            $nonStatusPer = array('status' => 'บุคลากร');
            $perId = $check_rich[0]['richmenu_personnal'];
            $nonIdPer = Rich::where('richId', $perId);
            $nonIdPer->update($nonStatus, ['upsert' => false]);

            $this->update();

        } else if (empty($time2) && !empty($time1)) {
            //ทำ per
            $dateFormat1 = date("d-m-Y", strtotime($time1));
        // $day1 = date("Y-m-d", strtotime($time1));

            Config::set('linebot.RICHMENU_LOGIN', $login);
            $this->Rich_Login = Config::get('linebot.RICHMENU_LOGIN');
            $this->RichCancelDefault();
            $this->RichDefault($login);
            $name_rl = "เมนูเริ่มต้น";
            $this->updateStatus($name_rl, $login);

            if ($dateDays == $strFormatStu) {
                Config::set('linebot.RICHMENU_STUDENT', $student);
                $this->Rich_Stu = Config::get('linebot.RICHMENU_STUDENT');
                $this->changeRichStu($arrayUser);
                $name_rs = "นิสิต";
                $this->updateStatus($name_rs, $student);
            } else {
                //ตั้งเวลา
                $this->Rich_Stu = Config::get('linebot.RICHMENU_STUDENT');
                $strFormat1 = str_replace("-", "/", $dateFormat1);
                $this->setTimeRich($strFormat1, 'นิสิต', $student);
                $name_rs = "กำหนดเวลาใช้งาน";
                $this->updateStatus($name_rs, $student);
            }

            Config::set('linebot.RICHMENU_PERSONNAL', $personnal);
            $this->Rich_Personnal = Config::get('linebot.RICHMENU_PERSONNAL');

            $this->changeRichPer($arrayPersonnal);
            $name_rp = "บุคลากร";
            $this->updateStatus($name_rp, $personnal);


            $this->Rich_Login = Config::get('linebot.RICHMENU_LOGIN');
            $non = Rich::where('richId', '!=', $login)->where('richId', '!=', $student)->where('richId', '!=', $personnal)->get();
            $non1 = json_decode($non);

            foreach ($non1 as $row) {
                foreach ($row as $key => $val) {
                    if ($key == "richId") {

                        $nonStatus = array('status' => 'ยังไม่ได้ใช้งาน');
                        $nonId = Rich::where('richId', $val)->where('status', '!=', 'กำหนดเวลาใช้งาน');
                        $nonId->update($nonStatus, ['upsert' => false]);
                    }
                }
            }

            $nonStatusStu = array('status' => 'นิสิต');
            $stuId = $check_rich[0]['richmenu_student'];
            $nonIdStu = Rich::where('richId', $stuId);
            $nonIdStu->update($nonStatusStu, ['upsert' => false]);

            $this->update();
        } else if (!empty($time1) && !empty($time2)) {
            //ทำเวลา stu || per
            $dateFormat1 = date("d-m-Y", strtotime($time1));
        // $day1 = date("Y-m-d", strtotime($time1));

        $dateFormat2 = date("d-m-Y", strtotime($time2));
        // $day2 = date("Y-m-d", strtotime($time2));

            Config::set('linebot.RICHMENU_LOGIN', $login);
            $this->Rich_Login = Config::get('linebot.RICHMENU_LOGIN');
            $this->RichCancelDefault();
            $this->RichDefault($login);
            $name_rl = "เมนูเริ่มต้น";
            $this->updateStatus($name_rl, $login);

            //ตั้งเวลา
            $this->Rich_Stu = Config::get('linebot.RICHMENU_STUDENT');
            $strFormat1 = str_replace("-", "/", $dateFormat1);
            $this->setTimeRich($strFormat1, 'นิสิต', $student);
            $name_rs = "กำหนดเวลาใช้งาน";
            $this->updateStatus($name_rs, $student);
            // $this->insertNotify('success',"ริชเมนูของนิสิต เปลี่ยนเป็น $row->name เรียบร้อยแล้ว");

            //ตั้งเวลา
            $this->Rich_Personnal = Config::get('linebot.RICHMENU_PERSONNAL');
            $strFormat2 = str_replace("-", "/", $dateFormat2);
            $this->setTimeRich($strFormat2, 'บุคลากร', $personnal);
            $name_rp = "กำหนดเวลาใช้งาน";
            $this->updateStatus($name_rp, $personnal);
            // $this->insertNotify('success',"ริชเมนูของบุคลากร เปลี่ยนเป็น $row->name เรียบร้อยแล้ว");

            $this->Rich_Login = Config::get('linebot.RICHMENU_LOGIN');
            $non = Rich::where('richId', '!=', $login)->where('richId', '!=', $student)->where('richId', '!=', $personnal)->where('timeRich', '!=', null)->where('timeType', '!=', null)->get();
            $non1 = json_decode($non);

            foreach ($non1 as $row) {
                foreach ($row as $key => $val) {
                    if ($key == "richId") {
                        $nonStatus = array('status' => 'ยังไม่ได้ใช้งาน');
                        $nonId = Rich::where('richId', $val)->where('status', '!=', 'กำหนดเวลาใช้งาน');
                        $nonId->update($nonStatus, ['upsert' => false]);
                    }
                }
            }

            $nonStatusStu = array('status' => 'นิสิต');
            $stuId = $check_rich[0]['richmenu_student'];
            $nonIdStu = Rich::where('richId', $stuId);
            $nonIdStu->update($nonStatusStu, ['upsert' => false]);

            $nonStatusPer = array('status' => 'บุคลากร');
            $perId = $check_rich[0]['richmenu_personnal'];
            $nonIdPer = Rich::where('richId', $perId);
            $nonIdPer->update($nonStatusPer, ['upsert' => false]);

            $this->update();

        }
        // Rich::whereNull('richId')->delete();

        redirect('/main/Richdata')->withSuccess('ใช้งานริชเมนูเรียบร้อย');

    }

    public function update()
    {
        $get = array('richmenu_login' => $this->Rich_Login, 'richmenu_student' => $this->Rich_Stu, 'richmenu_personnal' => $this->Rich_Personnal);
        $up = ConfigAT::where('_id', 1);
        $up->update($get, ['upsert' => false]);
    }

    public function updateStatus($name, $id)
    {
        $get = array('status' => $name);
        $up = Rich::where('richId', $id);
        $up->update($get, ['upsert' => false]);
    }

    public function setTimeRich($date, $type, $id)
    {
        $get = array('timeRich' => $date, 'timeType' => $type);
        $up = Rich::where('richId', $id);
        $up->update($get, ['upsert' => false]);
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

    public function changeRichStu($arrUser)
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
                "userIds" => $arrUser,
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

        return $response;
    }

    public function changeRichPer($arrayPersonnal)
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
                "userIds" => $arrayPersonnal,
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

        return $response;
    }

    public function Out_rich($uid)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.line.me/v2/bot/richmenu/bulk/unlink",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(array(
                "userIds" => $uid,
            )),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer $this->access_token",
                "cache-control: no-cache",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    public function RichDefault($login)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.line.me/v2/bot/user/all/richmenu/$login",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => "",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Connection: Keep-Alive",
                "Content-type: application/x-www-form-urlencoded;charset=UTF-8",
                "Authorization: Bearer $this->access_token",
                "Accept-Encoding: gzip, deflate, br",
                "Cache-Control: no-cache",
                "User-Agent: PostmanRuntime/7.24.0",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    public function RichCancelDefault()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.line.me/v2/bot/user/all/richmenu",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->access_token",
                "cache-control: no-cache",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    public function DeleteRich(Request $req)
    {
        $id = $req->input('richId');
        $non = ConfigAT::where('_id', 1)->get();
        if ($id != $non[0]['richmenu_login'] && $id != $non[0]['richmenu_student'] && $id != $non[0]['richmenu_personnal']) {
            Rich::where('richId', $id)->delete();
            $this->RemoveRich($id);
            redirect()->back()->withSuccess('ลบริชเมนูเรียบร้อย');
        } else {
            redirect()->back()->withError('ริชเมนูนี้ใช้งานอยู่ ไม่สามารถลบได้');
        }
    }

    public function CheckTime($day1, $day2)
    {
        $datetime = new DateTime();
        $datetime1 = new DateTime($day1);
        $datetime2 = new DateTime($day2);
        $difference1 = $datetime->diff($datetime1);
        $difference2 = $datetime->diff($datetime2);

        if ($difference1->invert == 1 || $difference2->invert == 1) {
            if ($difference1->days == 0 || $difference2->days == 0) {
                //continue
            } else {
                $arrayPostData['to'] = "Ude92fc5b523ebbfa80a5738ef6cbd495";
                $arrayPostData['messages'][0]['type'] = "text";
                $arrayPostData['messages'][0]['text'] = "use : เข้า ";
                $this->pushMsg($arrayPostData);
                return redirect('/main/Richdata')->with('msg', "กรุณากำหนดเวลาใหม่");
            }
        } else {
            //continue
        }
    }

    public function RemoveRich($richid)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.line.me/v2/bot/richmenu/$richid",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Connection: Keep-Alive",
                "Content-type: application/x-www-form-urlencoded;charset=UTF-8",
                "Authorization: Bearer $this->access_token",
                "Accept-Encoding: gzip, deflate, br",
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    public function updateStatusAfterCancel($id)
    {
        $get = array('status' => 'ยังไม่ได้ใช้งาน', 'timeRich' => '-', 'timeType' => '-');
        $up = Rich::where('richId', $id);
        $up->update($get, ['upsert' => false]);
    }

    public function checkImg($width, $height, $filename)
    {
        $err_img = [];
        if ($width != 2500 && $height != 1686) {
            File::delete('images/' . $filename);
            $err_img = ['msg' => 'ขนาดของรูปไม่ถูกต้อง กรุณาเลือกไฟล์รูปใหม่'];
            return response()->json(['error' => $validator->errors()->all(), 'error_ceateImg' => $err_img]);
        }

        if ($width != 2500 && $height != 843) {
            File::delete('images/' . $filename);
            $err_img = ['msg' => 'ขนาดของรูปไม่ถูกต้อง กรุณาเลือกไฟล์รูปใหม่'];
            return response()->json(['error' => $validator->errors()->all(), 'error_ceateImg' => $err_img]);
        }

        if ($width != 1200 && $height != 810) {
            File::delete('images/' . $filename);
            $err_img = ['msg' => 'ขนาดของรูปไม่ถูกต้อง กรุณาเลือกไฟล์รูปใหม่'];
            return response()->json(['error' => $validator->errors()->all(), 'error_ceateImg' => $err_img]);
        }

        if ($width != 1200 && $height != 405) {
            File::delete('images/' . $filename);
            $err_img = ['msg' => 'ขนาดของรูปไม่ถูกต้อง กรุณาเลือกไฟล์รูปใหม่'];
            return response()->json(['error' => $validator->errors()->all(), 'error_ceateImg' => $err_img]);
        }

        if ($width != 800 && $height != 540) {
            File::delete('images/' . $filename);
            $err_img = ['msg' => 'ขนาดของรูปไม่ถูกต้อง กรุณาเลือกไฟล์รูปใหม่'];
            return response()->json(['error' => $validator->errors()->all(), 'error_ceateImg' => $err_img]);
        }

        if ($width != 800 && $height != 270) {
            File::delete('images/' . $filename);
            $err_img = ['msg' => 'ขนาดของรูปไม่ถูกต้อง กรุณาเลือกไฟล์รูปใหม่'];
            return response()->json(['error' => $validator->errors()->all(), 'error_ceateImg' => $err_img]);
        } else {
            //
        }
    }

    public function DeleteNotification(Request $req)
    {
        return Notification::whereNotNull('status')->delete();
        // redirect()->back();
    }

    public function insertNotify($status, $detail)
    {
        $data = new Notification;
        $data->detail = $detail;
        $data->status = $status;
        $data->save();
        return "Success";
    }
}
