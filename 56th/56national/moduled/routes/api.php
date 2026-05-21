<?php
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\user;
    use App\Http\Controllers\album;
    use App\Http\Controllers\song;
    use App\Http\Controllers\task;
    // use App\Http\Controllers\image;

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

    Route::POST("/login",[user::class,"signin"]);
    Route::POST("/register",[user::class,"signup"]);
    Route::GET("/albums",[album::class,"getalbumlist"]);
    Route::GET("/albums/{albumid}",[album::class,"getalbum"]);
    Route::GET("/albums/{albumid}/cover",[album::class,"getalbumcover"]);
    Route::GET("/albums/{albumid}/songs",[album::class,"getalbumsonglist"]);
    Route::GET("/songs/{songid}/cover",[song::class,"getsongcover"]);
    Route::GET("/songs",[song::class,"getsonglist"]);

    Route::POST("/logout",[user::class,"signout"]);
    Route::GET("/songs/{songid}",[song::class,"getsong"]);
    Route::GET("/statistics",[song::class,"getstatistics"]);

    Route::GET("/users",[user::class,"getuserlist"]);
    Route::PUT("/users/{userid}",[user::class,"edituserrole"]);
    Route::PUT("/users/{userid}/ban",[user::class,"banuser"]);
    Route::PUT("/users/{userid}/unban",[user::class,"unbanuser"]);
    Route::POST("/albums",[album::class,"newalbum"]);
    Route::PUT("/albums/{albumid}",[album::class,"editalbum"]);
    Route::DELETE("/albums/{albumid}",[album::class,"deletealbum"]);
    Route::POST("/albums/{albumid}/songs",[song::class,"newsong"]);
    Route::PUT("/albums/{albumid}/songs/order",[song::class,"editsongorder"]);
    Route::POST("/albums/{albumid}/songs/{songid}",[song::class,"editsong"]);
    Route::DELETE("/albums/{albumid}/songs/{songid}",[song::class,"deletesong"]);

    // Route::GET("/task/type",[task::class,"gettasktype"]);
    // Route::POST("/task/type",[task::class,"newtasktype"]);
    // Route::DELETE("/task/type/{tasktypeid}",[task::class,"deletetasktype"]);

    // Route::POST("/task",[task::class,"newtask"]);
    // Route::GET("/task",[task::class,"gettasklist"]);
    // Route::GET("/task/{taskid}",[task::class,"gettask"]);
    // Route::DELETE("/task/cancel/{taskid}",[task::class,"canceltask"]);

    // Route::POST("/user/quota/{userid}",[user::class,"newquota"]);

    // Route::POST("/worker",[worker::class,"newworker"]);
    // Route::PUT("/worker/{workerid}",[worker::class,"editworker"]);
    // Route::DELETE("/worker/{workerid}",[worker::class,"deleteworker"]);
    // Route::GET("/worker/task",[worker::class,"workergettask"]);
    // Route::POST("/worker/task/{taskid}",[worker::class,"workerresponsetask"]);
?>
