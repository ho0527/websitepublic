<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    include("error.php");
    include("function.php");

    class post extends Controller{
        public function getpost(Request $request){
            $orderby=$request->input("order_by");
            $ordertype=$request->input("order_type");
            $content=$request->input("content");
            $tag=$request->input("tag");
            $location=$request->input("location_name");
            $page=$request->input("page");
            $pagesize=$request->input("page_size");
            if(!($request->has("order_by"))){ $orderby="created_at"; }
            if(!($request->has("order_type"))){ $ordertype="desc"; }
            if(!($request->has("content"))){ $content=""; }
            if(!($request->has("tag"))){ $tag=""; }
            if(!($request->has("location_name"))){ $location=""; }
            if(!($request->has("page"))){ $page=1; }
            if(!($request->has("pagesize"))){ $pagesize=10; }
            if(($orderby=="created_at"||$orderby=="like_count")&&($ordertype=="asc"||$ordertype=="desc")&&(1<=$pagesize&&$pagesize<=100)){
                $row=DB::table("posts")
                    ->where("type","=","public")
                    ->where("content","LIKE","%".$content."%")
                    ->where("tag","LIKE","%".$tag."%")
                    ->where("location","LIKE","%".$location."%")
                    ->orderBy($orderby,$ordertype)
                    ->skip(($page-1)*$pagesize)
                    ->take($pagesize)
                    ->select("*")->get();
                return response()->json([
                    "success"=>true,
                    "message"=>"",
                    "data"=>[
                        "total_count"=>$row->count(),
                        "posts"=>post($row)
                    ]
                ]);
            }else{
                return datatypeerror();
            }
        }

        public function getidpost(Request $request,$postid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            $row=DB::table("posts")
                ->where("id","=",$postid)
                ->select("*")->get();
            if($row->isNotEmpty()){
                $follow=DB::table("user_follows")
                    ->where("user_id","=",$row[0]->author_id)
                    ->where("follow_user_id","=",$userid)
                    ->select("*")->get();
                if(($row[0]->type=="public")||(($follow->isNotEmpty()||$userid==$row[0]->author_id)&&$row[0]->type=="only_follow")||($userid==$row[0]->author_id&&$row[0]->type=="only_self")){
                    $commentrow=DB::table("comments")
                        ->where("post_id","=",$postid)
                        ->select("*")->get();
                    return response()->json([
                        "success"=>true,
                        "message"=>"",
                        "data"=>[
                            "post"=>post($row),
                            "comments"=>comment($commentrow)
                        ]
                    ]);
                }else{
                    return nopermission();
                }
            }else{
                return posterror();
            }
        }

        public function post(Request $request){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                if($request->has("type")&&$request->has("content")&&$request->hasFile("images")){
                    $type=$request->input("type");
                    $content=$request->input("content");
                    $image=$request->file("images");
                    $tag="";
                    $location="";
                    if($request->has("tags")){ $tag=$request->input("tags"); }
                    if($request->has("location_name")){ $location=$request->input("location_name"); }
                    if(is_string($type)&&is_string($tag)&&is_string($content)&&is_string($location)&&($type=="public"||$type=="only_follow"||$type=="only_self")){
                        DB::table("posts")->insert([
                            "author_id"=>$userid,
                            "content"=>$content,
                            "type"=>$type,
                            "tag"=>$tag,
                            "location"=>$location,
                            "created_at"=>time(),
                        ]);
                        $row=DB::table("posts")
                            ->latest()
                            ->select("*")->get()[0];
                        $id=$row->id;
                        $row=DB::table("posts")
                            ->where("id","=",$id)
                            ->select("*")->get();
                        for($i=0;$i<count($image);$i=$i+1){
                            if(in_array($image[$i]->extension(),["png","jpg"])){
                                $path=$image[$i]->store("image");
                                $imagedata=getimagesize(storage_path("app/".$path));
                                DB::table("post_images")->insert([
                                    "post_id"=>$id,
                                    "width"=>$imagedata[0],
                                    "height"=>$imagedata[1],
                                    "filename"=>$path,
                                    "created_at"=>time()
                                ]);
                            }else{
                                $row=DB::table("posts")
                                    ->where(function($query)use($id){
                                        $query->where("id","=",$id);
                                    })->delete();
                                $row=DB::table("post_images")
                                    ->where(function($query)use($id){
                                        $query->where("id","=",$id);
                                    })->delete();
                                return imageerror();
                            }
                        }
                        return response()->json([
                            "success"=>true,
                            "message"=>"",
                            "data"=>post($row)
                        ]);
                    }else{
                        return datatypeerror();
                    }
                }else{
                    return missingfield();
                }
            }else{
                return tokenerror();
            }
        }

        public function editpost(Request $request,$postid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $row=DB::table("posts")
                    ->where("id","=",$postid)
                    ->select("*")->get();
                if($row->isNotEmpty()){
                    if($row[0]->author_id==$userid){
                        if($request->has("type")&&$request->has("content")){
                            $type=$request->input("type");
                            $tag="";
                            $content=$request->input("content");
                            if($request->has("tags")){ $tag=$request->input("tags"); }
                            if(is_string($type)&&is_string($tag)&&is_string($content)&&($type=="public"||$type=="only_follow"||$type=="only_self")){
                                DB::table("posts")
                                    ->where("id","=",$postid)
                                    ->update([
                                        "content"=>$content,
                                        "type"=>$type,
                                        "tag"=>$tag,
                                        "updated_at"=>time(),
                                    ]);
                                $row=DB::table("posts")
                                    ->where("id","=",$postid)
                                    ->select("*")->get();
                                return response()->json([
                                    "success"=>true,
                                    "message"=>"",
                                    "data"=>post($row)
                                ]);
                            }else{
                                return datatypeerror();
                            }
                        }else{
                            return missingfield();
                        }
                    }else{
                        return nopermission();
                    }
                }else{
                    return posterror();
                }
            }else{
                return tokenerror();
            }
        }

        public function delpost(Request $request,$postid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $row=DB::table("posts")
                    ->where("id","=",$postid)
                    ->select("*")->get();
                if($row->isNotEmpty()){
                    if($row[0]->author_id==$userid){
                        $row=DB::table("posts")
                            ->where("id","=",$postid)
                            ->delete();
                        return response()->json([
                            "success"=>true,
                            "message"=>"",
                            "data"=>""
                        ]);
                    }else{
                        return nopermission();
                    }
                }else{
                    return posterror();
                }
            }else{
                return tokenerror();
            }
        }

        public function favorite(Request $request,$postid){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $row=DB::table("posts")
                    ->where("id","=",$postid)
                    ->select("*")->get();
                if($row->isNotEmpty()){
                    $follow=DB::table("user_follows")
                        ->where("user_id","=",$row[0]->author_id)
                        ->where("follow_user_id","=",$userid)
                        ->select("*")->get();
                    if(($row[0]->type=="public")||(($follow->isNotEmpty()||$userid==$row[0]->author_id)&&$row[0]->type=="only_follow")||($userid==$row[0]->author_id&&$row[0]->type=="only_self")){
                        if($request->has("favorite")){
                            $type=$request->input("favorite"); // 是true==新增 false 取消 還是只有true
                            if(is_bool($type)){
                                $row=DB::table("user_likes")
                                    ->where("post_id","=",$postid)
                                    ->where("user_id","=",$userid)
                                    ->select("*")->get();
                                if($row->isEmpty()){
                                    $row=DB::table("user_likes")->insert([
                                        "user_id"=>$userid,
                                        "post_id"=>$postid,
                                    ]);
                                }else{
                                    $row=DB::table("user_likes")
                                        ->where("post_id","=",$postid)
                                        ->where("user_id","=",$userid)
                                        ->delete();
                                }
                                return response()->json([
                                    "success"=>true,
                                    "message"=>"",
                                    "data"=>""
                                ]);
                            }else{
                                return datatypeerror();
                            }
                        }else{
                            return missingfield();
                        }
                    }else{
                        return nopermission();
                    }
                }else{
                    return posterror();
                }
            }else{
                return tokenerror();
            }
        }

        public function getfavorite(Request $request){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $orderby=$request->input("order_by");
                $ordertype=$request->input("order_type");
                $page=$request->input("page");
                $pagesize=$request->input("page_size");
                if(!($request->has("order_by"))){ $orderby="created_at"; }
                if(!($request->has("order_type"))){ $ordertype="desc"; }
                if(!($request->has("page"))){ $page=1; }
                if(!($request->has("pagesize"))){ $pagesize=10; }
                if(($orderby=="created_at"||$orderby=="like_count")&&($ordertype=="asc"||$ordertype=="desc")&&(1<=$pagesize&&$pagesize<=100)){
                    $row=DB::table("user_likes")
                        ->where("user_id","=",$userid)
                        ->orderBy($orderby,$ordertype)
                        ->skip(($page-1)*$pagesize)
                        ->take($pagesize)
                        ->select("*")->get();
                    $data=[];
                    for($i=0;$i<count($row);$i=$i+1){
                        $imagerow=DB::table("posts")
                            ->where("id","=",$row[$i]->post_id)
                            ->select("*")->get();
                        $data[]=$imagerow[0];
                    }
                    return response()->json([
                        "success"=>true,
                        "message"=>"",
                        "data"=>[
                            "posts"=>post($data)
                        ]
                    ]);
                }else{
                    return datatypeerror();
                }
            }else{
                return tokenerror();
            }
        }

        public function comment(Request $request,$postid){
            $row=DB::table("posts")
                ->where("id","=",$postid)
                ->select("*")->get();
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                if($row->isNotEmpty()){
                    $follow=DB::table("user_follows")
                        ->where("user_id","=",$row[0]->author_id)
                        ->where("follow_user_id","=",$userid)
                        ->select("*")->get();
                    if(($row[0]->type=="public")||(($follow->isNotEmpty()||$userid==$row[0]->author_id)&&$row[0]->type=="only_follow")||($userid==$row[0]->author_id&&$row[0]->type=="only_self")){
                        if($request->has("content")){
                            $content=$request->input("content");
                            if(is_string($content)){
                                DB::table("comments")->insert([
                                    "user_id"=>$userid,
                                    "post_id"=>$postid,
                                    "content"=>$content,
                                    "created_at"=>time()
                                ]);

                                $commentrow=DB::table("comments")
                                    ->latest()
                                    ->select("*")->get();

                                return response()->json([
                                    "success"=>true,
                                    "data"=>comment([$commentrow[0]])
                                ],200);
                            }else{
                                return datatypeerror();
                            }
                        }else{
                            return missingfield();
                        }
                    }else{
                        return nopermission();
                    }
                }else{
                    return posterror();
                }
            }else{
                return tokenerror();
            }
        }

        public function editcomment(Request $request,$postid,$commentid){
            $row=DB::table("posts")
                ->where("id","=",$postid)
                ->select("*")->get();
            $commentrow=DB::table("comments")
                ->where("id","=",$commentid)
                ->select("*")->get();
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                if($row->isNotEmpty()){
                    if($commentrow->isNotEmpty()&&$commentrow[0]->post_id==$postid){
                        if($userid==$commentrow[0]->user_id){
                            if($request->has("content")){
                                $content=$request->input("content");
                                if(is_string($content)){
                                    DB::table("comments")
                                        ->where("id","=",$commentid)
                                        ->update([
                                            "content"=>$content
                                        ]);

                                    $commentrow=DB::table("comments")
                                        ->where("id","=",$commentid)
                                        ->select("*")->get();

                                    return response()->json([
                                        "success"=>true,
                                        "data"=>comment([$commentrow[0]])
                                    ],200);
                                }else{
                                    return datatypeerror();
                                }
                            }else{
                                return missingfield();
                            }
                        }else{
                            return nopermission();
                        }
                    }else{
                        return commenterror();
                    }
                }else{
                    return posterror();
                }
            }else{
                return tokenerror();
            }
        }

        public function delcomment(Request $request,$postid,$commentid){
            $row=DB::table("posts")
                ->where("id","=",$postid)
                ->select("*")->get();
            $commentrow=DB::table("comments")
                ->where("id","=",$commentid)
                ->select("*")->get();
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                if($row->isNotEmpty()){
                    if($commentrow->isNotEmpty()&&$commentrow[0]->post_id==$postid){
                        if($userid==$commentrow[0]->user_id){
                            $row=DB::table("comments")
                                ->where("id","=",$commentid)
                                ->select("*")->get();
                    
                            DB::table("comments")
                                ->where("id","=",$commentid)
                                ->delete();
    
                            return response()->json([
                                "success"=>true
                            ],200);
                        }else{
                            return nopermission();
                        }
                    }else{
                        return commenterror();
                    }
                }else{
                    return posterror();
                }
            }else{
                return tokenerror();
            }
        }
    }
?>