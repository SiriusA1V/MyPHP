<?php

namespace App\Http\Controllers;

use App\Chat_Md;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class Chat_Ctrl extends Controller
{
    private $md;
    private $name;
    private $true = 0;

    function __construct()
    {
        if(!isset($_SESSION)){
            session_start();
        }

        $this->name = isset($_SESSION['name']) ? $_SESSION['name'] : 0;
        $this->md = new Chat_Md();
    }


    function r_out(){
        if(!$this->name){
            return view('chat_login');
        }

        $r_id = isset($_GET['r_id']) ? $_GET['r_id'] : 0;

        $this->md->r_out($r_id, $this->name);

        return $this->chat_list();
    }

    public function chat(){
        if(!$this->name){
            return view('chat_login');
        }

        $r_id = isset($_POST['r_id']) ? $_POST['r_id'] : 0;
        $contents = isset($_POST['contents']) ? $_POST['contents'] : 0;

        $this->md->Chatin($r_id, $contents, $this->name);
    }

    public function make_room(){
        if(!$this->name){
            return view('chat_login');
        }

        $title = isset($_GET['title']) ? $_GET['title'] : 0;


        $r_id = $this->md->setChat($title, $this->name);

        if($r_id){
            return view('chat', ['r_id' => $r_id]);
        }else{
            return $this->chat_list();
        }
    }

    public function getChat_si(){
        if(!$this->name){
            return view('chat_login');
        }

        $r_id = isset($_GET['r_id']) ? $_GET['r_id'] : 0;

        $arr = $this->getChat($r_id);

        for($i = 0; $i < sizeof($arr); $i++){
            echo $arr[$i][0]." > ".$arr[$i][1]."<br>";
        }
    }

    public function getChat($r_id){
        if(!$this->name){
            return view('chat_login');
        }

        $arr = $this->md->getChat($r_id, $this->name);

        return $arr;
    }

    public function chat_room(){
        if(!$this->name){
            return view('chat_login');
        }

        $r_id = isset($_GET['r_id']) ? $_GET['r_id'] : 0;

        $bool = $this->md->intoroom($r_id, $this->name);

        if($bool){
            return view('chat', ['r_id' => $r_id]);
        }else{
            return $this->chat_list();
        }
    }

    public function login(){
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        $pswd = isset($_POST['pswd']) ? $_POST['pswd'] : 0;

        $name = $this->md->fUser($id, $pswd);

        if(isset($name[0][0])){

            $_SESSION['name'] = $name[0][0];

            $this->true = 1;
            return $this->chat_list();
        }else{
            echo "<h style='color:red'>잘못된 정보입니다.</h>";
            return view('chat_login');
        }
    }

    public function logout(){
        if(!$this->name){
            return view('chat_login');
        }

        session_destroy();
        return view('chat_login');
    }

    public function chat_list(){
        if(!$this->name && !$this->true){
            return view('chat_login');
        }

        $arr = $this->md->getList();
        $arr2 = array();
        $page_arr = array();
        $list_num = config('chat_const')['list_num'];
        $last = ceil(sizeof($arr)/$list_num);
        $page_select = isset($_GET['page'])? $_GET['page'] : 1;
        $page_start = $page_select%3 == 0? (floor($page_select/3))*3-2 : (floor($page_select/3) + 1)*3-2;
        $start = ($page_select-1)*$list_num;
        $page_end = $page_start+2 <= $last? $page_start+2 : $last;
        $end = $start+($list_num-1) < sizeof($arr)? $start+$list_num : sizeof($arr);
        $before = $last > 3 && $page_start > 3 ? $page_start-3 : $page_select;
        $after = $last > 3 && $page_start < $last-2 ? $page_start+3 : $page_select;

        for($i = $start ;$i < $end; $i++)
        {
            array_unshift($arr[$i], $i+1);
            array_push($arr2, $arr[$i]);
        }

        for($i = 0, $j = $page_start; $j <= $page_end ; $i++, $j++){
            $page_arr[$i] = $j;
        }
        array_unshift($page_arr, $after);
        array_unshift($page_arr, $before);

        return view('chat_list', ['list' => $arr2, 'page' => $page_arr]);
    }
}
