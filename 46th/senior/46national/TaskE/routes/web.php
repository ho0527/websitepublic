<?php
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;

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

    $reset=["respondcode"=>"200","status"=>"scuess","data"=>"OK"];
    $newbook=["respondcode"=>"200","status"=>"scuess","data"=>"{id}"];
    $isbnerror=["respondcode"=>"409 Confilct","status"=>"fail","data"=>"ISBN duplicate"];
    $isbnincorrect=["respondcode"=>"400 Bad Request","status"=>"fail","data"=>"ISBN error"];
    $booktomanydata=["respondcode"=>"400 Bad Request","status"=>"fail","data"=>"input error"];
    $booksearchfromid=["respondcode"=>"200","id"=>"{id}","name"=>"{book name}","isbn"=>"{isbn}"];
    $booksearchfromiderror=["respondcode"=>"404 Not Found","status"=>"fail","data"=>"book not found"];
    $editbook=["respondcode"=>"200","status"=>"scuess","data"=>"OK"];
    $editbookname=["respondcode"=>"200","status"=>"scuess","data"=>"OK"];
    $editbookfromiderror=["respondcode"=>"404 Not Found","status"=>"fail","data"=>"book id not found"];
    $editisbnerror=["respondcode"=>"409 Confilct","status"=>"fail","data"=>"ISBN duplicate"];
    $isbnincorrect=["respondcode"=>"400 Bad Request","status"=>"fail","data"=>"ISBN error"];
    $editbooktomanydata=["respondcode"=>"400 Bad Request","status"=>"fail","data"=>"input error"];
    $delectbook=["respondcode"=>"200","status"=>"scuess","data"=>"OK"];
    $delectbookfromiderror=["respondcode"=>"404 Not Found","status"=>"fail","data"=>"book id not found"];
    $bookdata=[
        ["id"=>"{id}","name"=>"{book name}","isbn"=>"{isbn}"],
        ["id"=>"{id}","name"=>"{book name}","isbn"=>"{isbn}"],//inf.
    ];
    $notfoundurl=["respondcode"=>"403 Forbidden","status"=>"fail","data"=>"403 Forbidden"];

    Route::get('/reset',function(){
        return view('welcome');
    });

    Route::post('/books',function(){
        return view('welcome');
    });

    Route::get('/books/:id',function(){
        return view('welcome');
    });

    Route::put('/books/:id',function(){
        return view('welcome');
    });

    Route::get('/books',function(){
        return view('welcome');
    });

    Route::get('/books/AbC',function(){
        return view('welcome');
    });