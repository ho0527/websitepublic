<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;

    class worker extends Controller{
        public function newwworker(Request $request){
            $requestdata=Validator::make($request->all(),[
                "name"=>"required|string",
            ],[
                "required"=>4,
                "string"=>5
            ]);

            if(!$requestdata->fails()){
                $row=DB::table("workers")
                    ->where("name",$request["name"])
                    ->select("*")->get();

                if(count($row)==0){
                    DB::table("workers")
                        ->insert([
                            "name"=>Str::lower($request["name"]),
                            "password"=>Hash::make(Str::random(10))
                        ]);
                    $row=DB::table("workers")
                        ->where("name",$request["name"])
                        ->where("deleted_at","=",NULL)
                        ->select("*")->get();
                    $row=$row[count($row)-1 ];

                    return response()->json([
                        "success"=>true,
                        "data"=>[
                            "id"=>$row->id,
                            "name"=>$row->name,
                            "types"=>[],
                            "is_idled"=>false,
                            "created_at"=>$row->created_at
                        ]
                    ]);
                }else{
                    return $this->error(17);
                }
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function deleteworker(Request $request,$workerid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata["id"]!=-1){
                    if($tokendata["type"]=="ADMIN"){
                        $row=DB::table("workers")
                            ->where("id","=",$workerid)
                            ->where("deleted_at","=",NULL)
                            ->select("*")->get();

                        if($row->isNotEmpty()){
                            DB::table("workers")
                                ->where("id","=",$workerid)
                                ->update([
                                    "deleted_at"=>$this->time()
                                ]);

                            return response()->json([
                                "success"=>true,
                                "data"=>""
                            ]);
                        }else{
                            return $this->error(12);
                        }
                    }else{
                        return $this->error(3);
                    }
                }else{
                    return $this->error(2);
                }
            }else{
                return $this->error(2);
            }
        }
    }
?>