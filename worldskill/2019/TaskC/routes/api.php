<?php
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\user;

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

    Route::GET("v1/events",[user::class,"event"]);
    Route::GET("v1/organizers/{organizer-slug}/event/{event-slug}",[user::class,"getevent"]);
    Route::POST("v1/login",[user::class,"login"]);
    Route::POST("v1/logout",[user::class,"logout"]);
    Route::POST("v1/organizers/{organizer-slug}/event/{event-slug}/registration",[user::class,"registration"]);
    Route::GET("v1/registrations",[user::class,"getregistration"]);
?>