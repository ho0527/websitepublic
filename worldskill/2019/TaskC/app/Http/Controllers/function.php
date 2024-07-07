<?php
    namespace App\Http\Controllers;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;

    session_start();
    date_default_timezone_set("Asia/Taipei");

    function time(){
        return date("Y-m-d H:i:s");
    }

    function logincheck($token){
        $row=DB::table("user")
            ->where("accesstoken","!=","NULL")
            ->select("*")->get();
        if($row->isNotEmpty()){
            for($i=0;$i<count($row);$i=$i+1){
                if($row[$i]->login_token==$token){
                    return $row[0]->id;
                }
            }
            return 0;
        }else{
            return 0;
        }
    }

    function comment($row){
        $data=[];
        for($i=0;$i<count($row);$i=$i+1){
            $iddata=$_SESSION["idlist"];
            $id=$row[$i]->id;
            if(in_array($id,$iddata)){
                $key=array_search($id,$iddata);
                unset($iddata[$key]);
                $_SESSION["idlist"]=$iddata;
                $userrow=DB::table("user")
                    ->where("id","=",$row[$i]->userid)
                    ->select("*")->get();
                $replycommentrow=DB::table("comment")
                    ->where("replyid","=",$id)
                    ->select("*")->get();
                $mainrow=[
                    "id"=>$row[$i]->id,
                    "text"=>$row[$i]->text,
                    "created_at"=>$row[$i]->created_at,
                    "user"=>$userrow[0]->nickname,
                    "replies"=>comment($replycommentrow)
                ];
                $data[]=$mainrow;
            }
        }
        return $data;
    }

    function delcomment($id){
        $row=DB::table("comment")
            ->where("replyid","=",$id)
            ->select("*")->get();

        DB::table("comment")
            ->where("id","=",$id)
            ->delete();
        for($i=0;$i<count($row);$i=$i+1){
            delcomment($row[$i]->id);
        }
    }
?>