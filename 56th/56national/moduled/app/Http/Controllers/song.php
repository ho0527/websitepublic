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
        public function getsongtype(Request $request){
            $requestdata=Validator::make($request->all(),[
                "order_by"=>"string|in:created_at",
                "order_type"=>"string|in:asc,desc",
                "page"=>"interger",
                "page_size"=>"interger"
            ],[
                "string"=>5,
                "interger"=>5,
                "in"=>5
            ]);

            if(!$requestdata->fails()){
                if($request->header("Authorization")){
                    $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata!=-1){
                        if($tokendata["type"]=="ADMIN"){
                            $data=[];
                            $row=DB::table("song_types")
                                ->orderBy($request["order_by"]??"created_at",$request["order_type"]??"asc")
                                ->skip(($request["page"]??1-1)*($request["page_size"]??10))
                                ->take($request["page_size"]??10)
                                ->select("*")->get();

                            for($i=0;$i<count($row);$i=$i+1){
                                $input=[];

                                $inputrow=DB::table("song_type_inputs")
                                    ->where("song_type_id","=",$row[$i]->id)
                                    ->select("*")->get();

                                for($j=0;$j<count($inputrow);$j=$j+1){
                                    $input[]=[
                                         "name"=>$inputrow[$j]->name,
                                         "type"=>$inputrow[$j]->type
                                    ];
                                }

                                $data[]=[
                                    "id"=>$row[$i]->id,
                                    "name"=>$row[$i]->name,
                                    "inputs"=>$input,
                                    "created_at"=>$this->timestarp($row[$i]->created_at)
                                ];
                            }

                            $row=DB::table("song_types")
                                ->select("*")->get();

                            return response()->json([
                                "success"=>true,
                                "data"=>[
                                    "total_count"=>count($row),
                                    "posts"=>$data
                                ]
                            ]);
                        }else{
                            return $this->error(3);
                        }
                    }else{
                        return $this->error(2);
                    }
                }else{
                    return $this->error(2);
                }
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function getsonglist(Request $request){
            $requestdata=Validator::make($request->all(),[
                "capital"=>"string",
                "year"=>"string",
                "limit"=>"integer",
                "cursor"=>"string"
            ],[
                "string"=>5,
                "integer"=>5,
                "in"=>5
            ]);

            if(!$requestdata->fails()){
                $requestdata=$requestdata->validated();

                $capital=$requestdata["capital"]??"";
                $year=$requestdata["year"]??"0-9999";
                $limit=$requestdata["limit"]??"10";
                $cursor=$requestdata["cursor"]??null;

                $yearsplit=explode("-",$year);
                if(count($yearsplit)==2&&is_numeric($yearsplit[0])&&is_numeric($yearsplit[1])&&$yearsplit[0]<=$yearsplit[1]){
                    $startyear=$yearsplit[0];
                    $endyear=$yearsplit[1];
                }else{
                    return $this->error(5);
                }

                if($cursor!=null){
                    $cursor=json_decode(base64_decode($cursor),true);
                    if($cursor["id"]){
                        $cursor=$cursor["id"];
                    }else{
                        return $this->error(5);
                    }
                }else{
                    $cursor=0;
                }

                $data=[];
                $row=DB::table("songs")
                    ->where("song_id",">=",$cursor)
                    ->where("release_year",">=",$startyear)
                    ->where("release_year","<=",$endyear)
                    ->where("title","like",$capital."%")
                    ->limit($limit)
                    ->select("*")->get();

                for($i=0;$i<count($row);$i=$i+1){
                    $userrow=DB::table("users")
                        ->where("user_id","=",$row[$i]->publisher_id)
                        ->select("*")->first();

                    $data[]=[
                        "id"=>$row[$i]->song_id,
                        "title"=>$row[$i]->title,
                        "artist"=>$row[$i]->artist,
                        "release_year"=>$row[$i]->release_year,
                        "publisher"=>[
                            "id"=>$userrow->user_id,
                            "username"=>$userrow->username,
                            "email"=>$userrow->email,
                        ]
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
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("X-Authorization"))[1]);
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
                            $songlabeldata[]=$labelrow->label;
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
                if($row->is_cover){
                    $path=storage_path("app/".$row->cover_image_path);
                    return response()->file($path);
                }else{
                    return $this->error(7);
                }
            }else{
                return $this->error(6);
            }
        }

        public function newsong(Request $request){
            $requestdata=Validator::make($request->all(),[
                "type"=>"required|integer",
                "inputs"=>"required|array"
            ],[
                "required"=>4,
                "integer"=>5,
                "array"=>5
            ]);

            if(!$requestdata->fails()){
                $requestdata=$requestdata->validated();
                if($request->header("Authorization")){
                    $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata!=-1){
                        if($tokendata["type"]=="USER"){
                            $row=DB::table("song_types")
                                ->where("id","=",$requestdata["type"])
                                ->where("deleted_at","=",NULL)
                                ->select("*")->first();

                            if($row){
                                $quotacount=DB::table("user_quota_transactions")
                                    ->where("user_id","=",$tokendata["id"])
                                    ->sum("value");
                                if(0<$quotacount){
                                    foreach($requestdata["inputs"] as $key=>$value){
                                        $typeinputrow=DB::table("song_type_inputs")
                                            ->where("song_type_id","=",$requestdata["type"])
                                            ->where("name","=",$key)
                                            ->select("*")->first();
                                        if(!$typeinputrow){
                                            return $this->error(12);
                                        }
                                        if($typeinputrow->type=="string"){
                                            if(!is_string($value)){
                                                return $this->error(15);
                                            }
                                        }elseif($typeinputrow->type=="number"){
                                            if(!is_numeric($value)){
                                                return $this->error(15);
                                            }
                                        }elseif($typeinputrow->type=="boolean"){
                                            if(!is_bool($value)){
                                                return $this->error(15);
                                            }
                                        }
                                    }

                                    DB::table("songs")->insert([
                                        "song_type_id"=>$requestdata["type"],
                                        "user_id"=>$tokendata["id"],
                                        "status"=>"pending",
                                        "created_at"=>$this->time()
                                    ]);

                                    $row=DB::table("songs")
                                        ->select("*")->get();
                                    $row=$row[count($row)-1];

                                    foreach($requestdata["inputs"] as $key=>$value){
                                        $typeinputrow=DB::table("song_type_inputs")
                                            ->where("song_type_id","=",$requestdata["type"])
                                            ->where("name","=",$key)
                                            ->select("*")->first();

                                        DB::table("song_inputs")->insert([
                                            "song_id"=>$row->id,
                                            "name"=>$typeinputrow->name,
                                            "type"=>$typeinputrow->type,
                                            "value"=>$value
                                        ]);
                                    }

                                    $userrow=DB::table("users")
                                        ->where("id","=",$tokendata["id"])
                                        ->select("*")->first();

                                    $typerow=DB::table("song_types")
                                        ->where("id","=",$requestdata["type"])
                                        ->select("*")->first();

                                    $typeinputrow=DB::table("song_type_inputs")
                                        ->where("song_type_id","=",$requestdata["type"])
                                        ->select("*")->get();

                                    $input=[];

                                    for($i=0;$i<count($typeinputrow);$i=$i+1){
                                        $input[]=[
                                            "name"=>$typeinputrow[$i]->name,
                                            "type"=>$typeinputrow[$i]->type
                                        ];
                                    }

                                    DB::table("user_quota_transactions")->insert([
                                        "user_id"=>$tokendata["id"],
                                        "value"=>-1,
                                        "reason"=>"CONSUME",
                                        "created_at"=>$this->time()
                                    ]);

                                    return response()->json([
                                        "success"=>true,
                                        "data"=>[
                                            "id"=>$row->id,
                                            "type"=>[
                                                "id"=>$typerow->id,
                                                "name"=>$typerow->name,
                                                "inputs"=>$input,
                                                "created_at"=>$typerow->created_at
                                            ],
                                            "user"=>[
                                                "id"=>$userrow->id,
                                                "email"=>$userrow->email,
                                                "nickname"=>$userrow->nickname,
                                                "profile_image"=>url($userrow->profile_image),
                                                "type"=>$userrow->type,
                                                "created_at"=>$userrow->created_at,
                                            ],
                                            "worker"=>NULL,
                                            "status"=>$row->status,
                                            "result"=>$row->result,
                                            "created_at"=>$row->created_at,
                                            "updated_at"=>$row->updated_at
                                        ]
                                    ]);
                                }else{
                                    return $this->error(9);
                                }
                            }else{
                                return $this->error(7);
                            }
                        }else{
                            return $this->error(3);
                        }
                    }else{
                        return $this->error(2);
                    }
                }else{
                    return $this->error(2);
                }
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function newsongtype(Request $request){
            $requestdata=Validator::make($request->all(),[
                "name"=>"required|string",
                "inputs"=>"required|array",
                "inputs.*.name"=>"required|string",
                "inputs.*.type"=>"required|string|in:string,number,boolean"
            ],[
                "required"=>4,
                "string"=>5,
                "array"=>5,
                "in"=>5
            ]);

            if(!$requestdata->fails()){
                if(preg_match("/^([a-z]|_|[0-9])+$/",$request["name"])){
                    if($request->header("Authorization")){
                        $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                        if($tokendata!=-1){
                            if($tokendata["type"]=="ADMIN"){
                                $tempnamelist=[];

                                for($i=0;$i<count($request["inputs"]);$i=$i+1){
                                    if(!in_array($request["inputs"][$i]["name"],$tempnamelist)){
                                        $tempnamelist[]=$request["inputs"][$i]["name"];
                                    }else{
                                        return $this->error(8);
                                    }
                                }

                                $row=DB::table("song_types")
                                    ->where("name","=",$request["name"])
                                    ->where("deleted_at","=",NULL)
                                    ->select("*")->get();

                                if($row->isEmpty()){
                                    DB::table("song_types")->insert([
                                        "name"=>$request["name"],
                                        "created_at"=>$this->time()
                                    ]);
                                    $row=DB::table("song_types")
                                        ->where("name","=",$request["name"])
                                        ->select("*")->get();

                                    $row=$row[count($row)-1];
                                    $songtypeid=$row->id;

                                    for($i=0;$i<count($request["inputs"]);$i=$i+1){
                                        DB::table("song_type_inputs")->insert([
                                            "song_type_id"=>$songtypeid,
                                            "name"=>$request["inputs"][$i]["name"],
                                            "type"=>$request["inputs"][$i]["type"]
                                        ]);
                                    }

                                    return response()->json([
                                        "success"=>true,
                                        "data"=>[
                                            "id"=>$songtypeid,
                                            "name"=>$request["name"],
                                            "inputs"=>$request["inputs"],
                                            "created_at"=>$row->created_at
                                        ]
                                    ]);
                                }else{
                                    return $this->error(16);
                                }
                            }else{
                                return $this->error(3);
                            }
                        }else{
                            return $this->error(2);
                        }
                    }else{
                        return $this->error(2);
                    }
                }else{
                    return $this->error(5);
                }
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function deletesongtype(Request $request,$songtypeid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata!=-1){
                    if($tokendata["type"]=="ADMIN"){
                        $row=DB::table("song_types")
                            ->where("id","=",$songtypeid)
                            ->where("deleted_at","=",NULL)
                            ->select("*")->get();
                        if($row->isNotEmpty()){
                            DB::table("song_types")
                                ->where("id","=",$songtypeid)
                                ->update([
                                    "deleted_at"=>$this->time()
                                ]);

                            return response()->json([
                                "success"=>true,
                                "data"=>""
                            ]);
                        }else{
                            return $this->error(12);
                        }
                    }else{
                        return $this->error(3);
                    }
                }else{
                    return $this->error(2);
                }
            }else{
                return $this->error(2);
            }
        }

        public function cancelsong(Request $request,$songid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata!=-1){
                    if($tokendata["type"]=="USER"){
                        $row=DB::table("songs")
                            ->where("id","=",$songid)
                            ->where("user_id","=",$tokendata["id"])
                            ->select("*")->first();

                        if($row){
                            if($row->status=="pending"){
                                DB::table("songs")
                                    ->where("id","=",$songid)
                                    ->update([
                                        "status"=>"canceled",
                                        "updated_at"=>$this->time()
                                    ]);

                                return response()->json([
                                    "success"=>true,
                                    "data"=>""
                                ]);
                            }else{
                                return $this->error(19);
                            }
                        }else{
                            return $this->error(7);
                        }
                    }else{
                        return $this->error(3);
                    }
                }else{
                    return $this->error(2);
                }
            }else{
                return $this->error(2);
            }
        }
    }
?>