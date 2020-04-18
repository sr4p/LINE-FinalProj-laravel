<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ConfigAT extends Eloquent
{
    //
    protected $connection = 'mongodb';
    protected $collection = 'config';

    // protected $primaryKey = 'id';
    protected $fillable = ['_id','channelAccessToken','channelSecret','richmenu_login','richmenu_student','richmenu_personnal'];
    // ConfigAT::create(['_id' => 3,'channelAccessToken' => '','channelSecret' => '','richmenu_login' => '','richmenu_student' => '','richmenu_personnal' => '']);
    
    use Notifiable;
}
