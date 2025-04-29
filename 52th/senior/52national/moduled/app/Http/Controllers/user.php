<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    include("error.php");
    include("function.php");

    class user extends Controller{
        public function login(Request $request){
            if($request->has("email")&&$request->has("password")){
                $email=$request->input("email");
                $password=$request->input("password");
                $row=DB::table("users")
                    ->where("email","=",$email)
                    ->select("*")->get();
                if($row->isNotEmpty()&&Hash::check($password,$row[0]->password)){
                    if(is_string($email)&&is_string($password)){
                        DB::table("users")
                            ->where("id","=",$row[0]->id)
                            ->update([
                                "access_token"=>hash("sha256",$email),
                            ]);
                        $row=DB::table("users")
                            ->where("email","=",$email)
                            ->select("*")->get();
                        return response()->json([
                            "success"=>true,
                            "message"=>"",
                            "data"=>user($row[0],"login")
                        ]);
                    }else{
                        return datatypeerror();
                    }
                }else{
                    return loginerror();
                }
            }else{
                return missingfield();
            }
        }

        public function logout(Request $request){
            $userid=logincheck();
            if($userid){
                DB::table("users")
                    ->where("id","=",$userid)
                    ->update([
                        "access_token"=>NULL,
                    ]);
                return response()->json([
                    "success"=>true,
                    "message"=>"",
                    "data"=>""
                ]);
            }else{
                return tokenerror();
            }
        }

        public function register(Request $request){
            if($request->has("email")&&$request->has("nickname")&&$request->has("password")&&$request->hasFile("profile_image")){
                $email=$request->input("email");
                $nickname=$request->input("nickname");
                $password=$request->input("password");
                $image=$request->file("profile_image");
                $row=DB::table("users")
                    ->where("email","=",$email)
                    ->select("*")->get();
                if($row->isEmpty()){
                    if(filter_var($email,FILTER_VALIDATE_EMAIL)&&is_string($email)&&is_string($nickname)&&is_string($password)){
                        if(8<=strlen($password)&&strlen($password)<=24){
                            if(in_array($image->extension(),["png","jpg"])){
                                $path=$image->store("image");
                                DB::table("users")->insert([
                                    "email"=>$email,
                                    "password"=>Hash::make($password),
                                    "nickname"=>$nickname,
                                    "profile_image"=>$path,
                                    "type"=>"USER",
                                    "created_at"=>time()
                                ]);
                                $row=DB::table("users")
                                    ->where("email","=",$email)
                                    ->select("*")->get();
                                return response()->json([
                                    "success"=>true,
                                    "message"=>"",
                                    "data"=>user($row[0],"normal")
                                ]);
                            }else{
                                return imageerror();
                            }
                        }else{
                            return passworderror();
                        }
                    }else{
                        return datatypeerror();
                    }
                }else{
                    return userexist();
                }
            }else{
                return missingfield();
            }
        }

        public function getuserpost(Request $request,$userid){
            $orderby="created_at";
            $ordertype="desc";
            $content="";
            $tag="";
            $location="";
            $page=1;
            $pagesize=10;
            if($request->has("order_by")){ $orderby=$request->input("order_by"); }
            if($request->has("order_type")){ $ordertype=$request->input("order_type"); }
            if($request->has("content")){ $content=$request->input("content"); }
            if($request->has("tag")){ $tag=$request->input("tag"); }
            if($request->has("location_name")){ $location=$request->input("location_name"); }
            if($request->has("page")){ $page=$request->input("page"); }
            if($request->has("pagesize")){ $pagesize=$request->input("page_size"); }
            $row=DB::table("users")
                ->where("id","=",$userid)
                ->select("*")->get();
            if($row->isNotEmpty()){
                if(($orderby=="created_at"||$orderby=="like_count")&&($ordertype=="asc"||$ordertype=="desc")&&(1<=$pagesize&&$pagesize<=100)){
                    $row=DB::table("posts")
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
            }else{
                return usererror();
            }
        }

        public function getprofile(Request $request,$userid){
            $row=DB::table("users")
                ->where("id","=",$userid)
                ->select("*")->get();
            if($row->isNotEmpty()){
                return response()->json([
                    "success"=>true,
                    "message"=>"",
                    "data"=>user($row[0],"normal")
                ]);
            }else{
                return usererror();
            }
        }

        public function editprofile(Request $request,$userid){
            $loginuserid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                $row=DB::table("users")
                    ->where("id","=",$userid)
                    ->select()->get();
                if($row->isNotEmpty()){
                    if($userid==$loginuserid){
                        if($request->has("nickname")){
                            $nickname=$request->input("nickname");
                            if(preg_match("/^[a-z0-9_]{4,16}$/",$nickname)){
                                DB::table("users")
                                    ->where("id","=",$userid)
                                    ->update([
                                        "nickname"=>$nickname
                                    ]);
                            }else{
                                return datatypeerror();
                            }
                        }
                        if($request->hasFile("profile_image")){
                            $image=$request->file("profile_image");
                            if(in_array($image->extension(),["png","jpg"])){
                                $path=$image->store("image");
                                DB::table("users")
                                    ->where("id","=",$userid)
                                    ->update([
                                        "profile_image"=>$path
                                    ]);
                            }else{
                                return imageerror();
                            }
                        }
                        DB::table("users")
                            ->where("id","=",$userid)
                            ->update([
                                "updated_at"=>time()
                            ]);
                        $row=DB::table("users")
                            ->where("id","=",$userid)
                            ->select()->get();
                        return response()->json([
                            "success"=>true,
                            "message"=>"",
                            "data"=>user($row[0],"normal")
                        ]);
                    }else{
                        return nopermission();
                    }
                }else{
                    return usererror();
                }
            }else{
                return tokenerror();
            }
        }

        public function getfollow(Request $request,$userid){
            $loginuserid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if(logincheck()){
                $orderby="created_at";
                $ordertype="desc";
                $page=1;
                $pagesize=10;
                if($request->has("order_by")){ $orderby=$request->input("order_by"); }
                if($request->has("order_type")){ $ordertype=$request->input("order_type"); }
                if($request->has("page")){ $page=$request->input("page"); }
                if($request->has("pagesize")){ $pagesize=$request->input("page_size"); }
                if(($orderby=="created_at"||$orderby=="like_count")&&($ordertype=="asc"||$ordertype=="desc")&&(1<=$pagesize&&$pagesize<=100)){
                    $row=DB::table("user_follows")
                        ->where("follow_user_id","=",$userid)
                        ->orderBy($orderby,$ordertype)
                        ->skip(($page-1)*$pagesize)
                        ->take($pagesize)
                        ->select("*")->get();
                    $data=[];
                    for($i=0;$i<count($row);$i=$i+1){
                        $userrow=DB::table("users")
                            ->where("id","=",$row[$i]->user_id)
                            ->select("*")->get()[0];
                        $data[]=[
                            "id"=>$userrow->id,
                            "email"=>$userrow->email,
                            "nickname"=>$userrow->nickname,
                            "profile_image"=>url($userrow->profile_image),
                            "type"=>$userrow->type,
                        ];
                    }
                    return response()->json([
                        "success"=>true,
                        "message"=>"",
                        "data"=>[
                            "posts"=>$data
                        ]
                    ]);
                }else{
                    return datatypeerror();
                }
            }else{
                return tokenerror();
            }
        }

        public function follow(Request $request,$userid){
            $loginuserid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($loginuserid){
                $row=DB::table("users")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($row->isNotEmpty()){
                    $row=DB::table("user_follows")->insert([
                        "user_id"=>$userid,
                        "follow_user_id"=>$loginuserid
                    ]);
                    return response()->json([
                        "success"=>true,
                        "message"=>"",
                        "data"=>""
                    ]);
                }else{
                    return usererror();
                }
            }else{
                return tokenerror();
            }
        }

        public function delfollow(Request $request,$userid){
            $loginuserid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($loginuserid){
                $row=DB::table("users")
                    ->where("id","=",$userid)
                    ->select("*")->get();
                if($row->isNotEmpty()){
                    $row=DB::table("user_follows")
                        ->where("user_id","=",$userid)
                        ->where("follow_user_id","=",$loginuserid)
                        ->delete();
                    return response()->json([
                        "success"=>true,
                        "message"=>"",
                        "data"=>""
                    ]);
                }else{
                    return usererror();
                }
            }else{
                return tokenerror();
            }
        }
    }
?>