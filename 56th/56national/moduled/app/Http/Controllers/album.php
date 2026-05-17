<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use PhpParser\Node\Stmt\Const_;

    class album extends Controller{
        public function getalbumtype(Request $request){
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
                            $row=DB::table("album_types")
                                ->orderBy($request["order_by"]??"created_at",$request["order_type"]??"asc")
                                ->skip(($request["page"]??1-1)*($request["page_size"]??10))
                                ->take($request["page_size"]??10)
                                ->select("*")->get();

                            for($i=0;$i<count($row);$i=$i+1){
                                $input=[];

                                $inputrow=DB::table("album_type_inputs")
                                    ->where("album_type_id","=",$row[$i]->id)
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

                            $row=DB::table("album_types")
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

        public function getalbumlist(Request $request){
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
                $row=DB::table("albums")
                    ->where("album_id",">=",$cursor)
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
                        "id"=>$row[$i]->album_id,
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
                        "next_cursor"=>count($row)==$limit?base64_encode(json_encode(["id"=>$row[count($row)-1]->album_id])):null
                    ]
                ]);
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function getalbum(Request $request,int $albumid){
            $row=DB::table("albums")
                ->where("album_id","=",$albumid)
                ->select("*")->first();

            if($row){
                $userrow=DB::table("users")
                    ->where("user_id","=",$row->publisher_id)
                    ->select("*")->first();

                return response()->json([
                    "success"=>true,
                    "data"=>[
                        "id"=>$row->album_id,
                        "title"=>$row->title,
                        "artist"=>$row->artist,
                        "release_year"=>$row->release_year,
                        "genre"=>$row->genre,
                        "description"=>$row->description,
                        "created_at"=>$row->created_at,
                        "updated_at"=>$row->updated_at,
                        "publisher"=>[
                            "id"=>$userrow->user_id,
                            "username"=>$userrow->username,
                            "email"=>$userrow->email,
                        ]
                    ]
                ]);
            }else{
                return $this->error(6);
            }
        }

        public function getalbumcover(Request $request,int $albumid){
            $row=DB::table("albums")
                ->where("album_id","=",$albumid)
                ->select("*")->first();

            if($row){
                $songrow=DB::table("songs")
                    ->where("album_id","=",$row->album_id)
                    ->where("is_cover","=",true)
                    ->orderBy("track_order","asc")
                    ->select("*")->first();

                if($songrow){
                    $path=storage_path("app/".$songrow->cover_image_path);
                    return response()->file($path);
                }else{
                    return $this->error(7);
                }
            }else{
                return $this->error(6);
            }
        }

        public function getalbumsonglist(Request $request,int $albumid){
            $row=DB::table("albums")
                ->where("album_id","=",$albumid)
                ->select("*")->first();

            if($row){
                $data=[];

                $songrow=DB::table("songs")
                    ->where("album_id","=",$row->album_id)
                    ->whereNull("deleted_at")
                    ->select("*")->get();

                for($i=0;$i<count($songrow);$i=$i+1){
                    $songlabeldata=[];
                    $songlabelrow=DB::table("song_labels")
                        ->where("song_id","=",$songrow[$i]->song_id)
                        ->select("*")->get();

                    for($j=0;$j<count($songlabelrow);$j=$j+1){
                        $labelrow=DB::table("labels")
                            ->where("label_id","=",$songlabelrow[$j]->label_id)
                            ->select("*")->first();
                        $songlabeldata[]=$labelrow->label;
                    }

                    $data[]=[
                        "id"=>$songrow[$i]->song_id,
                        "album_id"=>$songrow[$i]->album_id,
                        "title"=>$songrow[$i]->title,
                        "label"=>$songlabeldata,
                        "duration_seconds"=>$songrow[$i]->duration_seconds,
                        "is_cover"=>$songrow[$i]->is_cover,
                        "order"=>$songrow[$i]->track_order,
                        "cover"=>"/api/songs/".$songrow[$i]->song_id."/cover"
                    ];
                }

                return response()->json([
                    "success"=>true,
                    "data"=>$data
                ]);
            }else{
                return $this->error(6);
            }
        }

        public function newalbum(Request $request){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata!=-1){
                    if($tokendata["type"]=="admin"){
                        $requestdata=Validator::make($request->all(),[
                            "title"=>"required|string",
                            "artist"=>"required|string",
                            "release_year"=>"required|integer",
                            "genre"=>"required|string",
                            "description"=>"required|string"
                        ],[
                            "required"=>5,
                            "string"=>5,
                            "integer"=>15,
                        ]);
                        if(!$requestdata->fails()){
                            $requestdata=$requestdata->validated();
                            $title=$requestdata["title"];
                            $artist=$requestdata["artist"];
                            $release_year=$requestdata["release_year"];
                            $genre=$requestdata["genre"];
                            $description=$requestdata["description"];

                            DB::table("albums")->insert([
                                "title"=>$title,
                                "artist"=>$artist,
                                "release_year"=>$release_year,
                                "genre"=>$genre,
                                "description"=>$description,
                                "publisher_id"=>$tokendata["id"],
                                "created_at"=>$this->time(),
                                "updated_at"=>$this->time()
                            ]);

                            $row=DB::table("albums")
                                ->select("*")->get();
                            $row=$row[count($row)-1];

                            $userrow=DB::table("users")
                                ->where("user_id","=",$tokendata["id"])
                                ->select("*")->first();
                            return response()->json([
                                "success"=>true,
                                "data"=>[
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
                                    "updated_at"=>$this->timestarp($row->updated_at)
                                ]
                            ],201);
                        }else{
                            return $this->error($requestdata->errors()->first());
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

        public function editalbum(Request $request,int $albumid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata!=-1){
                    if($tokendata["type"]=="admin"){
                        $row=DB::table("albums")
                            ->where("album_id","=",$albumid)
                            ->select("*")->first();

                        if($row){
                            $requestdata=Validator::make($request->all(),[
                                "title"=>"required|string",
                                "description"=>"required|string"
                            ],[
                                "required"=>5,
                                "string"=>5,
                            ]);
                            if(!$requestdata->fails()){
                                $requestdata=$requestdata->validated();
                                $title=$requestdata["title"];
                                $description=$requestdata["description"];

                                DB::table("albums")
                                    ->where("album_id","=",$albumid)
                                    ->update([
                                        "title"=>$title,
                                        "description"=>$description
                                    ]);

                                $row=DB::table("albums")
                                    ->where("album_id","=",$albumid)
                                    ->select("*")->first();

                                $userrow=DB::table("users")
                                    ->where("user_id","=",$tokendata["id"])
                                    ->select("*")->first();
                                return response()->json([
                                    "success"=>true,
                                    "data"=>[
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
                                        "updated_at"=>$this->timestarp($row->updated_at)
                                    ]
                                ]);
                            }else{
                                return $this->error($requestdata->errors()->first());
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

        public function newalbumtype(Request $request){
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

                                $row=DB::table("album_types")
                                    ->where("name","=",$request["name"])
                                    ->where("deleted_at","=",NULL)
                                    ->select("*")->get();

                                if($row->isEmpty()){
                                    DB::table("album_types")->insert([
                                        "name"=>$request["name"],
                                        "created_at"=>$this->time()
                                    ]);
                                    $row=DB::table("album_types")
                                        ->where("name","=",$request["name"])
                                        ->select("*")->get();

                                    $row=$row[count($row)-1];
                                    $albumtypeid=$row->id;

                                    for($i=0;$i<count($request["inputs"]);$i=$i+1){
                                        DB::table("album_type_inputs")->insert([
                                            "album_type_id"=>$albumtypeid,
                                            "name"=>$request["inputs"][$i]["name"],
                                            "type"=>$request["inputs"][$i]["type"]
                                        ]);
                                    }

                                    return response()->json([
                                        "success"=>true,
                                        "data"=>[
                                            "id"=>$albumtypeid,
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

        public function deletealbum(Request $request,int $albumid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata!=-1){
                    if($tokendata["type"]=="admin"){
                        $row=DB::table("admin")
                            ->where("id","=",$albumtypeid)
                            ->where("deleted_at","=",NULL)
                            ->select("*")->get();
                        if($row->isNotEmpty()){
                            DB::table("album_types")
                                ->where("id","=",$albumtypeid)
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

        public function cancelalbum(Request $request,$albumid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata!=-1){
                    if($tokendata["type"]=="USER"){
                        $row=DB::table("albums")
                            ->where("id","=",$albumid)
                            ->where("user_id","=",$tokendata["id"])
                            ->select("*")->first();

                        if($row){
                            if($row->status=="pending"){
                                DB::table("albums")
                                    ->where("id","=",$albumid)
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