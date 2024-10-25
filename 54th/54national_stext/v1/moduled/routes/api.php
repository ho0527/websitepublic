<?php
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\user;
    use App\Http\Controllers\worker;
    use App\Http\Controllers\finance;
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

    Route::POST("/user/login",[user::class,"signin"]);
    Route::POST("/user/register",[user::class,"signup"]);
    Route::POST("/user/logout",[user::class,"signout"]);
    Route::GET("/user",[user::class,"getuserlist"]);
    Route::PUT("/user/{userid}",[user::class,"edituser"]);
    Route::GET("/user/leftquota",[user::class,"getleftquota"]);
    Route::GET("/user/quota",[user::class,"getquotalist"]);

    Route::GET("/task/type",[task::class,"gettasktype"]);
    Route::POST("/task/type",[task::class,"newtasktype"]);
    Route::PUT("/task/type/{tasktypeid}",[task::class,"edittasktype"]);
    Route::DELETE("/task/type/{tasktypeid}",[task::class,"deletetasktype"]);

    Route::POST("/task",[task::class,"newtask"]);
    Route::GET("/task",[task::class,"gettasklist"]);
    Route::GET("/task/{taskid}",[task::class,"gettask"]);
    Route::DELETE("/task/cancel/{taskid}",[task::class,"canceltask"]);

    Route::POST("/user/quota/{userid}",[user::class,"newquota"]);

    Route::POST("/worker",[worker::class,"newworker"]);
    Route::PUT("/worker/{workerid}",[worker::class,"editworker"]);
    Route::DELETE("/worker/{workerid}",[worker::class,"deleteworker"]);
?>