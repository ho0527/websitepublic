<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;

    class worker extends Controller{
        public function newworker(Request $request){
            $requestdata=Validator::make($request->all(),[
                "name"=>"required|string",
                "tasktypelist"=>"required|string",
            ],[
                "required"=>4,
                "string"=>5
            ]);

            if(!$requestdata->fails()){
                $requestdata=$requestdata->validated();
                if($request->header("Authorization")){
                    $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata!=-1){
                        if($tokendata["type"]=="ADMIN"){
                            $row=DB::table("workers")
                                ->where("name","=",$request["name"])
                                ->where("deleted_at","=",NULL)
                                ->select("*")->first();

                            if(!$row){
                                $tasktypelist=explode(",",$request["tasktypelist"]);
                                for($i=0;$i<count($tasktypelist);$i=$i+1){
                                    $row=DB::table("task_types")
                                        ->where("id","=",$tasktypelist[$i])
                                        ->select("*")->first();
                                    if(!$row){
                                        return $this->error(12);
                                    }
                                }
                                DB::table("workers")->insert([
                                    "name"=>$request["name"],
                                    "asscess_token"=>$this->random()
                                ]);
                                $row=DB::table("workers")
                                    ->where("name","=",$request["name"])
                                    ->select("*")->first();
                                for($i=0;$i<count($tasktypelist);$i=$i+1){
                                    DB::table("worker_task_types")->insert([
                                        "worker_id"=>$row->id,
                                        "task_type_id"=>$tasktypelist[$i]
                                    ]);
                                }

                                $workertaskdata=[];
                                $workertyperow=DB::table("worker_task_types")
                                    ->where("worker_id","=",$row->id)
                                    ->select("*")->get();

                                for($i=0;$i<count($workertyperow);$i=$i+1){
                                    $workertasktyperow=DB::table("task_types")
                                        ->where("id","=",$workertyperow[$i]->task_type_id)
                                        ->select("*")->first();

                                    $input=[];

                                    $inputrow=DB::table("task_type_inputs")
                                        ->where("task_type_id","=",$workertasktyperow->id)
                                        ->select("*")->get();

                                    for($j=0;$j<count($inputrow);$j=$j+1){
                                        $input[]=[
                                            "name"=>$inputrow[$j]->name,
                                            "type"=>$inputrow[$j]->type
                                        ];
                                    }

                                    $workertaskdata[]=[
                                        "id"=>$workertasktyperow->id,
                                        "name"=>$workertasktyperow->name,
                                        "inputs"=>$input,
                                        "created_at"=>$this->timestarp($workertasktyperow->created_at)
                                    ];
                                }

                                return response()->json([
                                    "success"=>true,
                                    "data"=>[
                                        "id"=>$row->id,
                                        "name"=>$row->name,
                                        "types"=>$workertaskdata,
                                        "is_idled"=>false,
                                        "created_at"=>$row->created_at
                                    ]
                                ]);
                            }else{
                                return $this->error(17);
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
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function editworker(Request $request,$workerid){
            $requestdata=Validator::make($request->all(),[
                "name"=>"string",
                "tasktypelist"=>"string",
            ],[
                "string"=>5
            ]);

            if(!$requestdata->fails()){
                $requestdata=$requestdata->validated();
                if($request->header("Authorization")){
                    $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata!=-1){
                        if($tokendata["type"]=="ADMIN"){
                            $row=DB::table("workers")
                                ->where("id","=",$workerid)
                                ->where("deleted_at","=",NULL)
                                ->select("*")->first();

                            if($row){
                                $row=DB::table("workers")
                                    ->where("id","!=",$workerid)
                                    ->where("name","=",$request["name"])
                                    ->where("deleted_at","=",NULL)
                                    ->select("*")->first();

                                if(!$row){
                                    if(isset($request["tasktypelist"])){
                                        $tasktypelist=explode(",",$request["tasktypelist"]);
                                        for($i=0;$i<count($tasktypelist);$i=$i+1){
                                            $row=DB::table("task_types")
                                                ->where("id","=",$tasktypelist[$i])
                                                ->select("*")->first();
                                            if(!$row){
                                                return $this->error(12);
                                            }
                                        }
                                    }

                                    DB::table("workers")
                                        ->where("id","=",$workerid)
                                        ->update([
                                            "name"=>$request["name"]??$row->name,
                                        ]);
                                    $row=DB::table("workers")
                                        ->where("id","=",$workerid)
                                        ->select("*")->first();

                                    if(isset($request["tasktypelist"])){
                                        for($i=0;$i<count($tasktypelist);$i=$i+1){
                                            DB::table("worker_task_types")->insert([
                                                "worker_id"=>$workerid,
                                                "task_type_id"=>$tasktypelist[$i]
                                            ]);
                                        }
                                    }

                                    $workertaskdata=[];
                                    $workertyperow=DB::table("worker_task_types")
                                        ->where("worker_id","=",$workerid)
                                        ->select("*")->get();

                                    DB::table("worker_task_types")
                                        ->where("worker_id","=",$workerid)
                                        ->delete();

                                    for($i=0;$i<count($workertyperow);$i=$i+1){
                                        $workertasktyperow=DB::table("task_types")
                                            ->where("id","=",$workertyperow[$i]->task_type_id)
                                            ->select("*")->first();

                                        $input=[];

                                        $inputrow=DB::table("task_type_inputs")
                                            ->where("task_type_id","=",$workertyperow[$i]->task_type_id)
                                            ->select("*")->get();

                                        for($j=0;$j<count($inputrow);$j=$j+1){
                                            $input[]=[
                                                "name"=>$inputrow[$j]->name,
                                                "type"=>$inputrow[$j]->type
                                            ];
                                        }

                                        $workertaskdata[]=[
                                            "id"=>$workertasktyperow->id,
                                            "name"=>$workertasktyperow->name,
                                            "inputs"=>$input,
                                            "created_at"=>$this->timestarp($workertasktyperow->created_at)
                                        ];
                                    }

                                    return response()->json([
                                        "success"=>true,
                                        "data"=>[
                                            "id"=>$row->id,
                                            "name"=>$row->name,
                                            "types"=>$workertaskdata,
                                            "is_idled"=>false,
                                            "created_at"=>$row->created_at
                                        ]
                                    ]);
                                }else{
                                    return $this->error(17);
                                }
                            }else{
                                return $this->error(13);
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
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function deleteworker(Request $request,$workerid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata!=-1){
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
                            return $this->error(13);
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

        public function workergettask(Request $request){
            if($request->header("Authorization")){
                $tokendata=explode("Bearer ",$request->header("Authorization"))[1];
                $workerrow=DB::table("workers")
                    ->where("asscess_token","=",$tokendata)
                    ->where("deleted_at","=",NULL)
                    ->select("*")->first();
                if($workerrow){
                    $workercandotask=[];
                    $workertaskdata=[];
                    $workerid=$workerrow->id;

                    $workertaskrow=DB::table("worker_task_types")
                        ->where("worker_id","=",$workerid)
                        ->select("*")->get();

                    for($i=0;$i<count($workertaskrow);$i=$i+1){
                        $workercandotask[]=$workertaskrow[$i]->task_type_id;
                        $tasktyperow=DB::table("task_types")
                            ->where("id","=",$workertaskrow[$i]->task_type_id)
                            ->select("*")->first();
                        $tasktypeinputrow=DB::table("task_type_inputs")
                            ->where("task_type_id","=",$workertaskrow[$i]->task_type_id)
                            ->select("*")->get();
                        $tasktypeinputdata=[];

                        for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
                            $tasktypeinputdata[]=[
                                "name"=>$tasktypeinputrow[$j]->name,
                                "type"=>$tasktypeinputrow[$j]->type
                            ];
                        }

                        $workertaskdata[]=[
                            "id"=>$tasktyperow->id,
                            "name"=>$tasktyperow->name,
                            "inputs"=>$tasktypeinputdata,
                            "created_at"=>$this->timestarp($tasktyperow->created_at)
                        ];
                    }

                    $row=DB::table("tasks")
                        ->whereIn("task_type_id",$workercandotask)
                        ->where("status","=","pending")
                        ->select("*")->first();

                    if($row){
                        DB::table("tasks")
                            ->where("id","=",$row->id)
                            ->update([
                                "worker_id"=>$workerid,
                                "status"=>"processing",
                                "updated_at"=>$this->time()
                            ]);

                        $userrow=DB::table("users")
                            ->where("id","=",$row->user_id)
                            ->select("*")->first();

                        $typerow=DB::table("task_types")
                            ->where("id","=",$row->task_type_id)
                            ->select("*")->first();

                        $typeinputrow=DB::table("task_type_inputs")
                            ->where("task_type_id","=",$row->task_type_id)
                            ->select("*")->get();

                        $input=[];

                        for($i=0;$i<count($typeinputrow);$i=$i+1){
                            $input[]=[
                                "name"=>$typeinputrow[$i]->name,
                                "type"=>$typeinputrow[$i]->type
                            ];
                        }

                        return response()->json([
                            "success"=>true,
                            "data"=>[
                                "id"=>$row->id,
                                "type"=>[
                                    "id"=>$typerow->id,
                                    "name"=>$typerow->name,
                                    "inputs"=>$input,
                                    "created_at"=>$typerow->created_at
                                ],
                                "user"=>[
                                    "id"=>$userrow->id,
                                    "email"=>$userrow->email,
                                    "nickname"=>$userrow->nickname,
                                    "profile_image"=>url($userrow->profile_image),
                                    "type"=>$userrow->type,
                                    "created_at"=>$userrow->created_at,
                                ],
                                "worker"=>[
                                    "id"=>$workerrow->id,
                                    "name"=>$workerrow->name,
                                    "inputs"=>$workertaskdata,
                                    "created_at"=>$workerrow->created_at
                                ],
                                "status"=>$row->status,
                                "result"=>$row->result,
                                "created_at"=>$row->created_at,
                                "updated_at"=>$row->updated_at
                            ]
                        ]);
                    }else{
                        return $this->error(11);
                    }
                }else{
                    return $this->error(2);
                }
            }else{
                return $this->error(2);
            }
        }

        public function workerresponsetask(Request $request,$taskid){
            $requestdata=Validator::make($request->all(),[
                "status"=>"required|string|in:finished,failed",
                "result"=>"required|mimes:jpg,png"
            ],[
                "required"=>4,
                "string"=>5,
                "in"=>5,
                "mimes"=>6
            ]);
            if(!$requestdata->fails()){
                $requestdata=$requestdata->validated();
                if($request->header("Authorization")){
                    $tokendata=explode("Bearer ",$request->header("Authorization"))[1];
                    $workerrow=DB::table("workers")
                        ->where("asscess_token","=",$tokendata)
                        ->where("deleted_at","=",NULL)
                        ->select("*")->first();
                    if($workerrow){
                        $row=DB::table("tasks")
                            ->where("id","=",$taskid)
                            ->select("*")->first();
                        if($row){
                            if($workerrow->id==$row->worker_id){
                                if($row->status=="processing"){
                                    $path="/storage/".$requestdata["result"]->store("images");

                                    $workercandotask=[];
                                    $workertaskdata=[];
                                    $workerid=$workerrow->id;

                                    $workertaskrow=DB::table("worker_task_types")
                                        ->where("worker_id","=",$workerid)
                                        ->select("*")->get();

                                    for($i=0;$i<count($workertaskrow);$i=$i+1){
                                        $workercandotask[]=$workertaskrow[$i]->task_type_id;
                                        $tasktyperow=DB::table("task_types")
                                            ->where("id","=",$workertaskrow[$i]->task_type_id)
                                            ->select("*")->first();
                                        $tasktypeinputrow=DB::table("task_type_inputs")
                                            ->where("task_type_id","=",$workertaskrow[$i]->task_type_id)
                                            ->select("*")->get();
                                        $tasktypeinputdata=[];

                                        for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
                                            $tasktypeinputdata[]=[
                                                "name"=>$tasktypeinputrow[$j]->name,
                                                "type"=>$tasktypeinputrow[$j]->type
                                            ];
                                        }

                                        $workertaskdata[]=[
                                            "id"=>$tasktyperow->id,
                                            "name"=>$tasktyperow->name,
                                            "inputs"=>$tasktypeinputdata,
                                            "created_at"=>$this->timestarp($tasktyperow->created_at)
                                        ];
                                    }

                                    DB::table("tasks")
                                        ->where("id","=",$row->id)
                                        ->update([
                                            "status"=>$requestdata["status"],
                                            "result"=>$path,
                                            "updated_at"=>$this->time()
                                        ]);

                                    $userrow=DB::table("users")
                                        ->where("id","=",$row->user_id)
                                        ->select("*")->first();

                                    $typerow=DB::table("task_types")
                                        ->where("id","=",$row->task_type_id)
                                        ->select("*")->first();

                                    $typeinputrow=DB::table("task_type_inputs")
                                        ->where("task_type_id","=",$row->task_type_id)
                                        ->select("*")->get();

                                    $input=[];

                                    for($i=0;$i<count($typeinputrow);$i=$i+1){
                                        $input[]=[
                                            "name"=>$typeinputrow[$i]->name,
                                            "type"=>$typeinputrow[$i]->type
                                        ];
                                    }

                                    return response()->json([
                                        "success"=>true,
                                        "data"=>[
                                            "id"=>$row->id,
                                            "type"=>[
                                                "id"=>$typerow->id,
                                                "name"=>$typerow->name,
                                                "inputs"=>$input,
                                                "created_at"=>$typerow->created_at
                                            ],
                                            "user"=>[
                                                "id"=>$userrow->id,
                                                "email"=>$userrow->email,
                                                "nickname"=>$userrow->nickname,
                                                "profile_image"=>url($userrow->profile_image),
                                                "type"=>$userrow->type,
                                                "created_at"=>$userrow->created_at,
                                            ],
                                            "worker"=>[
                                                "id"=>$workerrow->id,
                                                "name"=>$workerrow->name,
                                                "inputs"=>$workertaskdata,
                                                "created_at"=>$workerrow->created_at
                                            ],
                                            "status"=>$row->status,
                                            "result"=>$row->result,
                                            "created_at"=>$row->created_at,
                                            "updated_at"=>$row->updated_at
                                        ]
                                    ]);
                                }else{
                                    return $this->error(19);
                                }
                            }else{
                                return $this->error(3);
                            }
                        }else{
                            return $this->error(7);
                        }
                    }else{
                        return $this->error(2);
                    }
                }else{
                    return $this->error(2);
                }
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }
    }
?>