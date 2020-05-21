<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use \App\ConfigAT;
use \App\Rich;
use \App\User;
use \App\Notification;

class daily extends Command
{
    private $access_token;
    public $Rich_Stu;
    public $Rich_Personnal;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use richmenu set time';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        \Log::info('working : rich' . date("d/m/Y"));

        $check_rich = ConfigAT::where('_id', 1)->get();

        $this->access_token = $check_rich[0]['channelAccessToken'];
        $this->Rich_Stu = $check_rich[0]['richmenu_student'];
        $this->Rich_Personnal = $check_rich[0]['richmenu_personnal'];

        $student = $check_rich[0]['richmenu_student'];
        $personnal = $check_rich[0]['richmenu_personnal'];

        $rich = Rich::whereNotnull('timeRich')->get();
        $date = date("d/m/Y");

        $rich_de = json_decode($rich);

        $userStudent = array();
        $userPersonnal = array();

        $richStu = false;
        $richPer = false;

        $user = User::where('status', 'ใช้งานอยู่')->get();
        $user1 = json_decode($user);

        $flag = false;

        if (count($rich_de) == 0) {
            //
        } else {
            foreach ($rich_de as $row) {
                foreach ($row as $key => $valRich) {
                    if ($key == "richId") {
                        if ($date == $row->timeRich) {

                            foreach ($user1 as $rowUser) {
                                foreach ($rowUser as $key => $val) {
                                    if ($key == "username") {
                                        if (is_numeric($val)) {
                                            $userStudent[] = $rowUser->userId;
                                        }if (!is_numeric($val)) {
                                            $userPersonnal[] = $rowUser->userId;
                                        }
                                    }
                                }
                            }

                            $arrayUser = array_values($userStudent);
                            $arrayPersonnal = array_values($userPersonnal);

                            if ($row->timeType == 'นิสิต') {
                                $this->DisableRich('ยังไม่ได้ใช้งาน', $student);
                                $this->changeRichStu($arrayUser, $valRich);
                                $this->updateStatus("นิสิต", $valRich);
                                $this->updateStu($valRich);

                                
                                //notify
                                $check_config = ConfigAT::where('_id', 1)->get();
                                $stuR = $check_config[0]['richmenu_student'];
                                if($stuR == $valRich){
                                    $this->insertNotify('success',"ริชเมนูของนิสิต เปลี่ยนเป็น $row->name เรียบร้อยแล้ว");
                                    $richStu = true;
                                } else {
                                    $this->insertNotify('fail',"ริชเมนูของนิสิต เปลี่ยนเป็น $row->name ไม่สำเร็จ");
                                    $this->updateStatus("ยังไม่ได้ใช้งาน", $valRich);
                                }

                            } else if ($row->timeType == 'บุคลากร') {
                                $this->DisableRich('ยังไม่ได้ใช้งาน', $personnal);
                                $this->changeRichPer($arrayPersonnal, $valRich);
                                $this->updateStatus("บุคลากร", $valRich);
                                $this->updatePer($valRich);

                                //notify
                                $check_config = ConfigAT::where('_id', 1)->get();
                                $perR = $check_config[0]['richmenu_personnal'];
                                if($perR == $valRich){
                                    $this->insertNotify('success',"ริชเมนูของบุคลากร เปลี่ยนเป็น $row->name เรียบร้อยแล้ว");
                                    $richPer = true;
                                } else {
                                    $this->insertNotify('fail',"ริชเมนูของบุคลากร เปลี่ยนเป็น $row->name ไม่สำเร็จ");
                                    $this->updateStatus("ยังไม่ได้ใช้งาน", $valRich);
                                }

                            }
                        } else {
                            //
                        }
                    } else {
                        //
                    }
                }
            }
        }

        $expire = User::where('status', 'ใช้งานอยู่')->get();
        $json_expire = json_decode($expire);

        foreach ($json_expire as $row) {
            foreach ($row as $key => $val) {
                if ($key == 'username') {
                    $userName = User::where('username', $val)->get();
                    $un = $userName[0]['username'];
                    $uid = $userName[0]['userId'];
                    $timePass = $userName[0]['password_expire'];
                    $day = $this->pass_expire($timePass);

                    if (is_numeric($un)) {
                        if ($day <= 30) {
                            $this->pushTimePass($uid, $timePass);
                            $text = $this->encodeDataChw($uid, $un);
                            $url = "https://myid.buu.ac.th/chgpwdLine/" . $text;
                            $this->pushFlex($uid, $url);
                            $arrayPostData['to'] = $uid;
                            $arrayPostData['messages'][0]['type'] = "text";
                            $arrayPostData['messages'][0]['text'] = "คำเตือน!! รหัสของคุณใกล้หมดอายุ กรุณาเปลี่ยนรหัสผ่าน";
                            $this->pushMsg($arrayPostData);
                        } else {
                            // $this->pushTimePass($uid, $timePass);
                        }
                    } else {
                    }
                }
            }
        }

        Rich::whereNull('name')->delete();

        echo "use Rich Finish";
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

    public function DisableRich($name, $id)
    {
        $get = array('status' => $name, 'timeRich' => '', 'timeType' => '');
        $up = Rich::where('richId', $id);
        $up->update($get, ['upsert' => false]);
    }

    public function updateStatus($name, $id)
    {
        $get = array('status' => $name, 'timeRich' => '-', 'timeType' => '-');
        $up = Rich::where('richId', $id);
        $up->update($get, ['upsert' => false]);
    }

    public function updateStu($Stu)
    {
        $get = array('richmenu_student' => $Stu);
        $up = ConfigAT::where('_id', 1);
        $up->update($get, ['upsert' => false]);
    }

    public function updatePer($Per)
    {
        $get = array('richmenu_personnal' => $Per);
        $up = ConfigAT::where('_id', 1);
        $up->update($get, ['upsert' => false]);
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

    public function changeRichPer($arrayPersonnal, $per)
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
                "richMenuId" => "$per",
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

    public function changeRichStu($arrUser, $stu)
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
                "richMenuId" => "$stu",
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

    public function notificationExpire()
    {
        $expire = User::where('userId', $userId)->where('status', 'ใช้งานอยู่')->get();
        $json_expire = json_decode($expire);

        $timePass = null;

        foreach ($find as $row) {
            foreach ($row as $key => $val) {
                if ($key == 'username') {
                    $un = $val;
                    // $un = $row->username;
                    $uid = $row->userId;
                    if (is_numeric($val)) {
                        $timePass = $expire[0]['password_expire'];
                        $day = $this->pass_expire($timePass);
                        if ($day <= 60) {
                            $this->pushTimePass($uid, $timePass);
                            $agent->reply("รหัสของคุณจะหมดอายุใน $day วัน หากคุณต้องการเปลี่ยนรหัสผ่าน กรุณากดปุ่มเพื่อเปลี่ยนหรัสผ่าน");
                            $text = $this->encodeDataChw($uid, $un);
                            $url = "https://myid.buu.ac.th/chgpwdLine/" . $text;
                            $this->pushFlex($uid, $url);
                        } else {
                            $this->pushTimePass($uid, $timePass);
                        }
                    } else {
                    }
                }
            }
        }

    }
    public function pushTimePass($to, $time)
    {
        $expire = $this->pass_expire($time);

        $flex = ["type" => "flex",
            "altText" => "ผลการเรียน",
            "contents" => [
                "type" => "carousel",
                "contents" => [
                    [
                        "type" => "bubble",
                        "size" => "kilo",
                        "direction" => "ltr",
                        "header" => [
                            "type" => "box",
                            "layout" => "vertical",
                            "contents" => [
                                [
                                    "type" => "text",
                                    "text" => "รหัสผ่านจะหมดอายุภายใน",
                                    "size" => "md",
                                    "align" => "center",
                                    "weight" => "bold",
                                    "color" => "#000000",
                                ],
                                [
                                    "type" => "text",
                                    "text" => "$expire days",
                                    "size" => "md",
                                    "align" => "center",
                                    "weight" => "bold",
                                    "color" => "#FF0000",
                                ],
                                [
                                    "type" => "text",
                                    "text" => "———",
                                    "size" => "md",
                                    "align" => "center",
                                    "weight" => "bold",
                                    "color" => "#000000",
                                ],
                            ],
                        ],
                        "styles" => [
                            "header" => [
                                "backgroundColor" => "#F8F6D5",
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $data = [
            'to' => $to,
            'messages' => [$flex],
        ];

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
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function pass_expire($datetime)
    {
        $datetime1 = new DateTime();
        // $datetime2 = new DateTime('2020-08-02 15:04:53');
        $datetime2 = new DateTime($datetime);
        $interval = $datetime1->diff($datetime2);
        $days = $interval->format('%R%a');
        $getDays = str_replace("+", "", $days);
        return $getDays;
    }

    public function encodeDataChw($userId, $username)
    {
        $digit = date('d') % 4 + 7;

        $ref = $this->getRefChw($digit);
        $str = $this->getRefChw($digit) . $ref . $this->getRefChw($digit + 1) . "|$userId|$username|" . $ref;
        return base64_encode($str);
    }

    public function getRefChw($length)
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

    public function pushFlex($to, $url)
    {
        $flex = ["type" => "flex",
            "altText" => "Flex Message",
            "contents" => [
                "type" => "carousel",
                "contents" => [
                    [
                        "type" => "bubble",
                        "direction" => "ltr",
                        "hero" => [
                            "type" => "image",
                            "url" => "https://i.imgur.com/9dxKDyr.png",
                            "size" => "full",
                            "aspectRatio" => "16:9",
                            "aspectMode" => "fit",
                        ],
                        "footer" => [
                            "type" => "box",
                            "layout" => "horizontal",
                            "contents" => [
                                [
                                    "type" => "button",
                                    "action" => [
                                        "type" => "uri",
                                        "label" => "เปลี่ยนรหัสผ่าน",
                                        "uri" => "$url",
                                    ],
                                    "style" => "primary",
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $data = [
            'to' => $to,
            'messages' => [$flex],
        ];

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
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function insertNotify($status,$detail)
    {
        $data = new Notification;
        $data->detail = $detail;
        $data->status = $status;
        $data->save();
        return "Success";
    }
}
