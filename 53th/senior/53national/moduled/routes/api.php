<?php
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\user;
    use App\Http\Controllers\image;

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
    */

    Route::POST("/auth/login",[user::class,"login"]);
    Route::POST("/auth/register",[user::class,"register"]);
    Route::POST("/auth/logout",[user::class,"logout"]);
    Route::GET("/image/search",[image::class,"search"]);
    Route::GET("/image/popular",[image::class,"popular"]);
    Route::GET("/user/{user_id}/image",[image::class,"searchuserimage"])->where("user_id","[0-9]+");
    Route::POST("/image/upload",[image::class,"upload"]);
    Route::PUT("/image/{image_id}",[image::class,"updateimage"])->where("image_id","[0-9]+");
    Route::GET("/image/{image_id}",[image::class,"getimage"])->where("image_id","[0-9]+");
    Route::DELETE("/image/{image_id}",[image::class,"delimage"])->where("image_id","[0-9]+");
    Route::GET("/image/{image_id}/comment",[image::class,"getcomment"])->where("image_id","[0-9]+");
    Route::POST("/image/{image_id}/comment",[image::class,"comment"])->where("image_id","[0-9]+");
    Route::POST("/image/{image_id}/comment/{comment_id}/reply",[image::class,"replycomment"])->where("image_id","[0-9]+")->where("comment_id","[0-9]+");
    Route::DELETE("/image/{image_id}/comment/{comment_id}",[image::class,"delcomment"])->where("image_id","[0-9]+")->where("comment_id","[0-9]+");
    Route::GET("/user/popular",[image::class,"popularuser"]);
?>