<?php

namespace App\Http\Controllers;

use Config;
use Illuminate\Http\Request;
use \App\ConfigAT;

class mainController extends Controller
{
    //
    public function CHconfig(Request $req)
    {
        $cat = $req['cat'];
        $cs = $req['cs'];

        // $array = Config::get('linebot');
        // $array['ACCESS_TOKEN'] = $cat;
        // $array['CHANNEL_SECRET'] = $cs;
        // $data = var_export($array, 1);

        // if (File::put(app_path() . '/config/linebot.php', "<?php\n return $data ;")) {
        //     // Successful, return Redirect...
        // }

        $createId = ConfigAT::count();
        $tal = $createId + 1;

        $total = ConfigAT::all();
        if ($createId == 0) {
            ConfigAT::create(['_id' => $tal, 'channelAccessToken' => $cat, 'channelSecret' => $cs, 'richmenu_login' => '', 'richmenu_student' => '', 'richmenu_personnal' => '']);
            // Rich::all()->delete();
        } else {
            $update_token = array('channelAccessToken' => $cat, 'channelSecret' => $cs);
            $countId = ConfigAT::count();
            $dt = ConfigAT::where('_id', $createId);
            $dt->update($update_token, ['upsert' => true]);
        }

        Config::set('linebot.ACCESS_TOKEN', $cat);
        Config::set('linebot.CHANNEL_SECRET', $cs);
    }

    public function showUser(Request $req)
    {
        $countId = ConfigAT::count();
        $tal = $countId + 1;

        $at = null;
        $cs = null;
        if ($countId == 0) {
            $at = Config::get('linebot.ACCESS_TOKEN');
            $cs = Config::get('linebot.CHANNEL_SECRET');
        } else {
            $ct = ConfigAT::where('_id', $countId)->get();
            $at = $ct[0]['channelAccessToken'];
            $cs = $ct[0]['channelSecret'];
        }

        $username = "test";
        $usname = session("username");
        if ($usname) {
            // return view('main', compact('username','at','cs'));
            return view('main', ['username' => $username, 'at' => $at, 'cs' => $cs]);
        } else {
            return redirect('/');
        }
    }
    public function logout()
    {
        session()->pull('username', 'default');
        return redirect('/');
    }
}
