<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('loginUser');
// });

Route::get('/', function () {
    return view('loginUser');
});

Route::get('/main', function () {
    return view('main');
});

Route::get('/user', function () {
    return view('user');
});

Route::get('/main/Userdata', 'UserController@showUser');

Route::post('/main/Userdata/modal', 'UserController@showInfoUser');

Route::get('/main/Admindata', 'UserController@showAdmin');

Route::post('/login', 'processLogin@checkUser');

Route::get('/checkLogin','processLogin@checkLogin');

Route::get('/main', 'mainController@showUser');

Route::get('/logout', 'mainController@logout');

Route::get('/main/logout', 'mainController@logout');

Route::post('main/Userdata/change', 'UserController@changeStatus');

Route::post('main/Userdata/push', 'UserController@push_msg');

Route::get('insert','insertAdmin@insertform');

Route::post('/main/admin/create','insertAdmin@insert');

Route::post('/main/Admindata/editModal', 'insertAdmin@editAdmin');

Route::post('/main/Admindata/showModal', 'insertAdmin@showAdminInfo');

Route::post('/main/admin/update','insertAdmin@update');

//config
Route::post('/changeConfig','mainController@CHconfig');
//rich
Route::get('/main/Richdata','admin_main_rich@showRich'); 

Route::get('/main/rich','admin_create_rich@index');

Route::post('/createRichmenu','admin_create_rich@CreateRichmenu'); //create

Route::post('/useRichmenu','admin_create_rich@UseRichmenu'); // use

Route::post('/cancelRichmenu','admin_create_rich@CancelTimeRich'); // cancel

Route::delete('/delRichmenu','admin_create_rich@DeleteRich'); //delete


//lineBot
Route::get('/line/login','line_login@index');

Route::get('/line/close','line_login@close');

Route::post('/line/registerBot','line_login@PostApi');

Route::get('/service/card','line_studentcard@index');

Route::post('/service/studentcard','line_studentcard@showProfile');

//myid
Route::delete('userline/logout/{uid}','line_login@outUser'); //delete
Route::get('userline/userall','line_login@showUser');       // all
Route::get('userline/user/{id}','line_login@getUserByID');  // byId


//webhook_line
Route::post('webhook','line_fulliment@handle');


Route::get('/test','test@dateformat');
Route::get('/test/{id}','test@test_profile');

Route::get('/testlogin','test@test_login'); //login
