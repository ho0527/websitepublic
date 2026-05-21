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
                "string"=>19,
                "integer"=>19,
                "in"=>19
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
                    ->whereNull("deleted_at")
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
                ->whereNull("deleted_at")
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
                                        "track_order"=>count(DB::table("songs")->where("album_id","=",$albumid)->whereNull("deleted_at")->select("*")->get())+1,
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

        public function getstatistics(Request $request){
            $requestdata=Validator::make($request->all(),[
                "metrics"=>"required|string|in:label,album,song",
                "labels"=>"string"
            ],[
                "required"=>5,
                "string"=>5,
                "in"=>19
            ]);

            if($requestdata->fails()){
                return $this->error($requestdata->errors()->first());
            }

            $requestdata=$requestdata->validated();
            $labeltext=$requestdata["labels"]??null;
            $labels=$labeltext?array_values(array_filter(array_map("trim",explode(",",$labeltext)))):[];

            if(count($labels)>0){
                foreach($labels as $label){
                    $labelrow=DB::table("labels")
                        ->where("name","=",$label)
                        ->select("*")->first();
                    if(!$labelrow){
                        return $this->error(5);
                    }
                }
            }

            if($requestdata["metrics"]=="song"){
                $query=DB::table("songs")
                    ->leftJoin("user_view_logs","songs.song_id","=","user_view_logs.song_id")
                    ->whereNull("songs.deleted_at");

                if(count($labels)>0){
                    $query->whereExists(function($exists) use ($labels){
                        $exists->select(DB::raw(1))
                            ->from("song_labels")
                            ->join("labels","song_labels.label_id","=","labels.label_id")
                            ->whereColumn("song_labels.song_id","songs.song_id")
                            ->whereIn("labels.name",$labels);
                    });
                }

                $query->groupBy("songs.song_id")
                    ->orderByRaw("COUNT(DISTINCT user_view_logs.user_view_log_id) DESC")
                    ->orderBy("songs.song_id","asc")
                    ->select("songs.song_id",DB::raw("COUNT(DISTINCT user_view_logs.user_view_log_id) as view_count"));

                $row=$query->get();
                $data=[];
                foreach($row as $value){
                    $data[]=$this->songData($value->song_id);
                }

                return response()->json([
                    "success"=>true,
                    "data"=>$data
                ]);
            }

            if($requestdata["metrics"]=="album"){
                $query=DB::table("albums")
                    ->leftJoin("songs",function($join){
                        $join->on("albums.album_id","=","songs.album_id")
                            ->whereNull("songs.deleted_at");
                    })
                    ->leftJoin("user_view_logs","songs.song_id","=","user_view_logs.song_id")
                    ->whereNull("albums.deleted_at")
                    ->groupBy("albums.album_id")
                    ->orderByRaw("COUNT(user_view_logs.user_view_log_id) DESC")
                    ->orderBy("albums.album_id","asc")
                    ->select("albums.album_id",DB::raw("COUNT(user_view_logs.user_view_log_id) as total_view_count"));

                $row=$query->get();
                $data=[];
                foreach($row as $value){
                    $data[]=$this->albumData($value->album_id,(int)$value->total_view_count);
                }

                return response()->json([
                    "success"=>true,
                    "data"=>$data
                ]);
            }

            $query=DB::table("labels")
                ->leftJoin("song_labels","labels.label_id","=","song_labels.label_id")
                ->leftJoin("songs",function($join){
                    $join->on("song_labels.song_id","=","songs.song_id")
                        ->whereNull("songs.deleted_at");
                })
                ->leftJoin("user_view_logs","songs.song_id","=","user_view_logs.song_id")
                ->groupBy("labels.label_id","labels.name")
                ->orderByRaw("COUNT(user_view_logs.user_view_log_id) DESC")
                ->orderBy("labels.label_id","asc")
                ->select("labels.label_id","labels.name",DB::raw("COUNT(user_view_logs.user_view_log_id) as total_view_count"));
            $row=$query->get();
            $data=[];
            foreach($row as $value){
                $songrow=DB::table("songs")
                    ->join("song_labels","songs.song_id","=","song_labels.song_id")
                    ->leftJoin("user_view_logs","songs.song_id","=","user_view_logs.song_id")
                    ->where("song_labels.label_id","=",$value->label_id)
                    ->whereNull("songs.deleted_at")
                    ->groupBy("songs.song_id")
                    ->orderByRaw("COUNT(user_view_logs.user_view_log_id) DESC")
                    ->orderBy("songs.song_id","asc")
                    ->select("songs.song_id")
                    ->get();

                $songs=[];
                foreach($songrow as $songvalue){
                    $songs[]=$this->songData($songvalue->song_id);
                }

                $data[]=[
                    "total_view_count"=>(int)$value->total_view_count,
                    "label"=>$value->name,
                    "songs"=>$songs
                ];
            }

            return response()->json([
                "success"=>true,
                "data"=>$data
            ]);
        }

        public function editsongorder(Request $request,int $albumid){
            if($request->header("X-Authorization")){
                $tokendata=$this->logincheck($request->header("X-Authorization"));
                if($tokendata!=-1){
                    if($tokendata["role"]=="admin"){
                        $albumrow=DB::table("albums")
                            ->where("album_id","=",$albumid)
                            ->whereNull("deleted_at")
                            ->select("*")->first();

                        if(!$albumrow){
                            return $this->error(6);
                        }

                        $requestdata=Validator::make($request->all(),[
                            "song_ids"=>"required|array",
                            "song_ids.*"=>"integer"
                        ],[
                            "required"=>5,
                            "array"=>5,
                            "integer"=>5
                        ]);

                        if($requestdata->fails()){
                            return $this->error($requestdata->errors()->first());
                        }

                        $requestdata=$requestdata->validated();
                        $song_ids=$requestdata["song_ids"];

                        if(count($song_ids)!=count(array_unique($song_ids))){
                            return $this->error(19);
                        }

                        if(count($song_ids)<1){
                            return $this->error(19);
                        }

                        $album_song_ids=DB::table("songs")
                            ->where("album_id","=",$albumid)
                            ->whereNull("deleted_at")
                            ->pluck("song_id")
                            ->toArray();

                        foreach($song_ids as $song_id){
                            if(!in_array($song_id,$album_song_ids)){
                                return $this->error(6);
                            }
                        }

                        foreach($song_ids as $key=>$song_id){
                            DB::table("songs")
                                ->where("song_id","=",$song_id)
                                ->where("album_id","=",$albumid)
                                ->update([
                                    "track_order"=>$key+1,
                                    "updated_at"=>$this->time()
                                ]);
                        }

                        return response()->json([
                            "success"=>true,
                            "data"=>$this->albumSongs($albumid)
                        ]);
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

        public function editsong(Request $request,int $albumid,int $songid){
            if($request->header("X-Authorization")){
                $tokendata=$this->logincheck($request->header("X-Authorization"));
                if($tokendata!=-1){
                    if($tokendata["role"]=="admin"){
                        $albumrow=DB::table("albums")
                            ->where("album_id","=",$albumid)
                            ->whereNull("deleted_at")
                            ->select("*")->first();

                        if(!$albumrow){
                            return $this->error(6);
                        }

                        $row=DB::table("songs")
                            ->where("song_id","=",$songid)
                            ->where("album_id","=",$albumid)
                            ->whereNull("deleted_at")
                            ->select("*")->first();

                        if(!$row){
                            return $this->error(6);
                        }

                        $requestdata=Validator::make($request->all(),[
                            "title"=>"string",
                            "duration_seconds"=>"integer",
                            "label"=>"string",
                            "lyrics"=>"string",
                            "cover_image"=>"file|mimes:jpeg",
                            "is_cover"=>"string|in:true,false"
                        ],[
                            "string"=>5,
                            "integer"=>5,
                            "file"=>5,
                            "mimes"=>17,
                            "in"=>5
                        ]);

                        if($requestdata->fails()){
                            return $this->error($requestdata->errors()->first());
                        }

                        $requestdata=$requestdata->validated();

                        if(isset($requestdata["label"])){
                            $label=explode(",",$requestdata["label"]);
                            foreach($label as $key=>$value){
                                $label[$key]=trim($value);
                                $labelrow=DB::table("labels")
                                    ->where("name","=",$label[$key])
                                    ->select("*")->first();
                                if(!$labelrow){
                                    return $this->error(5);
                                }
                            }
                        }

                        $update=[
                            "title"=>$requestdata["title"]??$row->title,
                            "duration_seconds"=>$requestdata["duration_seconds"]??$row->duration_seconds,
                            "lyrics"=>$requestdata["lyrics"]??$row->lyrics,
                            "updated_at"=>$this->time()
                        ];

                        if(isset($requestdata["is_cover"])){
                            $is_cover=$requestdata["is_cover"]=="true"?true:false;
                            if($is_cover&&!$row->is_cover){
                                $coverrow=DB::table("songs")
                                    ->where("album_id","=",$albumid)
                                    ->where("is_cover","=",true)
                                    ->whereNull("deleted_at")
                                    ->select("*")->get();
                                if(2<count($coverrow)){
                                    return $this->error(16);
                                }
                            }
                            $update["is_cover"]=$is_cover;
                        }

                        if(isset($requestdata["cover_image"])){
                            $update["cover_image_path"]=$requestdata["cover_image"]->store("public/image");
                        }

                        DB::table("songs")
                            ->where("song_id","=",$songid)
                            ->where("album_id","=",$albumid)
                            ->update($update);

                        if(isset($label)){
                            DB::table("song_labels")
                                ->where("song_id","=",$songid)
                                ->delete();

                            foreach($label as $value){
                                $labelrow=DB::table("labels")
                                    ->where("name","=",$value)
                                    ->select("*")->first();
                                DB::table("song_labels")->insert([
                                    "song_id"=>$songid,
                                    "label_id"=>$labelrow->label_id
                                ]);
                            }
                        }

                        $row=DB::table("songs")
                            ->where("song_id","=",$songid)
                            ->select("*")->first();

                        $songlabeldata=$this->songLabels($songid);
                        $userviewlogrow=DB::table("user_view_logs")
                            ->where("song_id","=",$songid)
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

        private function songLabels(int $songid){
            $data=[];
            $songlabelrow=DB::table("song_labels")
                ->where("song_id","=",$songid)
                ->select("*")->get();

            for($i=0;$i<count($songlabelrow);$i=$i+1){
                $labelrow=DB::table("labels")
                    ->where("label_id","=",$songlabelrow[$i]->label_id)
                    ->select("*")->first();
                if($labelrow){
                    $data[]=$labelrow->name;
                }
            }

            return $data;
        }

        private function songViewCount(int $songid){
            return count(DB::table("user_view_logs")
                ->where("song_id","=",$songid)
                ->select("*")->get());
        }

        private function songData(int $songid){
            $row=DB::table("songs")
                ->where("song_id","=",$songid)
                ->whereNull("deleted_at")
                ->select("*")->first();

            return [
                "id"=>$row->song_id,
                "album_id"=>$row->album_id,
                "title"=>$row->title,
                "duration_seconds"=>$row->duration_seconds,
                "order"=>$row->track_order,
                "label"=>$this->songLabels($row->song_id),
                "view_count"=>$this->songViewCount($row->song_id),
                "is_cover"=>$row->is_cover,
                "lyrics"=>$row->lyrics,
                "cover_image_url"=>"/api/songs/".$row->song_id."/cover",
                "created_at"=>$this->timestarp($row->created_at),
                "updated_at"=>$this->timestarp($row->updated_at)
            ];
        }

        private function albumData(int $albumid,int $total_view_count){
            $row=DB::table("albums")
                ->where("album_id","=",$albumid)
                ->whereNull("deleted_at")
                ->select("*")->first();

            $userrow=DB::table("users")
                ->where("user_id","=",$row->publisher_id)
                ->select("*")->first();

            return [
                "id"=>$row->album_id,
                "title"=>$row->title,
                "artist"=>$row->artist,
                "release_year"=>$row->release_year,
                "genre"=>$row->genre,
                "description"=>$row->description,
                "publisher"=>[
                    "id"=>$userrow->user_id,
                    "username"=>$userrow->username,
                    "email"=>$userrow->email,
                ],
                "created_at"=>$this->timestarp($row->created_at),
                "updated_at"=>$this->timestarp($row->updated_at),
                "total_view_count"=>$total_view_count
            ];
        }

        private function albumSongs(int $albumid){
            $data=[];
            $songrow=DB::table("songs")
                ->where("album_id","=",$albumid)
                ->whereNull("deleted_at")
                ->orderBy("track_order","asc")
                ->select("*")->get();

            for($i=0;$i<count($songrow);$i=$i+1){
                $data[]=[
                    "id"=>$songrow[$i]->song_id,
                    "album_id"=>$songrow[$i]->album_id,
                    "title"=>$songrow[$i]->title,
                    "label"=>$this->songLabels($songrow[$i]->song_id),
                    "duration_seconds"=>$songrow[$i]->duration_seconds,
                    "is_cover"=>$songrow[$i]->is_cover,
                    "order"=>$songrow[$i]->track_order,
                    "cover"=>"/api/songs/".$songrow[$i]->song_id."/cover"
                ];
            }

            return $data;
        }
    }
?>
