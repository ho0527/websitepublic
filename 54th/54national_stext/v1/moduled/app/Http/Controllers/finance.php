<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;

    class finance extends Controller{
        public function getfinance(Request $request,$financeid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata["userid"]!=-1){
                    if($tokendata["permission"]!="Employee"){
                        $row=DB::table("finance")
                            ->where("id","=",$financeid)
                            ->select("*")->get();
                        if($row->isNotEmpty()&&$row[0]->deletetime==NULL){
                            $row=$row[0];
                            return response()->json([
                                "success"=>true,
                                "data"=>[
                                    "uuid"=>$row->uuid,
                                    "userId"=>$row->userid,
                                    "hour"=>$row->hour,
                                    "hourlyRate"=>$row->hourlyrate,
                                    "total"=>$row->total
                                ]
                            ]);
                        }else{
                            return $this->error(10);
                        }
                    }else{
                        return $this->error(11);
                    }
                }else{
                    return $this->error(5);
                }
            }else{
                return $this->error(4);
            }
        }

        public function getfinancelist(Request $request){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata["userid"]!=-1){
                    if($tokendata["permission"]!="Employee"){
                        $row=DB::table("finance")
                            // ->where("userid","=",$tokendata["userid"])
                            ->where("deletetime","=",NULL)
                            ->orderBy("id","DESC")
                            ->select("*")->get();
                        $data=[];
                        for($i=0;$i<count($row);$i=$i+1){
                            if($row[$i]->deletetime==NULL){
                                $data[]=[
                                    "id"=>$row[$i]->id,
                                    "uuid"=>$row[$i]->uuid,
                                    "userId"=>$row[0]->userid,
                                    "hour"=>$row[0]->hour,
                                    "hourlyRate"=>$row[0]->hourlyrate,
                                    "total"=>$row[0]->total
                                ];
                            }
                        }
                        return response()->json([
                            "success"=>true,
                            "data"=>$data
                        ]);
                    }else{
                        return $this->error(11);
                    }
                }else{
                    return $this->error(5);
                }
            }else{
                return $this->error(4);
            }
        }

        public function newfinance(Request $request){
            $requestdata=Validator::make($request->all(),[
                "hour"=>"required|numeric",
                "hourlyRate"=>"required|numeric"
            ],[
                "numeric"=>2,
                "required"=>3
            ]);

            if(!$requestdata->fails()){
                if($request->header("Authorization")){
                    $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata["userid"]!=-1){
                        if($tokendata["permission"]!="Employee"){
                            DB::table("finance")->insert([
                                "uuid"=>str::uuid(),
                                "userid"=>$tokendata["userid"],
                                "hour"=>$request["hour"],
                                "hourlyrate"=>$request["hourlyRate"],
                                "total"=>$request["hour"]*$request["hourlyrate"],
                                "createtime"=>$this->time()
                            ]);
                            $row=DB::table("finance")
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
                            return $this->error(11);
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

        public function editfinance(Request $request,$financeid){
            $requestdata=Validator::make($request->all(),[
                "hour"=>"numeric",
                "hourlyRate"=>"numeric"
            ],[
                "numeric"=>2
            ]);

            if(!$requestdata->fails()){
                if($request->header("Authorization")){
                    $tokendata=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata["userid"]!=-1){
                        if($tokendata["permission"]!="Employee"){
                            $row=DB::table("finance")
                                ->where("id","=",$financeid)
                                ->select("*")->get();
                            if($row->isNotEmpty()&&$row[0]->deletetime==NULL){
                                $row=$row[0];
                                $hour=($request["hour"]==NULL)?($row->hour):($request["hour"]);
                                $hourlyrate=($request["hourlyrate"]==NULL)?($row->hourlyrate):($request["hourlyrate"]);
                                DB::table("finance")
                                    ->where("id","=",$financeid)
                                    ->update([
                                        "hour"=>$hour,
                                        "hourlyrate"=>$hourlyrate,
                                        "total"=>$hour*$hourlyrate,
                                        "updatetime"=>Controller::time()
                                    ]);
                                return response()->json([
                                    "success"=>true,
                                    "data"=>""
                                ]);
                            }else{
                                return Controller::error(10);
                            }
                        }else{
                            return Controller::error(11);
                        }
                    }else{
                        return Controller::error(5);
                    }
                }else{
                    return Controller::error(4);
                }
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function deletefinance(Request $request,$financeid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata["userid"]!=-1){
                    if($tokendata["permission"]!="Employee"){
                        $row=DB::table("finance")
                            ->where("id","=",$financeid)
                            ->select("*")->get();
                        if($row->isNotEmpty()&&$row[0]->deletetime==NULL){
                            $row=$row[0];
                            DB::table("finance")
                                ->where("id","=",$financeid)
                                ->update([
                                    "deletetime"=>$this->time()
                                ]);
                            return response()->json([
                                "success"=>true,
                                "data"=>""
                            ]);
                        }else{
                            return $this->error(10);
                        }
                    }else{
                        return $this->error(11);
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