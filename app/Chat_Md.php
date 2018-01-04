<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat_Md extends Model
{
    private $conn;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $host = config('chat_const')['db_host'];
        $db_user = config('chat_const')['db_user'];
        $db_pswd = config('chat_const')['db_pswd'];
        $db_name = config('chat_const')['db_name'];

        $this->conn = new \mysqli($host,$db_user,$db_pswd,$db_name);
    }


    function r_out($r_id, $name){
        //방장인가?
        $query = "select user_name from chat_list where r_id = $r_id and user_name = '$name'";
        $result = $this->conn->query($query);

        $is_master = isset($result->fetch_all()[0][0])? 1 : 0;

        if($is_master){
            $query = "select user_num from chat_list where r_id = $r_id";
            $result = $this->conn->query($query);
            $user_num = $result->fetch_all()[0][0];
            if($user_num == 1){
                $this->delete_chat($r_id);
            }else{
                //방의 사람수 줄임
                $query1 = "update chat_list set user_num = $user_num-1 where r_id = $r_id";
                $result1 = $this->conn->query($query1);
                //list_child에서 사람 수 줄임
                $query2 = "delete from list_child where r_id = $r_id and user_name = '$name'";
                $result2 = $this->conn->query($query2);
                //마스터를 바꿈
                $query_reM = "select user_name from list_child where r_id = $r_id";
                $result_reM = $this->conn->query($query_reM);
                $reM = $result_reM->fetch_all()[0][0];
                $query3 = "update chat_list set user_name = '$reM' where r_id = $r_id";
                $result3 = $this->conn->query($query3);

            }
        }else{
            $query = "select user_num from chat_list where r_id = $r_id";
            $result = $this->conn->query($query);
            if(is_object($query)){
                $num = $result->fetch_all()[0][0];
            }
            $user_num = isset($num)? $num->fetch_all()[0][0] : 0;

            if($user_num){
                $query1 = "update chat_list set user_num = $user_num-1 where r_id = $r_id";
                $result1 = $this->conn->query($query1);
                $query2 = "delete from list_child where r_id = $r_id and user_name = '$name'";
                $result2 = $this->conn->query($query2);
            }
        }
    }

    function delete_chat($r_id){
        $query = "delete from list_child where r_id = $r_id";
        $result = $this->conn->query($query);
        $query1 = "delete from chat where r_id = $r_id";
        $result1 = $this->conn->query($query1);
        $query2 = "delete from chat_list where r_id = $r_id";
        $result2 = $this->conn->query($query2);
    }

    function getChat($r_id, $name){
        $query = "select `when` from list_child where r_id = $r_id and user_name = '$name'";
        $result = $this->conn->query($query);
        $data = $result->fetch_all()[0][0];
        $query1 = "select user_name, contents from chat where r_id = $r_id and date > '$data'";
        $result1 = $this->conn->query($query1);

        return $result1->fetch_all();
    }

    function chatIn($r_id, $content, $name){
        $query = "insert into chat(r_id, user_name, contents) values ($r_id, '$name', '$content');";
        $result = $this->conn->query($query);
    }

    function intoroom($r_id, $name){
        //이방 말고 다른방에 이 이름의 유저가 있는가?
        $query_is = "select user_name from list_child where  user_name = '$name' and r_id not in ($r_id)";
        $result_is = $this->conn->query($query_is);
        $is_in = isset($result_is->fetch_all()[0][0])? 1 : 0;

        //있으면
        if($is_in){
            return 0;
        }else{  //없으면
            //이 방에 이사람이 있는가?
            $query = "select user_name from list_child where r_id = $r_id and user_name='$name'";
            $result = $this->conn->query($query);

            $user_num = isset($result->fetch_all()[0][0])? 1 : 0;

            //있으면
            if($user_num){
                return 1;
            }else// 없으면
            {
                $query = "select user_num from chat_list where r_id = $r_id";
                $result = $this->conn->query($query);
                $user_num = $result->fetch_all()[0][0];
                $query1 = "update chat_list set user_num = $user_num+1 where r_id = $r_id";
                $result1 = $this->conn->query($query1);
                $query2 = "insert into list_child(r_id, user_name) values($r_id, '$name')";
                $result2 = $this->conn->query($query2);

                return 1;
            }
        }
    }

    function isChild($name){
        $query_is = "select user_name from list_child where user_name = '$name'";
        $result_is = $this->conn->query($query_is);
        $isname = isset($result_is->fetch_all()[0][0])? 1 : 0;

        return $isname;
    }

    public function setChat($title, $name){

        if($this->isChild($name) || strlen($title) > 20){
            return false;
        }else{
            $query = "insert into `chat_list`(r_title, user_name, user_num) VALUES ('$title', '$name', 1)";
            $result = $this->conn->query($query);
            $query1 = "select r_id from chat_list where r_title = '$title' and user_name = '$name'";
            $result1 = $this->conn->query($query1);
            $r_id = $result1->fetch_all()[0][0];
            $query2 = "insert into `list_child`(r_id, user_name) values ($r_id, '$name')";
            $result2 = $this->conn->query($query2);

            return $r_id;
        }
    }

    public function fUser($id, $pswd){
        $query = "select name from `user` where user_id = '$id' and pswd = '$pswd'";
        $result = $this->conn->query($query);
        $name = $result->fetch_all();

        return $name;
    }

    public function getList(){
        $query = "select * from `chat_list`";
        $result = $this->conn->query($query);
        $arr = $result->fetch_all();

        return $arr;
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->conn->close();
    }
}
