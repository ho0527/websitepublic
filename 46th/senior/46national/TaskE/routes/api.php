<?php
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\DB;

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

    $ok=response()->json(["status"=>"scueess","data"=>"OK"],200);
    // $newbook=["respondcode"=>"200","status"=>"scueess","data"=>$row->id];
    $isbnerror=response()->json(["status"=>"fail","data"=>"ISBN duplicate"],409);
    $isbnincorrect=response()->json(["status"=>"fail","data"=>"ISBN error"],400);
    $tomanydata=response()->json(["status"=>"fail","data"=>"input error"],400);
    $booksearchfromid=response()->json(["id"=>"{id}","name"=>"{book name}","isbn"=>"{isbn}"],200);
    $booksearchfromiderror=response()->json(["status"=>"fail","data"=>"book not found"],404);
    $editbook=response()->json(["status"=>"scueess","data"=>"OK"],200);
    $editbookfromiderror=response()->json(["status"=>"fail","data"=>"book id not found"],404);
    $delectbook=response()->json(["status"=>"scueess","data"=>"OK"],200);
    $delectbookfromiderror=response()->json(["status"=>"fail","data"=>"book id not found"],404);
    $bookdata=response()->json(["id"=>"{id}","name"=>"{book name}","isbn"=>"{isbn}"],200);
    $urlnotfound=response()->json(["status"=>"fail","data"=>"403 Forbidden"],403);

    Route::GET("/reset",function()use($ok){
        DB::table("book")->truncate();
        return $ok;
    });

    Route::POST("/books",function(Request $request)use($isbnerror,$isbnincorrect,$tomanydata){
        $name=$request->input("name");
        $isbn=$request->input("isbn");
        $isbnold=$isbn;
        $row=DB::table("book")
            ->where(function($query)use($isbn){
                $query->where("isbn","=",$isbn);
            })->select("*")->get();
        if($row->isEmpty()){
            $isbncheck=true;
            if(count(explode(" ",$isbn))==5||(count(explode("-",$isbn))==5)){
                if(count(explode(" ",$isbn))==4){
                    $isbn=explode(" ",$isbn);
                }else{
                    $isbn=explode("-",$isbn);
                }
                $isbn=$isbn[0].$isbn[1].$isbn[2].$isbn[3].$isbn[4];
                $isbncheckcode=$isbn[strlen($isbn)-1];
                $isbntotal=0;
                for($i=0;$i<11;$i=$i+1){
                    if(($i+1)%2==0){ $multiplier=1; }else{ $multiplier=3; }
                    $isbntotal=$isbntotal+((int)$isbn[$i]*$multiplier);
                }
                $isbntotal=$isbntotal%10;
                $n=10-$isbntotal;
                if($n==10){ $check=0; }else{ $check=$n; }
                if($check!=$isbncheckcode){ $isbncheck=false; }
            }else{
                $isbncheck=false;
            }
            if($isbncheck){
                if(count(array_diff(array_keys($request->all()),["name","isbn"]))<=0){
                    DB::table("book")->insert([
                        "name"=>$name,
                        "isbn"=>$isbnold,
                    ]);
                    $row=DB::table("book")
                        ->where(function($query)use($isbnold){
                            $query->where("isbn","=",$isbnold);
                        })->select("*")->get()[0];
                    return response()->json([
                        "status"=>"scueess",
                        "data"=>"$row->id"
                    ],200);
                }else{
                    return $tomanydata;
                }
            }else{
                return $isbnincorrect;
            }
        }else{
            return $isbnerror;
        }
    });

    Route::GET("/books/{id}",function(Request $request)use($booksearchfromid,$booksearchfromiderror,$urlnotfound){
        $id=$request->route("id");
        if(preg_match("[0-9]",$id)){
            $row=DB::table("book")
                ->where(function($query)use($id){
                    $query->where("id","=",$id);
                })->select("*")->get();
            if($row->isNotEmpty()){
                $row=$row[0];
                return response()->json([
                    "id"=>"$row->id",
                    "name"=>"$row->name",
                    "isbn"=>"$row->isbn",
                ],200);
            }else{
                return $booksearchfromiderror;
            }
        }else{
            return $urlnotfound;
        }
    });
    // Route::PUT("/books/{id}",[Controller::class,""]);

    Route::PUT("/books/{id}",function(Request $request)use($editbook,$booksearchfromiderror,$editbookfromiderror,$isbnerror,$isbnincorrect,$tomanydata){
        $id=$request->route("id");
        $name=$request->input("name");
        $isbn=$request->input("isbn");
        $isbnold=$isbn;
        $rowid=DB::table("book")
            ->where(function($query)use($id){
                $query->where("id","=",$id);
            })->select("*")->get();
        $row=DB::table("book")
            ->where(function($query)use($isbn){
                $query->where("isbn","=",$isbn);
            })->select("*")->get();
        if($rowid->isNotEmpty()){
            if($row->isEmpty()||$row[0]->id==$id){
                $isbncheck=true;
                if(count(explode(" ",$isbn))==5||(count(explode("-",$isbn))==5)){
                    if(count(explode(" ",$isbn))==5){
                        $isbn=explode(" ",$isbn);
                    }else{
                        $isbn=explode("-",$isbn);
                    }
                    $isbn=$isbn[0].$isbn[1].$isbn[2].$isbn[3].$isbn[4];
                    $isbncheckcode=$isbn[strlen($isbn)-1];
                    $isbntotal=0;
                    for($i=0;$i<11;$i=$i+1){
                        if(($i+1)%2==0){ $multiplier=1; }else{ $multiplier=3; }
                        $isbntotal=$isbntotal+((int)$isbn[$i]*$multiplier);
                    }
                    $isbntotal=$isbntotal%10;
                    $n=10-$isbntotal;
                    if($n==10){ $check=0; }else{ $check=$n; }
                    if($check!=$isbncheckcode){ $isbncheck=false; }
                }else{
                    $isbncheck=false;
                }
                if($isbncheck){
                    if(count(array_diff(array_keys($request->all()),["name","isbn"]))<=0){
                        DB::table("book")
                            ->where("id","=",$id)
                            ->update([
                                "name"=>$name,
                                "isbn"=>$isbnold,
                            ]);
                        return $editbook;
                    }else{
                        return $tomanydata;
                    }
                }else{
                    return $isbnincorrect;
                }
            }else{
                return $isbnerror;
            }
        }else{
            return $editbookfromiderror;
        }
    });

    Route::DELETE("/books/{id}",function(Request $request)use($ok,$booksearchfromiderror,$editbookfromiderror,$isbnerror,$isbnincorrect,$tomanydata){
        $id=$request->route("id");
        $row=DB::table("book")
            ->where(function($query)use($id){
                $query->where("id","=",$id);
            })->select("*")->get();
        if($row->isNotEmpty()){
            DB::table("book")
                ->where("id",$id)
                ->delete();
            return $ok;
        }else{
            return $editbookfromiderror;
        }
    });
?>