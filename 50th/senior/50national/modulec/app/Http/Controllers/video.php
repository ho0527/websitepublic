<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;

    class video extends Controller{
        public function uploadvideo(Request $request){
            $requestdata=Validator::make($request->all(),[
                "title"=>"required|string",
                "description"=>"required|string",
                "visibility"=>"required|string",
                "category_id"=>"required|integer",
                "duration"=>"required|integer",
                "video"=>"required|mimes:mp4"
            ],[
                "required"=>4,
                "string"=>5,
                "integer"=>5,
                "mimes"=>6,
            ]);
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $row=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($row[0]->disabled=="false"){
                    if(!$requestdata->fails()){
                        $requestdata=$requestdata->validate();
                        $row=DB::table("category")
                            ->where("id","=",$requestdata["category_id"])
                            ->select("*")->get();
                        if($row->isNotEmpty()){
                            $path=$requestdata["video"]->store("upload");
                            DB::table("video")->insert([
                                "userid"=>$userid,
                                "categoryid"=>$requestdata["category_id"],
                                "url"=>$path,
                                "title"=>$requestdata["title"],
                                "description"=>$requestdata["description"],
                                "visibility"=>$requestdata["visibility"],
                                "duration"=>$requestdata["duration"],
                                "count"=>0,
                                "created_at"=>Controller::time()
                            ]);
                            $row=DB::table("video")
                                ->latest()
                                ->select("*")->get()[0];
                            return response()->json([
                                "success"=>true,
                                "message"=>"",
                                "data"=>[
                                    "id"=>$row->id,
                                    "url"=>url($path)
                                ]
                            ],200);
                        }else{
                            return Controller::error(9);
                        }
                    }else{
                        return Controller::error($requestdata->messages()->first());
                    }
                }else{
                    return Controller::error(2);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function getvideo(Request $request){
            $row=DB::table("video")
                ->select("*")->get();

            $categoryrow=DB::table("category")
                ->select("*")->get();

            $data=[];

            for($i=0;$i<count($row);$i=$i+1){
                $data[]=[
                    "id"=>$row[$i]->id,
                    "title"=>$row[$i]->title,
                    "description"=>$row[$i]->description,
                    "duration"=>$row[$i]->duration,
                    "visibility"=>$row[$i]->visibility,
                    "created_at"=>$row[$i]->created_at,
                    "categoryid"=>$categoryrow[$row[$i]->categoryid]->title,
                ];
            }

            return response()->json([
                "success"=>true,
                "message"=>"",
                "data"=>$data
            ],200);
        }

        public function gethotvideo(Request $request){
            // a START
            $date=date("Y-m-d");
            $day=date("N");
            $date=strtotime("-$day day",strtotime($date));
            $date=date("Y-m-d H:i:s",$date);
            $programrow=DB::table("program")
                ->where("updatedat",">=",$date)
                ->select("*")->get();
            $programdata=[];
            for($i=0;$i<count($programrow);$i=$i+1){
                $programvideolistrow=DB::table("programvideolist")
                    ->where("programid","=",$programrow[$i]->id)
                    ->select("*")->get();
                $data=[];
                if($programvideolistrow->isNotEmpty()){
                    $data=[
                        "id"=>$programvideolistrow[count($programvideolistrow)-1]->id,
                        "episode_title"=>$programvideolistrow[count($programvideolistrow)-1]->title,
                    ];
                }
                $programdata[]=[
                    "id"=>$programrow[$i]->id,
                    "title"=>$programrow[$i]->title,
                    "cover_path"=>$programrow[$i]->url,
                    "description"=>$programrow[$i]->description,
                    "authorized_start_datetime"=>$programrow[$i]->authorizedstart,
                    "authorized_end_datetime"=>$programrow[$i]->authorizedend,
                    "updated_at"=>$programrow[$i]->updatedat,
                    "latest_video"=>$data
                ];
            }

            usort($programdata,function($a,$b){
                if($a["updated_at"]<$b["updated_at"]){
                    return 1;
                }
            });
            // a END
            
            // b START
            $hotprogram=[];
            $programvideolistrow=DB::table("programvideolist")
                ->where("createdat",">=",$date)
                ->select("*")->get();
            for($i=0;$i<count($programvideolistrow);$i=$i+1){
                $programrow=DB::table("program")
                    ->where("id","=",$programvideolistrow[$i]->programid)
                    ->select("*")->get()[0];
                $playrow=DB::table("play")
                    ->where("videoid","=",$programvideolistrow[$i]->videoid)
                    ->select("*")->get();

                $check=true;
                for($j=0;$j<count($hotprogram);$j=$j+1){
                    if($hotprogram[$j]["id"]==$programrow->id){
                        $hotprogram[$j]["plays"]=$hotprogram[$j]["plays"]+count($playrow);
                    }
                }
                if($check){
                    $hotprogram[]=[
                        "id"=>$programrow->id,
                        "title"=>$programrow->title,
                        "cover_path"=>$programrow->url,
                        "description"=>$programrow->description,
                        "authorized_start_datetime"=>$programrow->authorizedstart,
                        "authorized_end_datetime"=>$programrow->authorizedend,
                        "plays"=>count($playrow)
                    ];
                }
            }
            usort($hotprogram,function($a,$b){
                if($a["plays"]<$b["plays"]){
                    return 1;
                }
            });
            // b END

            // c START
            $categorydata=[];

            $categoryrow=DB::table("category")
                ->select("*")->get();

            for($i=0;$i<count($categoryrow);$i=$i+1){
                $data=[];
                $videorow=DB::table("video")
                    ->where("categoryid","=",$categoryrow[$i]->id)
                    ->where("created_at",">=",$date)
                    ->where("created_at","<",time())
                    ->select("*")->get();
                for($j=0;$j<count($videorow);$j=$j+1){
                    $userrow=DB::table("user")
                        ->where("id","=",$videorow[$j]->userid)
                        ->select("*")->get()[0];
                    $playrow=DB::table("play")
                        ->where("videoid","=",$videorow[$j]->id)
                        ->select("*")->get();
                    $data[]=[
                        "id"=>$videorow[$j]->id,
                        "title"=>$videorow[$j]->title,
                        "description"=>$videorow[$j]->description,
                        "duration"=>$videorow[$j]->duration,
                        "created_at"=>$videorow[$j]->created_at,
                        "user"=>$userrow->nickname,
                        "plays"=>count($playrow)
                    ];
                }
                usort($data,function($a,$b){
                    if($a["plays"]<$b["plays"]){
                        return 1;
                    }
                });
                $categorydata[]=[
                    "id"=>$categoryrow[$i]->id,
                    "title"=>$categoryrow[$i]->title,
                    "videos"=>array_slice($data,0,4)
                ];
            }
            // c END

            // d START
            $categoryrow=DB::table("category")
                ->select("*")->get();

            $videorow=DB::table("video")
                ->latest()
                ->select("*")->get();

            $latestvideo=[];

            for($i=0;$i<min(4,count($videorow));$i=$i+1){
                $categoryrow=DB::table("category")
                    ->where("id","=",$videorow[$i]->categoryid)
                    ->select("*")->get()[0];
                $userrow=DB::table("user")
                    ->where("id","=",$videorow[$i]->userid)
                    ->select("*")->get()[0];
                $latestvideo[]=[
                    "id"=>$videorow[$i]->id,
                    "title"=>$videorow[$i]->title,
                    "description"=>$videorow[$i]->description,
                    "duration"=>$videorow[$i]->duration,
                    "created_at"=>$videorow[$i]->created_at,
                    "category"=>$categoryrow->title,
                    "user"=>$userrow->nickname
                ];
            }
            // d END

            return response()->json([
                "success"=>true,
                "message"=>"",
                "data"=>[
                    "this_week_programs"=>$programdata,
                    "hot_program"=>$hotprogram,
                    "hot_category_videos"=>$categorydata,
                    "latest_videos"=>$latestvideo
                ]
            ],200);
        }

        public function getpublicvideo(Request $request){
            $keyword="";
            $page="1";
            if($request->has("q")){ $keyword=$request->input("q"); }
            if($request->has("page")){ $page=$request->input("page"); }

            $keywordlist=explode(" ",$keyword);

            if($keyword==""){
                $row=DB::table("video")
                    ->where("visibility","=","PUBLIC")
                    ->skip(($page-1)*10)
                    ->take(10)
                    ->select("*")->get();
            }else{
                $row=DB::table("video")
                    ->where("visibility","=","PUBLIC")
                    ->where("title","like","%".$keywordlist[0]."%")
                    ->orWhere("description","like","%".$keywordlist[0]."%")
                    ->orWhere(function($query)use($keywordlist){
                        for($i=1;$i<count($keywordlist);$i=$i+1){
                            $query->orWhere("title","like","%".$keywordlist[$i]."%");
                            $query->orWhere("description","like","%".$keywordlist[$i]."%");
                            // $query->orWhereRaw("MATCH(`title`,`description`)AGAINST(? IN BOOLEAN MODE)",[$keywordlist[$i]]); // full text部分有問題
                        }
                    })
                    ->skip(($page-1)*10)
                    ->take(10)
                    ->select("*")->get();
            }

            $categoryrow=DB::table("category")
                ->select("*")->get();

            $data=[];

            for($i=0;$i<count($row);$i=$i+1){
                $data[]=[
                    "id"=>$row[$i]->id,
                    "title"=>$row[$i]->title,
                    "description"=>$row[$i]->description,
                    "duration"=>$row[$i]->duration,
                    "visibility"=>$row[$i]->visibility,
                    "created_at"=>$row[$i]->created_at,
                    "category"=>$categoryrow[$row[$i]->categoryid]->title,
                ];
            }

            return response()->json([
                "success"=>true,
                "message"=>"",
                "data"=>[
                    "total_count"=>count($data),
                    "videos"=>$data
                ]
            ],200);
        }

        public function getidvideo(Request $request,$videoid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $row=DB::table("video")
                    ->where("id","=",$videoid)
                    ->select("*")->get();

                $userrow=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get()[0];
                if($userrow->disabled=="false"){
                    if($row->isNotEmpty()){
                        if($row[0]->userid==$userid){
                            DB::table("play")->insert([
                                "userid"=>$userid,
                                "videoid"=>$videoid,
                                "createdat"=>time()
                            ]);

                            $categoryrow=DB::table("category")
                                ->where("id","=",$row[0]->categoryid)
                                ->select("*")->get();
                            $likerow=DB::table("like")
                                ->where("videoid","=",$videoid)
                                ->select("*")->get();
                            $playrow=DB::table("play")
                                ->where("videoid","=",$videoid)
                                ->select("*")->get();
                            $programvideolistrow=DB::table("programvideolist")
                                ->where("videoid","=",$videoid)
                                ->select("*")->get();
                            $playlistrow=DB::table("playlist")
                                ->select("*")->get();
                            $likerow2=DB::table("like")
                                ->where("videoid","=",$videoid)
                                ->where("userid","=",$userid)
                                ->select("*")->get();
                            $like=false;
                            if($likerow2->isNotEmpty()){
                                $like=true;
                            }
                            $playlistdata=[];
                            $programvideolistdata=[];
                            echo("check1");
                            for($i=0;$i<count($playlistrow);$i=$i+1){
                                $data=explode(" ",$playlistrow);
                                for($j=0;$j<count($data);$j=$j+1){
                                    if($data[$j]==$videoid){
                                        $playlistdata[]=$playlistrow[$i]->id;
                                    }
                                }
                            }
                            for($i=0;$i<count($programvideolistrow);$i=$i+1){
                                $programvideolistdata[]=(int)$programvideolistrow[$i]->programid;
                            }
                            return response()->json([
                                "success"=>true,
                                "message"=>"",
                                "data"=>[
                                    "id"=>$row[0]->id,
                                    "title"=>$row[0]->title,
                                    "description"=>$row[0]->description,
                                    "visibility"=>$row[0]->visibility,
                                    "duration"=>$row[0]->duration,
                                    "created_at"=>$row[0]->created_at,
                                    "category"=>$categoryrow[0]->title,
                                    "user"=>$userrow->nickname,
                                    "url"=>url($row[0]->url),
                                    "like"=>count($likerow),
                                    "liked"=>$like,
                                    "plays"=>count($playrow),
                                    "playlist_ids"=>$playlistdata,
                                    "program_ids"=>$programvideolistdata
                                ]
                            ],200);
                        }else{
                            return Controller::error(3);
                        }
                    }else{
                        return Controller::error(10);
                    }
                }else{
                    return Controller::error(2);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function delvideo(Request $request,$videoid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $row=DB::table("video")
                    ->where("id","=",$videoid)
                    ->select("*")->get();

                $userrow=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get()[0];
                if($userrow->disabled=="false"){
                    if($row->isNotEmpty()){
                        if($row[0]->userid==$userid){
                            $isinplaylist=false;
                            $playlistrow=DB::table("playlist")
                                ->select("*")->get();
                            for($i=0;$i<count($playlistrow);$i=$i+1){
                                $data=explode(" ",$playlistrow);
                                for($j=0;$j<count($data);$j=$j+1){
                                    if($data[$j]==$videoid){
                                        $isinplaylist=true;
                                    }
                                }
                            }
                            if(!$isinplaylist){
                                $programvideolistrow=DB::table("programvideolist")
                                    ->where("videoid","=",$videoid)
                                    ->select("*")->get();
                                if($programvideolistrow->isEmpty()){
                                    $row=DB::table("video")
                                        ->where("id","=",$videoid)
                                        ->delete();
                                    return response()->json([
                                        "success"=>true,
                                        "message"=>"",
                                        "data"=>""
                                    ],200);
                                }else{
                                    return videoinprogram();
                                }
                            }else{
                                return videoinplaylist();
                            }
                        }else{
                            return nopermission();
                        }
                    }else{
                        return videonotfound();
                    }
                }else{
                    return userdisabled();
                }
            }else{
                return tokenerror();
            }
        }

        public function like(Request $request,$videoid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $userrow=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($userrow[0]->disabled=="false"){
                    if($request->has("like")){
                        if($request->input("like")){
                            $row=DB::table("video")
                                ->where("id","=",$videoid)
                                ->select("*")->get();
                            if($row->isNotEmpty()){
                                if($row[0]->visibility=="PUBLIC"||($row[0]->visibility=="PRIVATE"&&$row[0]->userid==$userid)){
                                    $likerow=DB::table("like")
                                        ->where("userid","=",$userid)
                                        ->where("videoid","=",$videoid)
                                        ->select("*")->get();
                                    if($likerow->isEmpty()){
                                        DB::table("like")->insert([
                                            "userid"=>$userid,
                                            "videoid"=>$videoid,
                                            "created_at"=>Controller::time()
                                        ]);
                                    }else{
                                        DB::table("like")
                                            ->where("userid","=",$userid)
                                            ->where("videoid","=",$videoid)
                                            ->delete();
                                    }
                                    return response()->json([
                                        "success"=>true,
                                        "message"=>"",
                                        "data"=>""
                                    ],200);
                                }else{
                                    return Controller::error(3);
                                }
                            }else{
                                return Controller::error(10);
                            }
                        }else{
                            return Controller::error(5);
                        }
                    }else{
                        return Controller::error(4);
                    }
                }else{
                    return Controller::error(2);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function comment(Request $request,$videoid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $userrow=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($userrow[0]->disabled=="false"){
                    $blocklistrow=DB::table("blocklist")
                        ->where("userid","=",$userid)
                        ->select("*")->get();
                    if($blocklistrow[count($blocklistrow)-1]->to<time()){
                        if($request->has("text")){
                            $text=$request->input("text");
                            if(is_string($text)){
                                $row=DB::table("video")
                                    ->where("id","=",$videoid)
                                    ->select("*")->get();
                                if($row->isNotEmpty()){
                                    if($row[0]->visibility=="PUBLIC"||($row[0]->visibility=="PRIVATE"&&$row[0]->userid==$userid)){
                                        DB::table("comment")->insert([
                                            "userid"=>$userid,
                                            "videoid"=>$videoid,
                                            "replyid"=>NULL,
                                            "text"=>$text,
                                            "created_at"=>Controller::time()
                                        ]);
    
                                        return response()->json([
                                            "success"=>true,
                                            "message"=>"",
                                            "data"=>1 // WTF
                                        ],200);
                                    }else{
                                        return Controller::error(3);
                                    }
                                }else{
                                    return Controller::error(10);
                                }
                            }else{
                                return Controller::error(5);
                            }
                        }else{
                            return Controller::error(4);
                        }
                    }else{
                        return Controller::error(3);
                    }
                }else{
                    return Controller::error(2);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function getcomment(Request $request,$videoid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $userrow=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($userrow[0]->disabled=="false"){
                    $videorow=DB::table("video")
                        ->where("id","=",$videoid)
                        ->select("*")->get();
                    if($videorow->isNotEmpty()){
                        if($videorow[0]->visibility=="PUBLIC"||($videorow[0]->visibility=="PRIVATE"&&$videorow[0]->userid==$userid)){
                            $page=1;
                            if($request->has("page")){
                                $page=$request->input("page");
                            }

                            $row=DB::table("comment")
                                ->where("videoid","=",$videoid)
                                ->latest()
                                ->select("*")->get();

                            $data=[];
                            $count=0;
                            $temprow=[];

                            for($i=0;$i<count($row);$i=$i+1){
                                $userrow=DB::table("user")
                                    ->where("id","=",$row[$i]->userid)
                                    ->select("*")->get();
                                $temprow[]=[
                                    "id"=>$row[$i]->id,
                                    "text"=>$row[$i]->text,
                                    "created_at"=>$row[$i]->created_at,
                                    "replyid"=>$row[$i]->replyid,
                                    "user"=>$userrow[0]->nickname,
                                    "replies"=>[]
                                ];
                            }

                            for($i=0;$i<count($temprow);$i=$i+1){
                                if($temprow[$i]["replyid"]==NULL){
                                    unset($temprow[$i]["replyid"]);
                                    $data[]=$temprow[$i];
                                }else{
                                    $replyid=$temprow[$i]["replyid"];
                                    for($j=0;$j<count($temprow);$j=$j+1){
                                        if($temprow[$j]["id"]==$replyid){
                                            unset($temprow[$i]["replyid"]);
                                            $temprow[$j]["replies"][]=$temprow[$i];
                                            break;
                                        }
                                    }
                                }
                            }

                            return response()->json([
                                "success"=>true,
                                "data"=>[
                                    "total_count"=>count($row),// 不確定是不是指影片全部總數還是顯示總數
                                    "comments"=>array_slice($data,($page-1)*10,10)
                                ]
                            ],200);
                        }else{
                            return Controller::error(3);
                        }
                    }else{
                        return Controller::error(10);
                    }
                }else{
                    return Controller::error(2);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function replycomment(Request $request,$commentid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $userrow=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($userrow[0]->disabled=="false"){
                    $commentrow=DB::table("comment")
                        ->where("id","=",$commentid)
                        ->select("*")->get();
                    if($commentrow->isNotEmpty()){
                        $blocklistrow=DB::table("blocklist")
                            ->where("userid","=",$userid)
                            ->select("*")->get();
                        $videorow=DB::table("video")
                            ->where("id","=",$commentrow[0]->videoid)
                            ->select("*")->get()[0];
                        if($videorow->visibility=="PUBLIC"||($videorow[0]->visibility=="PRIVATE"&&$videorow[0]->userid==$userid)&&$blocklistrow[count($blocklistrow)-1]->to<time()){
                            if($request->has("text")){
                                $text=$request->input("text");
                                if(is_string($text)){
                                    DB::table("comment")->insert([
                                        "userid"=>$userid,
                                        "videoid"=>$commentrow[0]->videoid,
                                        "replyid"=>$commentid,
                                        "text"=>$text,
                                        "created_at"=>Controller::time()
                                    ]);

                                    return response()->json([
                                        "success"=>true,
                                        "message"=>"",
                                        "data"=>1 // WTF
                                    ],200);
                                }else{
                                    return Controller::error(5);
                                }
                            }else{
                                return Controller::error(4);
                            }
                        }else{
                            return Controller::error(3);
                        }
                    }else{
                        return Controller::error(11);
                    }
                }else{
                    return Controller::error(2);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function delcomment(Request $request,$commentid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $userrow=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($userrow[0]->disabled=="false"){
                    $commentrow=DB::table("comment")
                        ->where("id","=",$commentid)
                        ->select("*")->get();
                    if($commentrow->isNotEmpty()){
                        $videorow=DB::table("video")
                            ->where("id","=",$commentrow[0]->videoid)
                            ->select("*")->get()[0];
                        if($commentrow[0]->userid==$userid||$userid=="1"){
                            if($request->has("ban")){
                                if($userid=="1"){
                                    if($request->has("days")&&$request->has("reason")){
                                        $day=$request->input("days");
                                        $reason=$request->input("reason");
                                        if(is_int($day)&&is_string($reason)){
                                            Controller::controllerdelcomment($commentid);
                                            $date=date("Y-m-d H:i:s");
                                            
                                            // 加一天
                                            $date=strtotime("+$day day",strtotime($date));
                                            $date=date("Y-m-d H:i:s",$date);
                                            
                                            // 减一秒
                                            $date=strtotime("-1 second",strtotime($date));
                                            $date=date("Y-m-d H:i:s",$date);
                                            
                                            DB::table("blocklist")->insert([
                                                "userid"=>$commentrow[0]->userid,
                                                "from"=>time(),
                                                "to"=>$date,
                                                "reason"=>$reason,
                                                "createat"=>Controller::time()
                                            ]);
                                            return response()->json([
                                                "success"=>true,
                                                "message"=>"",
                                                "data"=>""
                                            ],200);
                                        }else{
                                            return Controller::error(5);
                                        }
                                    }else{
                                        return Controller::error(4);
                                    }
                                }else{
                                    return Controller::error(3);
                                }
                            }else{
                                Controller::delcomment($commentid);
                                return response()->json([
                                    "success"=>true,
                                    "message"=>"",
                                    "data"=>""
                                ],200);
                            }
                        }else{
                            return Controller::error(3);
                        }
                    }else{
                        return Controller::error(11);
                    }
                }else{
                    return Controller::error(2);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function getplaylist(Request $request){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $data=[];

                $row=DB::table("playlist")
                    ->where("userid","=",$userid)
                    ->select("*")->get();

                for($i=0;$i<count($row);$i=$i+1){
                    $data[]=[
                        "id"=>$row[$i]->id,
                        "title"=>$row[$i]->title
                    ];
                }

                return response()->json([
                    "success"=>true,
                    "message"=>"",
                    "data"=>$data
                ],200);
            }else{
                return Controller::error(1);
            }
        }

        public function playlist(Request $request){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $userrow=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($userrow[0]->disabled=="false"){
                    if($request->has("title")){
                        $title=$request->input("title");
                        if(is_string($title)){
                            $row=DB::table("playlist")
                                ->where("title","=",$title)
                                ->select("*")->get();
                            if($row->isEmpty()){
                                DB::table("playlist")->insert([
                                    "userid"=>$userid,
                                    "title"=>$title,
                                    "videolist"=>"",
                                    "createdat"=>Controller::time()
                                ]);

                                return response()->json([
                                    "success"=>true,
                                    "message"=>"",
                                    "data"=>1 // WTF
                                ],200);
                            }else{
                                return Controller::error(19);
                            }
                        }else{
                            return Controller::error(5);
                        }
                    }else{
                        return Controller::error(4);
                    }
                }else{
                    return Controller::error(2);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function getidplaylist(Request $request,$playlistid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $userrow=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($userrow[0]->disabled=="false"){
                    $row=DB::table("playlist")
                        ->where("id","=",$playlistid)
                        ->select("*")->get();
                    if($row->isNotEmpty()){
                        if($row[0]->userid==$userid){
                            $video=[];
                            $videolist=explode(" ",$row[0]->videolist);
                            for($i=0;$i<count($videolist);$i=$i+1){
                                $videorow=DB::table("video")
                                    ->where("id","=",$videolist[$i])
                                    ->select("*")->get();
                                if($videorow->isNotEmpty()){
                                    $userrow2=DB::table("user")
                                        ->where("id","=",$videorow[0]->userid)
                                        ->select("*")->get()[0];
                                    $video[]=[
                                        "id"=>$videorow[0]->id,
                                        "title"=>$videorow[0]->title,
                                        "description"=>$videorow[0]->description,
                                        "visibility"=>$videorow[0]->visibility,
                                        "duration"=>$videorow[0]->duration,
                                        "created_at"=>$videorow[0]->created_at,
                                        "user"=>$userrow2->nickname
                                    ];
                                }
                            }
            
                            return response()->json([
                                "success"=>true,
                                "message"=>"",
                                "data"=>[
                                    "id"=>$row[0]->id,
                                    "title"=>$row[0]->title,
                                    "user"=>$userrow[0]->nickname,
                                    "video"=>$video
                                ]
                            ],200);
                        }else{
                            return Controller::error(3);
                        }
                    }else{
                        return Controller::error(12);
                    }
                }else{
                    return Controller::error(2);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function addvideotoplaylist(Request $request,$playlistid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $userrow=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($userrow[0]->disabled=="false"){
                    if($request->has("video_id")){
                        $videoid=$request->input("video_id");
                        if(is_int($videoid)){
                            $videorow=DB::table("video")
                                ->where("id","=",$videoid)
                                ->select("*")->get();
                            if($videorow->isNotEmpty()){
                                $row=DB::table("playlist")
                                    ->where("id","=",$playlistid)
                                    ->select("*")->get();
                                if($row->isNotEmpty()){
                                    if(($videorow[0]->visibility=="PUBLIC"||($videorow[0]->visibility=="PRIVATE"&&$videorow[0]->userid==$userid))&&$row[0]->userid==$userid){
                                        $videolist=explode(" ",$row[0]->videolist);
                                        if(!in_array($videoid,$videolist)){
                                            $videolist[]=$videoid;
                                            DB::table("playlist")
                                                ->where("id","=",$playlistid)
                                                ->update([
                                                    "videolist"=>implode(" ",$videolist)
                                                ]);
        
                                            return response()->json([
                                                "success"=>true,
                                                "message"=>"",
                                                "data"=>""
                                            ],200);
                                        }else{
                                            return Controller::error(17);
                                        }
                                    }else{
                                        return Controller::error(3);
                                    }
                                }else{
                                    return Controller::error(12);
                                }
                            }else{
                                return Controller::error(11);
                            }
                        }else{
                            return Controller::error(5);
                        }
                    }else{
                        return Controller::error(4);
                    }
                }else{
                    return Controller::error(2);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function sortplaylist(Request $request,$playlistid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $userrow=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($userrow[0]->disabled=="false"){
                    if($request->has("video_ids")){
                        $videoidlist=$request->input("video_ids");
                        if(is_array($videoidlist)){
                            $videorow=DB::table("video")
                                ->where("id","=",$videoidlist)
                                ->select("*")->get();
                            if($videorow->isNotEmpty()){
                                $row=DB::table("playlist")
                                    ->where("id","=",$playlistid)
                                    ->select("*")->get();
                                if($row->isNotEmpty()){
                                    if(($videorow[0]->visibility=="PUBLIC"||($videorow[0]->visibility=="PRIVATE"&&$videorow[0]->userid==$userid))&&$row[0]->userid==$userid){
                                        $videolist=explode(" ",$row[0]->videolist);
                                        $tempvidoeidlist=$videoidlist;
                                        $tempvidoelist=$videolist;

                                        sort($tempvidoeidlist);
                                        sort($tempvidoelist);

                                        if(count($tempvidoeidlist)==count($tempvidoelist)){
                                            $check=true;
                                            for($i=0;$i<count($tempvidoelist);$i=$i+1){
                                                if($tempvidoeidlist[$i]!=$tempvidoelist[$i]){
                                                    $check=false;
                                                }
                                            }
                                            if($check){
                                                DB::table("playlist")
                                                    ->where("id","=",$playlistid)
                                                    ->update([
                                                        "videolist"=>implode(" ",$videoidlist)
                                                    ]);
            
                                                return response()->json([
                                                    "success"=>true,
                                                    "message"=>"",
                                                    "data"=>""
                                                ],200);
                                            }else{
                                                return Controller::error(13);
                                            }
                                        }else{
                                            return Controller::error(7);
                                        }
                                    }else{
                                        return Controller::error(3);
                                    }
                                }else{
                                    return Controller::error(12);
                                }
                            }else{
                                return Controller::error(10);
                            }
                        }else{
                            return Controller::error(5);
                        }
                    }else{
                        return Controller::error(4);
                    }
                }else{
                    return Controller::error(2);
                }
            }else{
                return Controller::error(1);
            }
        
        }

        public function delvideoformplaylist(Request $request,$playlistid,$videoid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $userrow=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($userrow[0]->disabled=="false"){
                    $videorow=DB::table("video")
                        ->where("id","=",$videoid)
                        ->select("*")->get();
                    if($videorow->isNotEmpty()){
                        $row=DB::table("playlist")
                            ->where("id","=",$playlistid)
                            ->select("*")->get();
                        if($row->isNotEmpty()){
                            if(($videorow[0]->visibility=="PUBLIC"||($videorow[0]->visibility=="PRIVATE"&&$videorow[0]->userid==$userid))&&$row[0]->userid==$userid){
                                $videolist=explode(" ",$row[0]->videolist);
                                if(in_array($videoid,$videolist)){
                                    $key=array_search($videoid,$videolist);
                                    unset($videolist[$key]);
                                    DB::table("playlist")
                                        ->where("id","=",$playlistid)
                                        ->update([
                                            "videolist"=>implode(" ",$videolist)
                                        ]);

                                    return response()->json([
                                        "success"=>true,
                                        "message"=>"",
                                        "data"=>""
                                    ],200);
                                }else{
                                    return Controller::error(13);
                                }
                            }else{
                                return Controller::error(3);
                            }
                        }else{
                            return Controller::error(12);
                        }
                    }else{
                        return Controller::error(10);
                    }
                }else{
                    return Controller::error(2);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function delplaylist(Request $request,$playlistid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $userrow=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($userrow[0]->disabled=="false"){
                    $row=DB::table("playlist")
                        ->where("id","=",$playlistid)
                        ->select("*")->get();
                    if($row->isNotEmpty()){
                        if($row[0]->userid==$userid){
                            DB::table("playlist")
                                ->where("id","=",$playlistid)
                                ->delete();

                            return response()->json([
                                "success"=>true,
                                "message"=>"",
                                "data"=>""
                            ],200);
                        }else{
                            return Controller::error(3);
                        }
                    }else{
                        return Controller::error(12);
                    }
                }else{
                    return Controller::error(2);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function getprogram(Request $request){
            $keyword="";
            $page="1";
            if($request->has("q")){ $keyword=$request->input("q"); }
            if($request->has("page")){ $page=$request->input("page"); }

            $keywordlist=explode(" ",$keyword);

            $row=DB::table("program")
            ->where(function($query)use($keywordlist){
                $query->where("authorizedend",">=",Controller::time())
                    ->orWhereNull("authorizedend")
                    ->where(function($query)use($keywordlist){
                        $query->orWhere("title","like","%".$keywordlist[0]."%")
                            ->orWhere("description","like","%".$keywordlist[0]."%");
                    });
                    for($i=1;$i<count($keywordlist);$i=$i+1){
                        $query->orWhere(function($query)use($keywordlist,$i){
                            $query->orWhere("title","like","%".$keywordlist[$i]."%")
                                ->orWhere("description","like","%".$keywordlist[$i]."%");
                    });
                }
            })
            ->skip(($page-1)* 10)
            ->take(10)
            ->select("*")->get();

            $data=[];

            for($i=0;$i<count($row);$i=$i+1){
                $programvideolistrow=DB::table("programvideolist")
                ->where("programid","=",$row[$i]->id)
                    ->select("*")->get();
                $data[]=[
                    "id"=>$row[$i]->id,
                    "title"=>$row[$i]->title,
                    "description"=>$row[$i]->description,   
                    "authorized_start_datetime"=>$row[$i]->authorizedstart,
                    "authorized_end_datetime"=>$row[$i]->authorizedend,
                    "updated_at"=>$row[$i]->updatedat,
                    "episodes_count"=>count($programvideolistrow),
                ];
            }

            return response()->json([
                "success"=>true,
                "message"=>"",
                "data"=>[
                    "total_count"=>count($row),
                    "videos"=>$data
                ]
            ],200);
        }

        public function getidprogram(Request $request,$programid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $row=DB::table("program")
                    ->where("id","=",$programid)
                    ->select("*")->get();
                if($row->isNotEmpty()){
                    $programvideolistrow=DB::table("programvideolist")
                        ->where("programid","=",$programid)
                        ->select("*")->get();
                    $episode=[];
                    for($i=0;$i<count($programvideolistrow);$i=$i+1){
                        $videorow=DB::table("video")
                            ->where("id","=",$programvideolistrow[$i]->videoid)
                            ->select("*")->get()[0];
                        $categoryrow=DB::table("category")
                            ->where("id","=",$videorow->categoryid)
                            ->select("*")->get()[0];
                        $userrow=DB::table("user")
                            ->where("id","=",$videorow->userid)
                            ->select("*")->get()[0];
                        $episode[]=[
                            "id"=>$videorow->id,
                            "title"=>$videorow->title,
                            "description"=>$videorow->description,
                            "duration"=>$videorow->duration,
                            "created_at"=>$videorow->created_at,
                            "category"=>$categoryrow->title,
                            "user"=>$userrow->nickname,
                            "episode_title"=>$programvideolistrow[$i]->title
                        ];
                    }
                    return response()->json([
                        "success"=>true,
                        "message"=>"",
                        "data"=>[
                            "id"=>$row[0]->id,
                            "title"=>$row[0]->title,
                            "cover_path"=>$row[0]->url,
                            "description"=>$row[0]->description,
                            "authorized_start_datetime"=>$row[0]->authorizedstart,
                            "authorized_end_datetime"=>$row[0]->authorizedend,
                            "updated_at"=>$row[0]->updatedat,
                            "episodes"=>$episode
                        ]
                    ],200);
                }else{
                    return Controller::error(14);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function program(Request $request){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $row=DB::table("user")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($userid=="1"){
                    if($request->has("title")&&$request->has("description")&&$request->has("authorized_start_datetime")&&$request->hasFile("cover")){
                        $title=$request->input("title");
                        $description=$request->input("description");
                        $authorizedstart=$request->input("authorized_start_datetime");
                        $authorizedend=NULL;
                        if($request->has("authorized_end_datetime")){
                            $authorizedend=$request->input("authorized_end_datetime");
                        }
                        $cover=$request->file("cover");
                        if(is_string($title)&&is_string($description)&&preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/",$authorizedstart)&&(preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/",$authorizedend)||$authorizedend==NULL)){
                            if(in_array($cover->extension(),["jpg"])){
                                $row=DB::table("program")
                                    ->where("title","=",$title)
                                    ->select("*")->get();
                                if($row->isEmpty()){
                                    $path=$cover->store("upload");
                                    DB::table("program")->insert([
                                        "userid"=>$userid,
                                        "url"=>$path,
                                        "title"=>$title,
                                        "description"=>$description,
                                        "authorizedstart"=>$authorizedstart,
                                        "authorizedend"=>$authorizedend,
                                        "createdat"=>Controller::time()
                                    ]);
                                    $row=DB::table("program")
                                        ->select("*")->get();
                                    return response()->json([
                                        "success"=>true,
                                        "message"=>"",
                                        "data"=>[
                                            "id"=>$row[count($row)-1]->id,
                                            "url"=>url($path)
                                        ]
                                    ],200);
                                }else{
                                    return Controller::error(20);
                                }
                            }else{
                                return Controller::error(8);
                            }
                        }else{
                            return Controller::error(5);
                        }
                    }else{
                        return Controller::error(4);
                    }
                }else{
                    return Controller::error(3);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function editprogram(Request $request,$programid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                if($userid=="1"){
                    $row=DB::table("program")
                        ->where("id","=",$programid)
                        ->select("*")->get();
                    if($row->isNotEmpty()){
                        $title=$row[0]->title;
                        $description=$row[0]->description;
                        $authorizedstart=$row[0]->authorizedstart;
                        $authorizedend=$row[0]->authorizedend;
                        if($request->has("title")){ $title=$request->input("title"); }
                        if($request->has("description")){ $description=$request->input("description"); }
                        if($request->has("authorized_start_datetime")){ $authorizedstart=$request->input("authorized_start_datetime"); }
                        if($request->has("authorized_end_datetime")){ $authorizedend=$request->input("authorized_end_datetime"); }

                        if(is_string($title)&&is_string($description)&&preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/",$authorizedstart)&&(preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/",$authorizedend)||$authorizedend==NULL)){
                            $row=DB::table("program")
                                ->where("title","=",$title)
                                ->select("*")->get();
                            if($row->isEmpty()||$row[0]->id==$programid){
                                DB::table("program")
                                    ->where("id","=",$programid)
                                    ->update([
                                        "title"=>$title,
                                        "description"=>$description,
                                        "authorizedstart"=>$authorizedstart,
                                        "authorizedend"=>$authorizedend,
                                        "updatedat"=>Controller::time()
                                    ]);
                                return response()->json([
                                    "success"=>true,
                                    "message"=>"",
                                    "data"=>""
                                ],200);
                            }else{
                                return Controller::error(20);
                            }
                        }else{
                            return Controller::error(5);
                        }
                    }else{
                        return Controller::error(14);
                    }
                }else{
                    return Controller::error(3);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function addvideotoprogram(Request $request,$programid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                if($userid=="1"){
                    if($request->has("video_id")&&$request->has("title")){
                        $videoid=$request->input("video_id");
                        $title=$request->input("title");
                        if(is_int($videoid)&&is_string($title)){
                            $videorow=DB::table("video")
                                ->where("id","=",$videoid)
                                ->select("*")->get();
                            if($videorow->isNotEmpty()){
                                $row=DB::table("program")
                                    ->where("id","=",$programid)
                                    ->select("*")->get();
                                if($row->isNotEmpty()){
                                    $programvideolistrow=DB::table("programvideolist")
                                        ->where("videoid","=",$videoid)
                                        ->select("*")->get();
                                    if($programvideolistrow->isEmpty()){
                                        if($videorow[0]->visibility=="PUBLIC"){
                                            DB::table("programvideolist")->insert([
                                                "programid"=>$programid,
                                                "videoid"=>$videoid,
                                                "title"=>$title,
                                                "createdat"=>Controller::time()
                                            ]);
                                            return response()->json([
                                                "success"=>true,
                                                "message"=>"",
                                                "data"=>""
                                            ],200);
                                        }else{
                                            return Controller::error(21);
                                        }
                                    }else{
                                        return Controller::error(18);
                                    }
                                }else{
                                    return Controller::error(14);
                                }
                            }else{
                                return Controller::error(10);
                            }
                        }else{
                            return Controller::error(5);
                        }
                    }else{
                        return Controller::error(4);
                    }
                }else{
                    return Controller::error(3);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function delvideoformprogram(Request $request,$programid,$videoid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                if($userid=="1"){
                    $videorow=DB::table("video")
                        ->where("id","=",$videoid)
                        ->select("*")->get();
                    if($videorow->isNotEmpty()){
                        $row=DB::table("program")
                            ->where("id","=",$programid)
                            ->select("*")->get();
                        if($row->isNotEmpty()){
                            $programvideolistrow=DB::table("programvideolist")
                                ->where("videoid","=",$videoid)
                                ->where("programid","=",$programid)
                                ->select("*")->get();
                            if($programvideolistrow->isNotEmpty()){
                                DB::table("programvideolist")
                                    ->where("videoid","=",$videoid)
                                    ->where("programid","=",$programid)
                                    ->delete();

                                return response()->json([
                                    "success"=>true,
                                    "message"=>"",
                                    "data"=>""
                                ],200);
                            }else{
                                return Controller::error(16);
                            }
                        }else{
                            return Controller::error(14);
                        }
                    }else{
                        return Controller::error(10);
                    }
                }else{
                    return Controller::error(3);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function delprogram(Request $request,$programid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                if($userid=="1"){
                    $row=DB::table("program")
                        ->where("id","=",$programid)
                        ->select("*")->get();
                    if($row->isNotEmpty()){
                        DB::table("program")
                            ->where("id","=",$programid)
                            ->delete();
                        DB::table("programvideolist")
                            ->where("programid","=",$programid)
                            ->delete();
                        return response()->json([
                            "success"=>true,
                            "message"=>"",
                            "data"=>""
                        ]);
                    }else{
                        return Controller::error(14);
                    }
                }else{
                    return Controller::error(3);
                }
            }else{
                return Controller::error(1);
            }
        }
    }
?>