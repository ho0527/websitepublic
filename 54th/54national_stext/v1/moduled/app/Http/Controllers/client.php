<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;

    class client extends Controller{
        public function getclient(Request $request,$clientid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata["userid"]!=-1){
                    $row=DB::table("client")
                        ->where("id","=",$clientid)
                        ->select("*")->get();
                    if($row->isNotEmpty()&&$row[0]->deletetime==NULL){
                        $row=$row[0];
                        return response()->json([
                            "success"=>true,
                            "data"=>[
                                "uuid"=>$row->uuid,
                                "userId"=>$row->userid,
                                "number"=>$row->number,
                                "name"=>$row->name,
                                "phone"=>$row->phone,
                                "position"=>$row->position
                            ]
                        ]);
                    }else{
                        return $this->error(9);
                    }
                }else{
                    return $this->error(5);
                }
            }else{
                return $this->error(4);
            }
        }

        public function getclientlist(Request $request){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata["userid"]!=-1){
                    $row=DB::table("client")
                        ->where("deletetime","=",NULL)
                        ->orderBy("id","DESC")
                        ->select("*")->get();
                    $data=[];
                    for($i=0;$i<count($row);$i=$i+1){
                        if($row[$i]->deletetime==NULL){
                            $data[]=[
                                "id"=>$row[$i]->id,
                                "uuid"=>$row[$i]->uuid,
                                "userId"=>$row[$i]->userid,
                                "number"=>$row[$i]->number,
                                "name"=>$row[$i]->name,
                                "phone"=>$row[$i]->phone,
                                "position"=>$row[$i]->position
                            ];
                        }
                    }
                    return response()->json([
                        "success"=>true,
                        "data"=>$data
                    ]);
                }else{
                    return $this->error(5);
                }
            }else{
                return $this->error(4);
            }
        }

        public function newclient(Request $request){
            $requestdata=Validator::make($request->all(),[
                "number"=>"required|string",
                "name"=>"required|string",
                "phone"=>"required|string",
                "position"=>"required|string"
            ],[
                "string"=>2,
                "required"=>3
            ]);

            if(!$requestdata->fails()){
                if($request->header("Authorization")){
                    $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata["userid"]!=-1){
                        DB::table("client")->insert([
                            "uuid"=>str::uuid(),
                            "userid"=>$tokendata["userid"],
                            "number"=>$request["number"],
                            "name"=>$request["name"],
                            "phone"=>$request["phone"],
                            "position"=>$request["position"],
                            "createtime"=>$this->time()
                        ]);
                        $row=DB::table("client")
                            ->select("*")->get();
                        return response()->json([
                            "success"=>true,
                            "data"=>[
                                "id"=>$row[count($row)-1]->id,
                                "uuid"=>$row[count($row)-1]->uuid,
                                "userId"=>(int)$tokendata["userid"]
                            ]
                        ]);
                    }else{
                        return $this->error(5);
                    }
                }else{
                    return $this->error(4);
                }
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function editclient(Request $request,$clientid){
            $requestdata=Validator::make($request->all(),[
                "number"=>"string",
                "name"=>"string",
                "phone"=>"string",
                "position"=>"string"
            ],[
                "string"=>2
            ]);

            if(!$requestdata->fails()){
                if($request->header("Authorization")){
                    $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata["userid"]!=-1){
                        $row=DB::table("client")
                            ->where("id","=",$clientid)
                            ->select("*")->get();
                        if($row->isNotEmpty()&&$row[0]->deletetime==NULL){
                            $row=$row[0];
                            DB::table("client")
                                ->where("id","=",$clientid)
                                ->update([
                                    "number"=>($request["number"]==NULL)?($row->number):($request["number"]),
                                    "name"=>($request["name"]==NULL)?($row->name):($request["name"]),
                                    "phone"=>($request["phone"]==NULL)?($row->phone):($request["phone"]),
                                    "position"=>($request["position"]==NULL)?($row->position):($request["position"]),
                                    "updatetime"=>$this->time()
                                ]);
                            return response()->json([
                                "success"=>true,
                                "data"=>""
                            ]);
                        }else{
                            return $this->error(9);
                        }
                    }else{
                        return $this->error(5);
                    }
                }else{
                    return $this->error(4);
                }
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function deleteclient(Request $request,$clientid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata["userid"]!=-1){
                    $row=DB::table("client")
                        ->where("id","=",$clientid)
                        ->select("*")->get();
                    if($row->isNotEmpty()&&$row[0]->deletetime==NULL){
                        $row=$row[0];
                        DB::table("client")
                            ->where("id","=",$clientid)
                            ->update([
                                "deletetime"=>$this->time()
                            ]);
                        return response()->json([
                            "success"=>true,
                            "data"=>""
                        ]);
                    }else{
                        return $this->error(9);
                    }
                }else{
                    return $this->error(5);
                }
            }else{
                return $this->error(4);
            }
        }
    }
?>