<?php
    namespace App\Http\Controllers;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;

    session_start();

    function time(){
        date_default_timezone_set("Asia/Taipei");
        return date("Y-m-d H:i:s");
    }

    function logincheck(){
        $row=DB::table("users")
            ->where("access_token","!=","NULL")
            ->select("*")->get();
        if($row->isNotEmpty()){
            return $row[0]->id;
        }else{
            return 0;
        }
    }

    function user($row,$type){
        $mainrow=[
            "id"=>$row->id,
            "email"=>$row->email,
            "nickname"=>$row->nickname,
            "profile_image"=>url($row->profile_image),
            "type"=>$row->type,
        ];
        if($type=="login"){
            $mainrow["access_token"]=$row->access_token;
        }
        return $mainrow;
    };

    function image($row){
        $data=[];
        $id=$row->id;
        $imagerow=DB::table("post_images")
            ->where(function($query)use($id){
                $query->where("post_id","=",$id);
            })->select("*")->get();
        for($i=0;$i<$imagerow->count();$i=$i+1){
            $data[]=[
                "id"=>$imagerow[$i]->id,
                "url"=>storage_path("public/".$imagerow[$i]->filename),
                "width"=>$imagerow[$i]->width,
                "height"=>$imagerow[$i]->height,
                "created_at"=>$imagerow[$i]->created_at
            ];
        }
        return $data;
    };

    function post($row){
        $userid=logincheck();
        $data=[];
        for($i=0;$i<count($row);$i=$i+1){
            $id=$row[$i]->id;
            $likerow=DB::table("user_likes")
                ->where(function($query)use($id){
                    $query->where("post_id","=",$id);
                })->select("*")->get();
            $userrow=DB::table("users")
                ->where(function($query)use($row,$i){
                    $query->where("id","=",$row[$i]->author_id);
                })->select("*")->get();
            if($row[$i]->location==""){ $location=NULL; }
            else{ $location=$row[$i]->location; }
            $mainrow=[
                "id"=>$id,
                "author"=>user($userrow[0],"normal"),
                "image"=>image($row[$i]),
                "like_count"=>count($likerow),
                "content"=>$row[$i]->content,
                "type"=>$row[$i]->type,
                "tag"=>explode(" ",$row[$i]->tag),
                "location_name"=>$location,
            ];
            if($userid){
                $likerow2=DB::table("user_likes")
                    ->where("post_id","=",$id)
                    ->where("user_id","=",$userid)
                    ->select("*")->get();
                if($likerow2->isNotEmpty()){
                    $mainrow["liked"]=true;
                }else{
                    $mainrow["liked"]=false;
                }
            }
            $mainrow["updated_at"]=$row[$i]->updated_at;
            $mainrow["created_at"]=$row[$i]->created_at;
            $data[]=$mainrow;
        }
        return $data;
    };


    function comment($row){
        $data=[];
        for($i=0;$i<count($row);$i=$i+1){
            $userrow=DB::table("users")
                ->where("id","=",$row[$i]->user_id)
                ->select("*")->get();
            $data[]=[
                "id"=>$row[$i]->id,
                "user"=>user($userrow[0],"normal"),
                "content"=>$row[$i]->content,
                "created_at"=>$row[$i]->created_at
            ];
        }
        return $data;
    };
?>