<?php
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\user;
    use App\Http\Controllers\video;

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

    Route::POST("v1/login",[user::class,"login"]);
    Route::POST("v1/logout",[user::class,"logout"]);
    Route::POST("v1/video",[video::class,"uploadvideo"]);
    Route::GET("v1/video",[video::class,"getvideo"]);
    Route::GET("v1/video/hot",[video::class,"gethotvideo"]);
    Route::GET("v1/video/public",[video::class,"getpublicvideo"]);
    Route::GET("v1/video/{video_id}",[video::class,"getidvideo"])->where("video_id","[0-9]+");
    Route::DELETE("v1/video/{video_id}",[video::class,"delvideo"])->where("video_id","[0-9]+");
    Route::PUT("v1/video/{video_id}/like",[video::class,"like"])->where("video_id","[0-9]+");
    Route::POST("v1/video/{video_id}/comment",[video::class,"comment"])->where("video_id","[0-9]+");
    Route::GET("v1/video/{video_id}/comment",[video::class,"getcomment"])->where("video_id","[0-9]+");
    Route::POST("v1/comment/{comment_id}/reply",[video::class,"replycomment"])->where("comment_id","[0-9]+");
    Route::DELETE("v1/comment/{comment_id}",[video::class,"delcomment"])->where("comment_id","[0-9]+");
    Route::GET("v1/playlist",[video::class,"getplaylist"]);
    Route::POST("v1/playlist",[video::class,"playlist"]);
    Route::GET("v1/playlist/{playlist_id}",[video::class,"getidplaylist"])->where("playlist_id","[0-9]+");
    Route::POST("v1/playlist/{playlist_id}/video",[video::class,"addvideotoplaylist"])->where("playlist_id","[0-9]+");
    Route::PUT("v1/playlist/{playlist_id}/order",[video::class,"sortplaylist"])->where("playlist_id","[0-9]+");
    Route::DELETE("v1/playlist/{playlist_id}/video/{video_id}",[video::class,"delvideoformplaylist"])->where("playlist_id","[0-9]+")->where("video_id","[0-9]+");
    Route::DELETE("v1/playlist/{playlist_id}",[video::class,"delplaylist"])->where("playlist_id","[0-9]+");
    Route::GET("v1/program",[video::class,"getprogram"]);
    Route::GET("v1/program/{program_id}",[video::class,"getidprogram"])->where("program_id","[0-9]+");
    Route::GET("v1/user",[user::class,"getuser"]);
    Route::PUT("v1/user/{user_id}/ban",[user::class,"banuser"])->where("user_id","[0-9]+");
    Route::POST("v1/program",[video::class,"program"]);
    Route::PUT("v1/program/{program_id}",[video::class,"editprogram"])->where("program_id","[0-9]+");
    Route::POST("v1/program/{program_id}/video",[video::class,"addvideotoprogram"])->where("program_id","[0-9]+");
    Route::DELETE("v1/program/{program_id}/video/{video_id}",[video::class,"delvideoformprogram"])->where("program_id","[0-9]+")->where("video_id","[0-9]+");
    Route::DELETE("v1/program/{program_id}",[video::class,"delprogram"])->where("program_id","[0-9]+");
    Route::GET("v1/blacklist",[user::class,"getblocklist"]);
?>