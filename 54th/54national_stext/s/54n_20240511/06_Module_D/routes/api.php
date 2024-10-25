<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

function nowtime(){
    date_default_timezone_set("Asia/Taipei");
    return time("Y-m-d H:i:S");
}

function logincheck($token){
    if($token){
        $token=explode("Bearer ",$token)[1];
        $row=DB::table("users")
            ->where("access_token","=",$token)
            ->select("*")->first();
        if($row){
            return [
                "userid"=>$row->id,
                "permission"=>$row->type
            ];
        }else{
            return -1;
        }
    }else{
        return -1;
    }
}

function error($key){
    $errorcode=[
        ["MSG_INVALID_LOGIN",403],
        ["MSG_USER_EXISTS",409],
        ["MSG_PASSWORD_NOT_SECURE",409],
        ["MSG_INVALID_ACCESS_TOKEN",401],
        ["MSG_PERMISSION_DENY",403],
        ["MSG_MISSING_FIELD",400],
        ["MSG_WRONG_DATA_TYPE",400],
        ["MSG_IMAGE_NOT_EXISTS",404],
        ["MSG_COMMENT_NOT_EXISTS",404],
        ["MSG_USER_NOT_EXISTS",404],
        ["MSG_INVALID_FILE_FORMAT",400]
    ];
    return response()->json([
        "success"=>false,
        "message"=>$errorcode[$key][0]
    ],$errorcode[$key][1]);
}

function success($data){
    return response()->json([
        "success"=>true,
        "data"=>$data
    ],200);
}

function updateurl($url){
    return implode("storage/",explode("public/",url($url)));
}

Route::POST("/auth/login",function(Request $request){
    $requestdata=Validator::make($request->all(),[
        "email"=>"request|string|email",
        "password"=>"request|string",
    ],[
        "request"=>5,
        "string"=>6,
        "email"=>6
    ]);

    if(!$requestdata->fails()){
        $requestdata=$requestdata->validated();
        $row=DB::table("users")
            ->where("email","=",$requestdata["email"])
            ->select("*")->first();
        if($row&&Hash::check($request["password"],$row->password)){
            DB::table("users")
                ->where("email","=",$requestdata["email"])
                ->update([
                    "access_token"=>hash("sha256",$row->email)
                ]);
            $row=DB::table("users")
                ->where("email","=",$requestdata["email"])
                ->select("*")->first();
            return success([
                "id"=>$row->id,
                "email"=>$row->email,
                "nickname"=>$row->nickname,
                "profile_image"=>updateurl($row->profile_image),
                "type"=>$row->type,
                "created_at"=>$row->created_at,
                "access_token"=>$row->access_token
            ]);
        }else{
            return error(1);
        }
    }else{
        return error($request->errors()->first());
    }
});

Route::POST("/auth/register",function(Request $request){
    $requestdata=Validator::make($request->all(),[
        "email"=>"request|string|email",
        "nickname"=>"request|string",
        "profile_image"=>"request|mimes:png,jpg",
        "password"=>"request|string|min:4",
    ],[
        "request"=>5,
        "string"=>6,
        "email"=>6,
        "mimes"=>10,
        "min"=>2
    ]);

    if(!$requestdata->fails()){
        $requestdata=$requestdata->validated();
        $row=DB::table("users")
            ->where("email","=",$requestdata["email"])
            ->select("*")->first();
        if(!$row){
            DB::table("users")->insert([
                "email"=>$data["email"],
                "nickname"=>$data["nickname"],
                "profile_image"=>$data["profile_image"]->store("images"),
                "type"=>"USER"
            ]);
            $row=DB::table("users")
                ->where("email","=",$requestdata["email"])
                ->select("*")->first();
            return success([
                "id"=>$row->id,
                "email"=>$row->email,
                "nickname"=>$row->nickname,
                "profile_image"=>updateurl($row->profile_image),
                "type"=>$row->type,
                "created_at"=>$row->created_at
            ]);
        }else{
            return error(2);
        }
    }else{
        return error($request->errors()->first());
    }
});

Route::POST("/auth/logout",function(Request $request){
    $header=$request->header("Authorization");
    $logincheck=logincheck($header);
    if($logincheck!=-1){
        DB::table("users")
            ->where("email","=",$logincheck["userid"])
            ->update([
                "access_token"=>NULL
            ]);
        return success("");
    }else{
        return error(3);
    }
});

Route::GET("/image/search",function(Request $request){
    $requestdata=Validator::make($request->all(),[
        "order_by"=>"string|in:created_at,updated_at,width,height",
        "order_type"=>"string|in:asc,desc",
        "keyword"=>"string",
        "page"=>"integer|min:1",
        "page_size"=>"integer|min:1|max:100",
    ],[
        "string"=>6,
        "in"=>6,
        "integer"=>6,
        "min"=>6,
        "max"=>6
    ]);

    if(!$requestdata->fails()){
        $requestdata=$requestdata->validated();

        $page=$requestdata["page"]??1;
        $pagesize=$requestdata["page_size"]??1;
        $imagedata=[];

        $row=DB::table("images")
            ->where("keyword","LIKE","%".$requestdata["keyword"]??""."%")
            ->orderBy($requestdata["order_by"]??"created_at",$requestdata["order_type"]??"asc")
            ->select("*")->get();

        for($i=($page-1)*$pagesize;$i<min(count($row),$page*$pagesize);$i=$i+1){
            $imagedata[]=[
                "id"=>$row[$i]->id,
                "url"=>$row[$i]->url,
                "title"=>$row[$i]->title,
                "updateed_at"=>$row[$i]->updateed_at,
                "created_at"=>$row[$i]->created_at
            ];
        }

        return success([
            "total_count"=>count($row),
            "images"=>$imagedata
        ]);
    }else{
        return error($request->errors()->first());
    }
});

Route::GET("/image/popular",function(Request $request){
    $requestdata=Validator::make($request->all(),[
        "order_by"=>"string|in:created_at,updated_at,width,height",
        "order_type"=>"string|in:asc,desc",
        "keyword"=>"string",
        "page"=>"integer|min:1",
        "page_size"=>"integer|min:1|max:100",
    ],[
        "string"=>6,
        "in"=>6,
        "integer"=>6,
        "min"=>6,
        "max"=>6
    ]);

    if(!$requestdata->fails()){
        $requestdata=$requestdata->validated();

        $page=$requestdata["page"]??1;
        $pagesize=$requestdata["page_size"]??1;
        $imagedata=[];

        $row=DB::table("images")
            ->where("keyword","LIKE","%".$requestdata["keyword"]??""."%")
            ->orderBy($requestdata["order_by"]??"created_at",$requestdata["order_type"]??"asc")
            ->select("*")->get();

        for($i=($page-1)*$pagesize;$i<min(count($row),$page*$pagesize);$i=$i+1){
            $imagedata[]=[
                "id"=>$row[$i]->id,
                "url"=>$row[$i]->url,
                "title"=>$row[$i]->title,
                "updateed_at"=>$row[$i]->updateed_at,
                "created_at"=>$row[$i]->created_at
            ];
        }

        return success([
            "total_count"=>count($row),
            "images"=>$imagedata
        ]);
    }else{
        return error($request->errors()->first());
    }
});

Route::GET("/user/:user_id/image",function(Request $request){

});

Route::POST("/image/upload",function(Request $request){

});

Route::PUT("/image/:image_id",function(Request $request){

});

Route::GET("/image/:image_id",function(Request $request){

});

Route::DELETE("/image/:image_id",function(Request $request){

});

Route::GET("/image/:image_id/comment",function(Request $request){

});

Route::POST("/image/:image_id/comment",function(Request $request){

});

Route::POST("/image/:image_id/comment/:comment_id/reply",function(Request $request){

});

Route::DELETE("/image/:image_id/comment/:comment_id",function(Request $request){

});

Route::GET("/user/popular",function(Request $request){

});