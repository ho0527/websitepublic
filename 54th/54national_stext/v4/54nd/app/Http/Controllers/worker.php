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
                $worker=DB::table("workers")
                    ->where("asscess_token","=",$tokendata)
                    ->where("deleted_at","=",NULL)
                    ->select("*")->first();
                if($worker){
                    $workercandotask=[];
                    $workerid=$worker->id;

                    $workertaskrow=DB::table("worker_task_types")
                        ->where("worker_id","=",$workerid)
                        ->select("*")->get();

                    for($i=0;$i<count($workertaskrow);$i=$i+1){
                        $workercandotask[]=$workertaskrow[$i]->task_type_id;
                    }

                    sort($workercandotask);

                    print_r($workercandotask);
                }else{
                    return $this->error(2);
                }
            }else{
                return $this->error(2);
            }
        }

        public function workerresponsetask(Request $request,$taskid){
            // if($request->header("Authorization")){
            //     $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            //     if($tokendata!=-1){
            //         $workerid=$tokendata["id"];
        }
    }
?>