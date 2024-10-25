<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;

    class project extends Controller{
        public function getproject(Request $request,$projectid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata["userid"]!=-1){
                    $row=DB::table("project")
                        ->where("id","=",$projectid)
                        ->select("*")->get();
                    if($row->isNotEmpty()&&$row[0]->deletetime==NULL){
                        $row=$row[0];
                        $usertoprojectrow=DB::table("usertoproject")
                            ->where("projectid","=",$projectid)
                            ->select("*")->get();
                        $clienttoprojectrow=DB::table("clienttoproject")
                            ->where("projectid","=",$projectid)
                            ->select("*")->get();
                        $userliststr=[];
                        $clientliststr=[];
                        for($i=0;$i<count($usertoprojectrow);$i=$i+1){
                            $userliststr[]=$usertoprojectrow[$i]->userid;
                        }
                        for($i=0;$i<count($clienttoprojectrow);$i=$i+1){
                            $clientliststr[]=$clienttoprojectrow[$i]->clientid;
                        }
                        $userliststr=implode(",",$userliststr);
                        $clientliststr=implode(",",$clientliststr);
                        return response()->json([
                            "success"=>true,
                            "data"=>[
                                "uuid"=>$row->uuid,
                                "userId"=>$row->userid,
                                "managerId"=>$row->manageruserid,
                                "userList"=>$userliststr,
                                "clientList"=>$clientliststr,
                                "number"=>$row->number,
                                "location"=>$row->location,
                                "startDate"=>explode(" ",$row->starttime)[0],
                                "startTime"=>explode(" ",$row->starttime)[1],
                                "endDate"=>explode(" ",$row->endtime)[0],
                                "endTime"=>explode(" ",$row->endtime)[1]
                            ]
                        ]);
                    }else{
                        return $this->error(8);
                    }
                }else{
                    return $this->error(5);
                }
            }else{
                return $this->error(4);
            }
        }

        public function getprojectlist(Request $request){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata["userid"]!=-1){
                    $row=DB::table("project")
                        ->where("userid","=",$tokendata["userid"])
                        ->where("deletetime","=",NULL)
                        ->orderBy("id","DESC")
                        ->select("*")->get();
                    $data=[];
                    for($i=0;$i<count($row);$i=$i+1){
                        if($row[$i]->deletetime==NULL){
                            $projectid=$row[$i]->id;
                            $usertoprojectrow=DB::table("usertoproject")
                                ->where("projectid","=",$projectid)
                                ->select("*")->get();
                            $clienttoprojectrow=DB::table("clienttoproject")
                                ->where("projectid","=",$projectid)
                                ->select("*")->get();
                            $userliststr=[];
                            $clientliststr=[];
                            for($j=0;$j<count($usertoprojectrow);$j=$j+1){
                                $userliststr[]=$usertoprojectrow[$j]->userid;
                            }
                            for($j=0;$j<count($clienttoprojectrow);$j=$j+1){
                                $clientliststr[]=$clienttoprojectrow[$j]->clientid;
                            }
                            $userliststr=implode(",",$userliststr);
                            $clientliststr=implode(",",$clientliststr);
                            $data[]=[
                                "id"=>$row[$i]->id,
                                "uuid"=>$row[$i]->uuid,
                                "userId"=>$row[$i]->userid,
                                "managerId"=>$row[$i]->manageruserid,
                                "userList"=>$userliststr,
                                "clientList"=>$clientliststr,
                                "number"=>$row[$i]->number,
                                "location"=>$row[$i]->location,
                                "startDate"=>explode(" ",$row[$i]->starttime)[0],
                                "startTime"=>explode(" ",$row[$i]->starttime)[1],
                                "endDate"=>explode(" ",$row[$i]->endtime)[0],
                                "endTime"=>explode(" ",$row[$i]->endtime)[1]
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

        public function newproject(Request $request){
            $requestdata=Validator::make($request->all(),[
                "manager"=>"required|string",
                "employee"=>"required|string",
                "client"=>"required|string",
                "number"=>"required|string",
                "note"=>"required|string",
                "location"=>"required|string",
                "startDate"=>"required|date_format:Y-m-d",
                "startTime"=>"required|date_format:H:i:s",
                "endDate"=>"required|date_format:Y-m-d",
                "endTime"=>"required|date_format:H:i:s"
            ],[
                "string"=>2,
                "required"=>3,
                "date_format"=>7
            ]);

            if(!$requestdata->fails()){
                if($request->header("Authorization")){
                    $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata["userid"]!=-1){
                        $employeelist=explode(",",$request["employee"]);
                        $clientlist=explode(",",$request["client"]);
                        DB::table("project")->insert([
                            "uuid"=>str::uuid(),
                            "userid"=>$tokendata["userid"],
                            "manageruserid"=>$request["manager"],
                            "number"=>$request["number"],
                            "note"=>$request["note"],
                            "location"=>$request["location"],
                            "starttime"=>$request["startDate"]." ".$request["startTime"],
                            "endtime"=>$request["endDate"]." ".$request["endTime"],
                            "createtime"=>$this->time()
                        ]);
                        $row=DB::table("project")
                            ->select("*")->get();
                        $projectid=$row[count($row)-1]->id;
                        for($i=0;$i<count($employeelist);$i=$i+1){
                            DB::table("usertoproject")->insert([
                                "projectid"=>$projectid,
                                "userid"=>$employeelist[$i],
                                "createtime"=>$this->time()
                            ]);
                        }
                        for($i=0;$i<count($clientlist);$i=$i+1){
                            DB::table("clienttoproject")->insert([
                                "projectid"=>$projectid,
                                "clientid"=>$clientlist[$i],
                                "createtime"=>$this->time()
                            ]);
                        }
                        return response()->json([
                            "success"=>true,
                            "data"=>[
                                "id"=>$projectid,
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

        public function editproject(Request $request,$projectid){
            $requestdata=Validator::make($request->all(),[
                "manager"=>"string",
                "employee"=>"string",
                "client"=>"string",
                "number"=>"string",
                "note"=>"string",
                "location"=>"string",
                "startDate"=>"date_format:Y-m-d",
                "startTime"=>"date_format:H:i:s",
                "endDate"=>"date_format:Y-m-d",
                "endTime"=>"date_format:H:i:s"
            ],[
                "string"=>2,
                "date_format"=>7
            ]);

            if(!$requestdata->fails()){
                if($request->header("Authorization")){
                    $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata["userid"]!=-1){
                        $row=DB::table("project")
                            ->where("id","=",$projectid)
                            ->select("*")->get();
                        if($row->isNotEmpty()&&$row[0]->deletetime==NULL){
                            if($request["employee"]!=NULL){
                                $employeelist=explode(",",$request["employee"]);
                                DB::table("usertoproject")
                                    ->where("projectid","=",$projectid)
                                    ->delete();
                                for($i=0;$i<count($employeelist);$i=$i+1){
                                    DB::table("usertoproject")->insert([
                                        "projectid"=>$projectid,
                                        "userid"=>$employeelist[$i],
                                        "createtime"=>$this->time()
                                    ]);
                                }
                            }
                            if($request["client"]!=NULL){
                                $clientlist=explode(",",$request["client"]);
                                DB::table("clienttoproject")
                                    ->where("projectid","=",$projectid)
                                    ->delete();
                                for($i=0;$i<count($clientlist);$i=$i+1){
                                    DB::table("clienttoproject")->insert([
                                        "projectid"=>$projectid,
                                        "clientid"=>$clientlist[$i],
                                        "createtime"=>$this->time()
                                    ]);
                                }
                            }
                            $row=$row[0];
                            DB::table("project")
                                ->where("id","=",$projectid)
                                ->update([
                                    "userid"=>$tokendata["userid"]??$row->userid,
                                    "manageruserid"=>$request["manager"]??$row->manageruserid,
                                    "number"=>$request["number"]??$row->number,
                                    "note"=>$request["note"]??$row->note,
                                    "location"=>$request["location"]??$row->location,
                                    "starttime"=>($request["startDate"]==NULL||$request["startTime"]==NULL)?($row->starttime):($request["startDate"]." ".$request["startTime"]),
                                    "endtime"=>($request["endDate"]==NULL||$request["endTime"]==NULL)?($row->endtime):($request["endDate"]." ".$request["endTime"]),
                                    "updatetime"=>$this->time()
                                ]);
                            return response()->json([
                                "success"=>true,
                                "data"=>""
                            ]);
                        }else{
                            return $this->error(8);
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

        public function deleteproject(Request $request,$projectid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata["userid"]!=-1){
                    $row=DB::table("project")
                        ->where("id","=",$projectid)
                        ->select("*")->get();
                    if($row->isNotEmpty()&&$row[0]->deletetime==NULL){
                        $row=$row[0];
                        DB::table("project")
                            ->where("id","=",$projectid)
                            ->update([
                                "deletetime"=>$this->time()
                            ]);
                        return response()->json([
                            "success"=>true,
                            "data"=>""
                        ]);
                    }else{
                        return $this->error(8);
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