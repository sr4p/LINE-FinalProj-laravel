<?php

namespace App\Http\Controllers;

use Config;
use DateTime;
use Dialogflow\WebhookClient;
use Illuminate\Http\Request;
use \App\ConfigAT;
use \App\User;

// use \MongoDB\BSON;

class line_fulliment extends Controller
{
    private $access_token;

    public function handle(Request $request)
    {
        $countId = ConfigAT::count();
        $richAll = ConfigAT::where('_id', $countId)->get();

        $ac_token = $richAll[0]['channelAccessToken'];
        $ac_secret = $richAll[0]['channelSecret'];

        Config::set('linebot.ACCESS_TOKEN', $ac_token);
        Config::set('linebot.CHANNEL_SECRET', $ac_secret);

        $this->access_token = Config::get("linebot.ACCESS_TOKEN");
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
        $intent = $agent->getIntent();
        $userId = $request['originalDetectIntentRequest']['payload']['data']['source']['userId'];

        $today = date("d/m/Y H:i:s");
        $userName = User::where('userId', $userId)->where('status', 'ใช้งานอยู่')->get();

        $find = json_decode($userName);
        $teach = false;
        $flag = false;
        $un = null;
        $sta = null;
        $timePass = null;
        foreach ($find as $row) {
            foreach ($row as $key => $val) {
                if ($key == 'username') {
                    $un = $val;
                    if (is_numeric($val)) {
                        $teach = true;
                        $sta = 'นิสิต';
                        $timePass = $userName[0]['password_expire'];
                    } else {
                        $teach = false;
                        $sta = 'บุคลากร';
                        $timePass = $userName[0]['password_expire'];
                    }
                }if ($key == 'status' && $val == 'ใช้งานอยู่') {
                    $flag = true;

                }
            }
        }
        //intent chatbot

        if ($flag == true) {
            if ('tttt' == $intent) {
                $agent->reply("TEST Intent => UserId Line : [$userId]");
                $agent->reply(" Status : [$sta]");
            } else if ('grade' == $intent) {
                $us = $userName[0]['username'];
                $grade = $this->getGradeByid($us);
                $arr = json_decode((string) $grade);
                $gpax = $arr->gpax;
                $format_gpax = number_format((float) $gpax, 2, '.', '');
                $this->pushGradeStu($userId, $format_gpax);
            } else if ('changePassword' == $intent) {
                $text = $this->encodeDataChw($userId, $un);
                $url = "https://myid.buu.ac.th/chgpwdLine/" . $text;
                $this->pushFlexPassword($userId, $url);
            } else if ('password expire' == $intent) {
                $day = $this->pass_expire($timePass);
                if ($day <= 30) {
                    $this->pushTimePass($userId, $timePass);
                    $agent->reply("รหัสของคุณจะหมดอายุใน $day วัน หากคุณต้องการเปลี่ยนรหัสผ่าน กรุณากดปุ่ม 'เปลี่ยนหรัสผ่าน'");
                    $text = $this->encodeDataChw($userId, $un);
                    $url = "https://myid.buu.ac.th/chgpwdLine/" . $text;
                    $this->pushFlex($userId, $url);
                } else {
                    $this->pushTimePass($userId, $timePass);
                }
            } else if ('money' == $intent) {
                $this->pushCostStu($userId);
            } else if ('logout-info-yes' == $intent) {
                $this->logout($userId);
                $this->outAccount($userId, $un);
                $agent->reply('ออกจากระบบเรียบร้อย');
            }
            return response()->json($agent->render());
        } else {
            $agent->reply('กรุณาเข้าสู่ระบบ');
            return response()->json($agent->render());
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

    public function encodeData($userId, $username)
    {
        $digit = date('d') % 5 + 6;

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

    public function outAccount($userid, $username)
    {
        $text = $this->encodeData($userid, $username);
        $url = "https://myid.buu.ac.th/lineAccountRM/" . $text;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);
        // print_r($obj);
    }

    //ChwPass
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

    public function pushGradeStu($to, $grade)
    {
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
                                    "text" => "ผลการเรียนเฉลี่ย",
                                    "size" => "md",
                                    "align" => "center",
                                    "weight" => "bold",
                                    "color" => "#000000",
                                ],
                                [
                                    "type" => "text",
                                    "text" => "$grade",
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

    public function getGradeByid($uid)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://myid.buu.ac.th/grade.php?student=$uid",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Cookie: A10_slb-20480=EJABAFAKFAAA",
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function pushCostStu($to)
    {
        $flex = ["type" => "flex",
            "altText" => "ภาระค่าใช้จ่าย",
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
                                    "text" => "ค่าลงทะเบียน",
                                    "size" => "md",
                                    "align" => "center",
                                    "weight" => "bold",
                                    "color" => "#000000",
                                ],
                                [
                                    "type" => "text",
                                    "text" => "2500 บาท",
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
                                [
                                    "type" => "text",
                                    "text" => "ค่าปรับหอสมุด",
                                    "size" => "md",
                                    "align" => "center",
                                    "weight" => "bold",
                                    "color" => "#000000",
                                ],
                                [
                                    "type" => "text",
                                    "text" => "ไม่พบยอดค้างชำระ",
                                    "size" => "md",
                                    "align" => "center",
                                    "weight" => "bold",
                                    "color" => "#E2C47B",
                                ],
                                [
                                    "type" => "text",
                                    "text" => "———",
                                    "size" => "md",
                                    "align" => "center",
                                    "weight" => "bold",
                                    "color" => "#000000",
                                ],
                                [
                                    "type" => "text",
                                    "text" => "ค่าเช่าหอพัก",
                                    "size" => "md",
                                    "align" => "center",
                                    "weight" => "bold",
                                    "color" => "#000000",
                                ],
                                [
                                    "type" => "text",
                                    "text" => "ไม่พบยอดค้างชำระ",
                                    "size" => "md",
                                    "align" => "center",
                                    "weight" => "bold",
                                    "color" => "#E2C47B",
                                ],
                                [
                                    "type" => "text",
                                    "text" => "———",
                                    "size" => "md",
                                    "align" => "center",
                                    "weight" => "bold",
                                    "color" => "#000000",
                                ],
                                [
                                    "type" => "text",
                                    "text" => "ค่าปรับอื่นๆ",
                                    "size" => "md",
                                    "align" => "center",
                                    "weight" => "bold",
                                    "color" => "#000000",
                                ],
                                [
                                    "type" => "text",
                                    "text" => "ไม่พบยอดค้างชำระ",
                                    "size" => "md",
                                    "align" => "center",
                                    "weight" => "bold",
                                    "color" => "#E2C47B",
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

    public function pushFlexPassword($to, $url)
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
                            "url" => "https://i.imgur.com/zp2tOlV.png",
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
                                        "type" => "message",
                                        "label" => "เช็ควันหมดอายุรหัสผ่าน",
                                        "text" => "เช็ควันหมดอายุรหัสผ่าน",
                                    ],
                                    "style" => "primary",
                                ],
                            ],
                        ],
                    ],
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
                            // $this->pushTimePass($uid, $timePass);
                            $agent->reply("รหัสของคุณจะหมดอายุใน $day วัน หากคุณต้องการเปลี่ยนรหัสผ่าน กรุณากดปุ่มเพื่อเปลี่ยนหรัสผ่าน");
                            $text = $this->encodeDataChw($uid, $un);
                            $url = "https://myid.buu.ac.th/chgpwdLine/" . $text;
                            $this->pushFlex($uid, $url);
                        } else {
                            // $this->pushTimePass($uid, $timePass);
                        }
                    } else {
                    }
                }
            }
        }

    }

}
