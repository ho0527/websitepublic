<?php
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\DB;
    use App\Http\Controllers\user;
    use App\Http\Controllers\post;

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

    Route::POST("/user/login",[user::class,"login"]);
    Route::POST("/user/logout",[user::class,"logout"]);
    Route::POST("/user/register",[user::class,"register"]);
    Route::GET("/post/public",[post::class,"getpost"]);
    Route::GET("/post/{post_id}",[post::class,"getidpost"])->where("post_id","[0-9]+");
    Route::POST("/post",[post::class,"post"]);
    Route::POST("/post/{post_id}",[post::class,"editpost"])->where("post_id","[0-9]+");
    Route::DELETE("/post/{post_id}",[post::class,"delpost"])->where("post_id","[0-9]+");
    Route::POST("/post/{post_id}/favorite",[post::class,"favorite"])->where("post_id","[0-9]+");
    Route::GET("/post/favorite",[post::class,"getfavorite"]);
    Route::POST("/post/{post_id}/comment",[post::class,"comment"])->where("post_id","[0-9]+");
    Route::POST("/post/{post_id}/comment/{comment_id}",[post::class,"editcomment"])->where("post_id","[0-9]+")->where("comment_id","[0-9]+");
    Route::DELETE("/post/{post_id}/comment/{comment_id}",[post::class,"delcomment"])->where("post_id","[0-9]+")->where("comment_id","[0-9]+");
    Route::GET("/user/{user_id}/post",[user::class,"getuserpost"])->where("user_id","[0-9]+");
    Route::GET("/user/{user_id}/profile",[user::class,"getprofile"])->where("user_id","[0-9]+");
    Route::POST("/user/{user_id}/profile",[user::class,"editprofile"])->where("user_id","[0-9]+");
    Route::GET("/user/{user_id}/follow",[user::class,"getfollow"])->where("user_id","[0-9]+");
    Route::POST("/user/{user_id}/follow",[user::class,"follow"])->where("user_id","[0-9]+");
    Route::DELETE("/user/{user_id}/follow",[user::class,"delfollow"])->where("user_id","[0-9]+");
?>