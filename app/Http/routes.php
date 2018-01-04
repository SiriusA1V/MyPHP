<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


/*------------------chating--------------------------------------*/

Route::post('chat/login', 'Chat_Ctrl@login');
Route::get('chat/logout', 'Chat_Ctrl@logout');
Route::get('chat/list', 'Chat_Ctrl@chat_list');
Route::get('chat/room', 'Chat_Ctrl@chat_room');
Route::get('chat/make', 'Chat_Ctrl@make_room');
Route::get('chat/m_title', function () {
    if(!isset($_SESSION)){
        session_start();
    }

    if(isset($_SESSION['name'])){
        return view('m_title');
    }else{
        return view('chat_login');
    }
});
Route::post('chat/chat', 'Chat_Ctrl@chat');
Route::get('chat/getChat_si', 'Chat_Ctrl@getChat_si');
Route::get('chat/r_out', 'Chat_Ctrl@r_out');
Route::get('chat', 'Chat_Ctrl@chat_list');