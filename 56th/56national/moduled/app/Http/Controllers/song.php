<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use PhpParser\Node\Stmt\Const_;

    class song extends Controller{
        public function getsonglist(Request $request){
            $requestdata=Validator::make($request->all(),[
                "keyword"=>"string",
                "limit"=>"integer",
                "cursor"=>"string"
            ],[
                "string"=>5,
                "integer"=>5,
                "in"=>5
            ]);

            if(!$requestdata->fails()){
                $requestdata=$requestdata->validated();

                $keyword=$requestdata["keyword"]??"";
                $limit=$requestdata["limit"]??10;
                $cursor=$requestdata["cursor"]??null;

                if($cursor!=null){
                    $cursor=json_decode(base64_decode($cursor),true);
                    if(isset($cursor["id"])){
                        $cursor=$cursor["id"];
                    }else{
                        return $this->error(20);
                    }
                }else{
                    $cursor=0;
                }

                if($limit<1||100<$limit){
                    return $this->error(19);
                }

                $data=[];
                $row=DB::table("songs")
                    ->where("song_id",">",$cursor)
                    ->where("title","like","%".$keyword."%")
                    ->limit($limit)
                    ->select("*")->get();

                for($i=0;$i<count($row);$i=$i+1){
                    $songlabeldata=[];
                    $songlabelrow=DB::table("song_labels")
                        ->where("song_id","=",$row[$i]->song_id)
                        ->select("*")->get();

                    for($j=0;$j<count($songlabelrow);$j=$j+1){
                        $labelrow=DB::table("labels")
                            ->where("label_id","=",$songlabelrow[$j]->label_id)
                            ->select("*")->first();
                        $songlabeldata[]=$labelrow->name;
                    }

                    $data[]=[
                        "id"=>$row[$i]->song_id,
                        "album_id"=>$row[$i]->album_id,
                        "title"=>$row[$i]->title,
                        "duration_seconds"=>$row[$i]->duration_seconds,
                        "cover_image_url"=>"/api/songs/".$row[$i]->song_id."/cover",
                        "label"=>$songlabeldata,
                        "album_title"=>DB::table("albums")
                            ->where("album_id","=",$row[$i]->album_id)
                            ->select("title")->first()->title
                    ];
                }

                return response()->json([
                    "success"=>true,
                    "data"=>$data,
                    "meta"=>[
                        "prev_cursor"=>$cursor>0?base64_encode(json_encode(["id"=>$cursor])):null,
                        "next_cursor"=>count($row)==$limit?base64_encode(json_encode(["id"=>$row[count($row)-1]->song_id])):null
                    ]
                ]);
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function getsong(Request $request,int $songid){
            if($request->header("X-Authorization")){
                $tokendata=$this->logincheck($request->header("X-Authorization"));
                if($tokendata!=-1){
                    $row=DB::table("songs")
                        ->where("song_id","=",$songid)
                        ->whereNull("deleted_at")
                        ->select("*")->first();

                    if($row){
                        $songlabeldata=[];
                        $songlabelrow=DB::table("song_labels")
                            ->where("song_id","=",$row->song_id)
                            ->select("*")->get();

                        for($j=0;$j<count($songlabelrow);$j=$j+1){
                            $labelrow=DB::table("labels")
                                ->where("label_id","=",$songlabelrow[$j]->label_id)
                                ->select("*")->first();
                            $songlabeldata[]=$labelrow->name;
                        }

                        DB::table("user_view_logs")
                            ->insert([
                                "user_id"=>$tokendata["id"],
                                "song_id"=>$row->song_id
                            ]);

                        $userviewlogrow=DB::table("user_view_logs")
                            ->where("song_id","=",$row->song_id)
                            ->select("*")->get();

                        return response()->json([
                            "success"=>true,
                            "data"=>[
                                "id"=>$row->song_id,
                                "album_id"=>$row->album_id,
                                "title"=>$row->title,
                                "duration_seconds"=>$row->duration_seconds,
                                "order"=>$row->track_order,
                                "label"=>$songlabeldata,
                                "view_count"=>count($userviewlogrow),
                                "is_cover"=>$row->is_cover,
                                "lyrics"=>$row->lyrics,
                                "cover_image_url"=>"/api/songs/".$row->song_id."/cover",
                                "created_at"=>$row->created_at,
                                "updated_at"=>$row->updated_at
                            ]
                        ]);
                    }else{
                        return $this->error(6);
                    }
                }else{
                    return $this->error(3);
                }
            }else{
                return $this->error(9);
            }
        }

        public function getsongcover(Request $request,int $songid){
            $row=DB::table("songs")
                ->where("song_id","=",$songid)
                ->select("*")->first();

            if($row){
                if($row->cover_image_path){
                    $path=storage_path("app/".$row->cover_image_path);
                    return response()->file($path);
                }else{
                    return $this->error(7);
                }
            }else{
                return $this->error(6);
            }
        }

        public function newsong(Request $request,int $albumid){
            if($request->header("X-Authorization")){
                $tokendata=$this->logincheck($request->header("X-Authorization"));
                if($tokendata!=-1){
                    if($tokendata["role"]=="admin"){
                        $row=DB::table("albums")
                            ->where("album_id","=",$albumid)
                            ->select("*")->first();
                        if($row){
                            if($row->publisher_id==$tokendata["id"]){
                                $requestdata=Validator::make($request->all(),[
                                    "title"=>"required|string",
                                    "duration_seconds"=>"required|integer",
                                    "label"=>"string",
                                    "lyrics"=>"required|string",
                                    "cover_image"=>"required|file|mimes:jpeg",
                                    "is_cover"=>"required|string|in:true,false"
                                ],[
                                    "required"=>5,
                                    "string"=>5,
                                    "integer"=>5,
                                    "file"=>5,
                                    "mimes"=>17,
                                    "in"=>5
                                ]);
                                if(!$requestdata->fails()){
                                    $requestdata=$requestdata->validated();
                                    $title=$requestdata["title"];
                                    $duration_seconds=$requestdata["duration_seconds"];
                                    $label=explode(",",$requestdata["label"]);
                                    $lyrics=$requestdata["lyrics"];
                                    $is_cover=$requestdata["is_cover"]=="true"?true:false;
                                    if($is_cover){
                                        $coverrow=DB::table("songs")
                                            ->where("album_id","=",$albumid)
                                            ->where("is_cover","=",true)
                                            ->whereNull("deleted_at")
                                            ->select("*")->get();
                                        if(2<count($coverrow)){
                                            return $this->error(16);
                                        }
                                    }
                                    foreach($label as $key=>$value){
                                        $labelrow=DB::table("labels")
                                            ->where("name","=",$value)
                                            ->select("*")->first();
                                        if(!$labelrow){
                                            return $this->error(5);
                                        }
                                    }

                                    $cover_image_path=$requestdata["cover_image"]->store("public/image");

                                    DB::table("songs")->insert([
                                        "album_id"=>$albumid,
                                        "title"=>$title,
                                        "duration_seconds"=>$duration_seconds,
                                        "lyrics"=>$lyrics,
                                        "is_cover"=>$is_cover,
                                        "cover_image_path"=>$cover_image_path,
                                        "track_order"=>count(DB::table("songs")->where("album_id","=",$albumid)->select("*")->get())+1,
                                        "created_at"=>$this->time(),
                                        "updated_at"=>$this->time()
                                    ]);

                                    $row=DB::table("songs")
                                        ->select("*")->get();
                                    $row=$row[count($row)-1];

                                    foreach($label as $key=>$value){
                                        $labelrow=DB::table("labels")
                                            ->where("name","=",$value)
                                            ->select("*")->first();
                                        DB::table("song_labels")->insert([
                                            "song_id"=>$row->song_id,
                                            "label_id"=>$labelrow->label_id
                                        ]);
                                    }

                                    return response()->json([
                                        "success"=>true,
                                        "data"=>[
                                            "id"=>$row->song_id,
                                            "album_id"=>$row->album_id,
                                            "title"=>$row->title,
                                            "duration_seconds"=>$row->duration_seconds,
                                            "order"=>$row->track_order,
                                            "label"=>$label,
                                            "view_count"=>0,
                                            "is_cover"=>$row->is_cover,
                                            "lyrics"=>$row->lyrics,
                                            "cover_image_url"=>"/api/songs/".$row->song_id."/cover",
                                            "created_at"=>$row->created_at,
                                            "updated_at"=>$row->updated_at
                                        ]
                                    ],201);
                                }else{
                                    return $this->error($requestdata->errors()->first());
                                }
                            }else{
                                return $this->error(18);
                            }
                        }else{
                            return $this->error(6);
                        }
                    }else{
                        return $this->error(8);
                    }
                }else{
                    return $this->error(3);
                }
            }else{
                return $this->error(9);
            }
        }

        public function deletesong(Request $request,int $albumid,int $songid){
            if($request->header("X-Authorization")){
                $tokendata=$this->logincheck($request->header("X-Authorization"));
                if($tokendata!=-1){
                    if($tokendata["role"]=="admin"){
                        $row=DB::table("songs")
                            ->where("song_id","=",$songid)
                            ->where("album_id","=",$albumid)
                            ->whereNull("deleted_at")
                            ->select("*")->first();
                        if($row){
                            DB::table("songs")
                                ->where("song_id","=",$songid)
                                ->update([
                                    "deleted_at"=>$this->time()
                                ]);

                            return response()->json([
                                "success"=>true
                            ]);
                        }else{
                            return $this->error(6);
                        }
                    }else{
                        return $this->error(8);
                    }
                }else{
                    return $this->error(3);
                }
            }else{
                return $this->error(9);
            }
        }
    }
?>