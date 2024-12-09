<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;

    class image extends Controller{
        public function search(Request $request){
            $requestdata=Validator::make($request->all(),[
                    "order_by"=>"nullable|in:created_at,updated_at",
                    "order_type"=>"nullable|in:asc,desc",
                    "keyword"=>"nullable",
                    "page"=>"nullable|min:1",
                    "pagesize"=>"nullable|min:1|max:100",
                ],[
                "in"=>6,
                "min"=>6,
                "max"=>6
            ]);

            if(!$requestdata->fails()){
                $requestdata=$requestdata->validate();
                $requestdata["order_by"]=$requestdata["order_by"]??"created_at";
                $requestdata["order_type"]=$requestdata["order_type"]??"desc";
                $requestdata["keyword"]=$requestdata["keyword"]??"";
                $requestdata["page"]=$requestdata["page"]??1;
                $requestdata["pagesize"]=$requestdata["pagesize"]??10;

                $row=DB::table("images")
                    ->where("title","LIKE","%".$requestdata["keyword"]."%")
                    ->where("description","LIKE","%".$requestdata["keyword"]."%")
                    ->where("deleted_at","=",NULL)
                    ->orderBy($requestdata["order_by"],$requestdata["order_type"])
                    ->skip(($requestdata["page"]-1)*$requestdata["pagesize"])
                    ->take($requestdata["pagesize"])
                    ->select("*")->get();
                return response()->json([
                    "success"=>true,
                    "data"=>[
                        "total_count"=>count($row),
                        "images"=>Controller::image($row)
                    ]
                ]);
            }else{
                return Controller::error($requestdata->errors()->first());
            }
        }

        public function popular(Request $request){
            $limit=10;
            if($request->has("limit")){ $limit=$request->input("limit"); };
            if(1<=$limit&&$limit<=100){
                $data=[];
                $maindata=[];
                $row=DB::table("images")
                    ->where("deleted_at","=",NULL)
                    ->select("*")->get();

                for($i=0;$i<count($row);$i=$i+1){
                    $data[]=[$row[$i]->id,0];
                }

                $row=DB::table("image_views")
                    ->select("*")->get();

                for($i=0;$i<count($row);$i=$i+1){
                    for($j=0;$j<count($data);$j=$j+1){
                        if($data[$j][0]==$row[$i]->image_id){
                            $data[$j][1]=$data[$j][1]+1;
                        }
                    }
                }

                usort($data,function($a,$b){
                    if($a[1]<$b[1]){
                        return 1;
                    }
                });

                for($i=0;$i<min(count($data),$limit);$i=$i+1){
                    $row=DB::table("images")
                        ->where("id","=",$data[$i][0])
                        ->select("*")->get()[0];

                    $maindata[]=[
                        "id"=>$row->id,
                        "url"=>$row->url,
                        "title"=>$row->title,
                        "updated_at"=>$row->updated_at,
                        "created_at"=>$row->created_at
                    ];
                }

                return response()->json([
                    "success"=>true,
                    "data"=>$maindata
                ]);
            }else{
                return Controller::error(6);
            }
        }

        public function searchuserimage(Request $request,$userid){
            $row=DB::table("users")
                ->where("id","=",$userid)
                ->select("*")->get();
            if($row->isNotEmpty()){
                $row=DB::table("images")
                    ->where("user_id","=",$userid)
                    ->where("deleted_at","=",NULL)
                    ->select("*")->get();
                return response()->json([
                    "success"=>true,
                    "data"=>Controller::image($row)
                ]);
            }else{
                return Controller::error(9);
            }
        }

        public function upload(Request $request){
            $requestdata=Validator::make($request->all(),[
                "title"=>"required",
                "description"=>"required",
                "image"=>"required|mimes:png,jpg"
            ],[
                "required"=>5,
                "mimes"=>10
            ]);
            if(!$requestdata->fails()){
                $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($userid){
                    $requestdata=$requestdata->validate();
                    $mimetype="image/jpeg";
                    if($requestdata["image"]->getClientMimeType()=="png"){
                        $mimetype="image/png";
                    }
                    $path=$requestdata["image"]->store("image");
                    $imagedata=getimagesize(storage_path("app/".$path));
                    DB::table("images")->insert([
                        "url"=>$path,
                        "user_id"=>$userid,
                        "title"=>$requestdata["title"],
                        "description"=>$requestdata["description"],
                        "width"=>$imagedata[0],
                        "height"=>$imagedata[1],
                        "mimetype"=>$mimetype,
                        "created_at"=>Controller::time()
                    ]);
                    $row=DB::table("images")
                        ->latest()
                        ->select("*")->get();
                    return response()->json([
                        "success"=>true,
                        "data"=>Controller::imagedetail([$row[0]])
                    ],200);
                }else{
                    return Controller::error(3);
                }
            }else{
                return Controller::error($requestdata->errors()->first());
            }
        }

        public function updateimage(Request $request,$imageid){
            $row=DB::table("images")
                ->where("id","=",$imageid)
                ->select("*")->get();
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($row->isNotEmpty()&&$row[0]->deleted_at==NULL){
                if($userid){
                    $title=$row[0]->title;
                    $description=$row[0]->description;
                    if($request->has("title")){ $title=$request->input("title"); }
                    if($request->has("description")){ $description=$request->input("description"); }

                    if(is_string($title)&&is_string($description)){
                        DB::table("images")
                            ->where("id","=",$imageid)
                            ->update([
                                "title"=>$title,
                                "description"=>$description,
                                "updated_at"=>Controller::time(),
                            ]);
                        $row=DB::table("images")
                            ->where("id","=",$imageid)
                            ->select("*")->get();
                        return response()->json([
                            "success"=>true,
                            "data"=>Controller::imagedetail([$row[0]])
                        ],200);
                    }else{
                        return Controller::error(6);
                    }
                }else{
                    return Controller::error(4);
                }
            }else{
                return Controller::error(7);
            }
        }

        public function getimage(Request $request,$imageid){
            $row=DB::table("images")
                ->where("id","=",$imageid)
                ->select("*")->get();
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($row->isNotEmpty()&&$row[0]->deleted_at==NULL){
                if($userid=="0"){ $userid="-1"; } // 如果沒有登入id=-1

                DB::table("image_views")->insert([
                    "user_id"=>$userid,
                    "image_id"=>$imageid,
                    "viewed_at"=>Controller::time()
                ]);

                return response()->json([
                    "success"=>true,
                    "data"=>Controller::imagedetail([$row[0]])
                ],200);
            }else{
                return Controller::error(7);
            }
        }

        public function delimage(Request $request,$imageid){
            $row=DB::table("images")
                ->where("id","=",$imageid)
                ->select("*")->get();
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($row->isNotEmpty()&&$row[0]->deleted_at==NULL){
                if($userid==$row[0]->user_id){
                    DB::table("images")
                        ->where("id","=",$imageid)
                        ->update([
                            "deleted_at"=>Controller::time()
                        ]);

                    DB::table("image_views")
                        ->where("image_id","=",$imageid)
                        ->delete();

                    return response()->json([
                        "success"=>true,
                    ],200);
                }else{
                    return Controller::error(4);
                }
            }else{
                return Controller::error(7);
            }
        }

        public function getcomment($imageid){
            $row=DB::table("images")
                ->where("id","=",$imageid)
                ->select("*")->get();
            if($row->isNotEmpty()&&$row[0]->deleted_at==NULL){
                $row=DB::table("comments")
                    ->where("image_id","=",$imageid)
                    ->select("*")->get();

                $commentidrow=DB::table("comments")
                    ->select("*")->get();

                $commentid=[];

                for($i=0;$i<count($commentidrow);$i=$i+1){
                    $commentid[]=$commentidrow[$i]->id;
                }

                $_SESSION["idlist"]=$commentid;

                return response()->json([
                    "success"=>true,
                    "data"=>Controller::controllercomment($row)
                ],200);
            }else{
                return Controller::error(7);
            }
        }

        public function comment(Request $request,$imageid){
            $row=DB::table("images")
                ->where("id","=",$imageid)
                ->select("*")->get();
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($row->isNotEmpty()&&$row[0]->deleted_at==NULL){
                if($userid){
                    if($request->has("content")){
                        $content=$request->input("content");
                        if(is_string($content)){
                            DB::table("comments")->insert([
                                "image_id"=>$imageid,
                                "user_id"=>$userid,
                                "content"=>$content,
                                "created_at"=>Controller::time()
                            ]);

                            $row=DB::table("comments")
                                ->latest()
                                ->select("*")->get();

                            $commentidrow=DB::table("comments")
                                ->select("*")->get();

                            $commentid=[];

                            for($i=0;$i<count($commentidrow);$i=$i+1){
                                $commentid[]=$commentidrow[$i]->id;
                            }

                            $_SESSION["idlist"]=$commentid;

                            return response()->json([
                                "success"=>true,
                                "data"=>Controller::controllercomment([$row[0]])
                            ],200);
                        }else{
                            return Controller::error(6);
                        }
                    }else{
                        return Controller::error(5);
                    }
                }else{
                    return Controller::error(4);
                }
            }else{
                return Controller::error(7);
            }
        }

        public function replycomment(Request $request,$imageid,$commentid){
            $row=DB::table("images")
                ->where("id","=",$imageid)
                ->select("*")->get();
            $commentrow=DB::table("comments")
                ->where("id","=",$commentid)
                ->select("*")->get();
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($row->isNotEmpty()&&$row[0]->deleted_at==NULL){
                if($commentrow->isNotEmpty()&&$commentrow[0]->image_id==$imageid){
                    if($userid){
                        if($request->has("content")){
                            $content=$request->input("content");
                            if(is_string($content)){
                                DB::table("comments")->insert([
                                    "image_id"=>$imageid,
                                    "user_id"=>$userid,
                                    "comment_id"=>$commentid,
                                    "content"=>$content,
                                    "created_at"=>Controller::time()
                                ]);

                                $row=DB::table("comments")
                                    ->latest()
                                    ->select("*")->get();

                                $commentidrow=DB::table("comments")
                                    ->select("*")->get();

                                $commentid=[];

                                for($i=0;$i<count($commentidrow);$i=$i+1){
                                    $commentid[]=$commentidrow[$i]->id;
                                }

                                $_SESSION["idlist"]=$commentid;

                                return response()->json([
                                    "success"=>true,
                                    "data"=>Controller::controllercomment([$row[0]])
                                ],200);
                            }else{
                                return Controller::error(6);
                            }
                        }else{
                            return Controller::error(5);
                        }
                    }else{
                        return Controller::error(4);
                    }
                }else{
                    return Controller::error(8);
                }
            }else{
                return Controller::error(7);
            }
        }

        public function delcomment(Request $request,$imageid,$commentid){
            $row=DB::table("images")
                ->where("id","=",$imageid)
                ->select("*")->get();
            $commentrow=DB::table("comments")
                ->where("id","=",$commentid)
                ->select("*")->get();
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($row->isNotEmpty()&&$row[0]->deleted_at==NULL){
                if($commentrow->isNotEmpty()&&$commentrow[0]->image_id==$imageid){
                    if($userid==$commentrow[0]->user_id||$userid==1){
                        Controller::controllerdelcomment($commentid);
                        return response()->json([
                            "success"=>true
                        ],200);
                    }else{
                        return Controller::error(4);
                    }
                }else{
                    return Controller::error(8);
                }
            }else{
                return Controller::error(7);
            }
        }

        public function popularuser(Request $request){
            $orderby="upload_count";
            $limit=10;
            if($request->has("order_by")){
                $orderby=$request->input("order_by");
            }
            if($request->has("limit")){
                $limit=$request->input("limit");
            }
            if(1<=$limit&&$limit<=100){
                $data=[];
                $maindata=[];

                $row=DB::table("users")
                    ->select("*")->get();

                for($i=0;$i<count($row);$i=$i+1){
                    $data[]=[$row[$i]->id,0];
                }

                if($orderby=="upload_count"){
                    $row=DB::table("images")
                        ->select("*")->get();
                }elseif($orderby=="total_view_count"){
                    $row=DB::table("image_views")
                        ->select("*")->get();
                }elseif($orderby=="total_comment_count"){
                    $row=DB::table("comments")
                        ->select("*")->get();
                }else{
                    return Controller::error(6);
                }

                for($i=0;$i<count($row);$i=$i+1){
                    for($j=0;$j<count($data);$j=$j+1){
                        if($data[$j][0]==$row[$i]->user_id){
                            $data[$j][1]=$data[$j][1]+1;
                        }
                    }
                }

                usort($data,function($a,$b){
                    if($a[1]<$b[1]){
                        return 1;
                    }else{
                        return 0;
                    }
                });

                for($i=0;$i<min(count($data),$limit);$i=$i+1){
                    $row=DB::table("users")
                        ->where("id","=",$data[$i][0])
                        ->select("*")->get();
                    $maindata[]=[
                        "user"=>Controller::user($row,"normal"),
                        $orderby=>$data[$i][1]
                    ];
                }

                return response()->json([
                    "success"=>true,
                    "data"=>$maindata
                ]);
            }else{
                return Controller::error(6);
            }
        }
    }
?>