<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Config;
use DateTime;
use File;
use Illuminate\Http\Request;
use \App\ConfigAT;
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

    public function CreateRichmenu(Request $req)
    {
        $countId = ConfigAT::count();
        $richAll = ConfigAT::where('_id', $countId)->get();

        $input = $req->all();
        $this->access_token = $richAll[0]['channelAccessToken'];
        $this->channelSecret = $richAll[0]['channelSecret'];
        $secret = $richAll[0]['channelSecret'];
        // var_dump($input);

        $json = $req->input('json');
        $file = $req->file('file');
        $name = $req->input('name');

        $filename = $file->getClientOriginalName();
        $location = 'images';
        $file->move($location, $filename);

        $pa = $file->getClientOriginalName();

        $output = public_path("images\\" . $pa);

        //finish ---create Rich
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
        $richid = $you['richMenuId'];

        echo $you['richMenuId'];
        $this->insertRich($richid, $name);

        //finish ---upload img Rich
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient("$this->access_token");
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $secret]);
        $imagePath = $output;
        $contentType = 'image/png';
        $response = $bot->uploadRichMenuImage($richid, $imagePath, $contentType);

        File::delete('images/' . $filename);

        return redirect('/main/Richdata')->withSuccess('เพิ่มริชเมนูเรียบร้อย');
    }

    public function insertRich($richid, $nameRich)
    {
        $data = new Rich;
        $data->name = $nameRich;
        $data->status = 'ยังไม่ได้ใช้งาน';
        $data->richId = $richid;
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

        $dateFormat1 = date("d-m-Y", strtotime($time1));
        $dateFormat2 = date("d-m-Y", strtotime($time2));

        $day1 = date("Y-m-d", strtotime($time1));
        $day2 = date("Y-m-d", strtotime($time2));

        $datetime = new DateTime();
        $datetime1 = new DateTime($day1);
        $datetime2 = new DateTime($day2);
        $difference1 = $datetime->diff($datetime1);
        $difference2 = $datetime->diff($datetime2);
        $checkTime = false;
        $useRichMenu = false;

        if (!empty($time1) || !empty($time2)) {
            if ($difference1->invert == 1 || $difference2->invert == 1) {
                if ($difference1->days == 0 || $difference2->days == 0) {
                    //continue
                } else {
                    return response('failed', 500);
                }
            } else {
                //continue
            }
        } else {
            //
        }

        $dateDays = date("d-m-Y");
        $strFormatStu = str_replace("-", "/", $dateFormat1);
        $strFormatPer = str_replace("-", "/", $dateFormat2);

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
                            $nonId = Rich::where('richId', $val);
                            $nonId->update($nonStatus, ['upsert' => true]);
                        }
                    }
                }
                $this->update();
            }

        } else if (empty($time1) && !empty($time2)) {
            //ทำ stu

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
                $name_rp = "บุคลากร(กำหนดเวลาใช้งาน $strFormat2)";
                $this->updateStatus($name_rp, $personnal);
            }

            $this->Rich_Login = Config::get('linebot.RICHMENU_LOGIN');
            $non = Rich::where('richId', '!=', $login)->where('richId', '!=', $student)->where('richId', '!=', $personnal)->get();
            $non1 = json_decode($non);

            foreach ($non1 as $row) {
                foreach ($row as $key => $val) {
                    if ($key == "richId") {
                        $nonStatus = array('status' => 'ยังไม่ได้ใช้งาน');
                        $nonId = Rich::where('richId', $val);
                        $nonId->update($nonStatus, ['upsert' => true]);
                    }
                }
            }

            $nonStatusPer = array('status' => 'บุคลากร');
            $perId = $check_rich[0]['richmenu_personnal'];
            $nonIdPer = Rich::where('richId', $perId);
            $nonIdPer->update($nonStatus, ['upsert' => true]);

            $this->update();

        } else if (empty($time2) && !empty($time1)) {
            //ทำ per

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
                $name_rs = "นิสิต(กำหนดเวลาใช้งาน $strFormat1)";
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
                        $nonId = Rich::where('richId', $val);
                        $nonId->update($nonStatus, ['upsert' => true]);
                    }
                }
            }

            $nonStatusStu = array('status' => 'นิสิต');
            $stuId = $check_rich[0]['richmenu_student'];
            $nonIdStu = Rich::where('richId', $stuId);
            $nonIdStu->update($nonStatusStu, ['upsert' => true]);

            $this->update();
        } else if (!empty($time1) && !empty($time2)) {
            //ทำเวลา stu || per

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
            $name_rs = "นิสิต(กำหนดเวลาใช้งาน $strFormat1)";
            $this->updateStatus($name_rs, $student);

            //ตั้งเวลา
            $this->Rich_Personnal = Config::get('linebot.RICHMENU_PERSONNAL');
            $strFormat2 = str_replace("-", "/", $dateFormat2);
            $this->setTimeRich($strFormat2, 'บุคลากร', $personnal);
            $name_rp = "บุคลากร(กำหนดเวลาใช้งาน $strFormat2)";
            $this->updateStatus($name_rp, $personnal);

            $this->Rich_Login = Config::get('linebot.RICHMENU_LOGIN');
            $non = Rich::where('richId', '!=', $login)->where('richId', '!=', $student)->where('richId', '!=', $personnal)->get();
            $non1 = json_decode($non);

            foreach ($non1 as $row) {
                foreach ($row as $key => $val) {
                    if ($key == "richId") {
                        $nonStatus = array('status' => 'ยังไม่ได้ใช้งาน');
                        $nonId = Rich::where('richId', $val);
                        $nonId->update($nonStatus, ['upsert' => true]);
                    }
                }
            }

            $nonStatusStu = array('status' => 'นิสิต');
            $stuId = $check_rich[0]['richmenu_student'];
            $nonIdStu = Rich::where('richId', $stuId);
            $nonIdStu->update($nonStatusStu, ['upsert' => true]);

            $nonStatusPer = array('status' => 'บุคลากร');
            $perId = $check_rich[0]['richmenu_personnal'];
            $nonIdPer = Rich::where('richId', $perId);
            $nonIdPer->update($nonStatusPer, ['upsert' => true]);

            $this->update();

        }
        Rich::whereNull('richId')->delete();

        redirect('/main/Richdata')->withSuccess('ใช้งานริชเมนูเรียบร้อย');
    }

    public function update()
    {
        $get = array('richmenu_login' => $this->Rich_Login, 'richmenu_student' => $this->Rich_Stu, 'richmenu_personnal' => $this->Rich_Personnal);
        $up = ConfigAT::where('_id', 1);
        $up->update($get, ['upsert' => true]);
    }

    public function updateStatus($name, $id)
    {
        $get = array('status' => $name);
        $up = Rich::where('richId', $id);
        $up->update($get, ['upsert' => true]);
    }

    public function setTimeRich($date, $type, $id)
    {
        $get = array('timeRich' => $date, 'timeType' => $type);
        $up = Rich::where('richId', $id);
        $up->update($get, ['upsert' => true]);
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

    public function DeleteRich($id)
    {
        $non = ConfigAT::where('_id', 1)->get();
        if ($id != $non[0]['richmenu_login'] && $id != $non[0]['richmenu_student'] && $id != $non[0]['richmenu_personnal']) {
            Rich::where('richId', $id)->delete();
            redirect('/main/Richdata')->withSuccess('ลบริชเมนูเรียบร้อย');
        } else {
            redirect('/main/Richdata')->withError('ริชเมนูนี้ใช้งานอยู่ ไม่สามารถลบได้');
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

}
