<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Const_;

    class task extends Controller{
        public function gettasktype(Request $request){
            $requestdata=Validator::make($request->all(),[
                "order_by"=>"string|in:created_at",
                "order_type"=>"string|in:asc,desc",
                "page"=>"interger",
                "page_size"=>"interger"
            ],[
                "string"=>5,
                "interger"=>5,
                "in"=>5
            ]);

            if(!$requestdata->fails()){
                if($request->header("Authorization")){
                    $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata!=-1){
                        if($tokendata["type"]=="ADMIN"){
                            $data=[];
                            $row=DB::table("task_types")
                                ->orderBy($request["order_by"]??"created_at",$request["order_type"]??"asc")
                                ->skip(($request["page"]??1-1)*($request["page_size"]??10))
                                ->take($request["page_size"]??10)
                                ->select("*")->get();

                            for($i=0;$i<count($row);$i=$i+1){
                                $input=[];

                                $inputrow=DB::table("task_type_inputs")
                                    ->where("task_type_id","=",$row[$i]->id)
                                    ->select("*")->get();

                                for($j=0;$j<count($inputrow);$j=$j+1){
                                    $input[]=[
                                         "name"=>$inputrow[$j]->name,
                                         "type"=>$inputrow[$j]->type
                                    ];
                                }

                                $data[]=[
                                    "id"=>$row[$i]->id,
                                    "name"=>$row[$i]->name,
                                    "inputs"=>$input,
                                    "created_at"=>$this->timestarp($row[$i]->created_at)
                                ];
                            }

                            $row=DB::table("task_types")
                                ->select("*")->get();

                            return response()->json([
                                "success"=>true,
                                "data"=>[
                                    "total_count"=>count($row),
                                    "posts"=>$data
                                ]
                            ]);
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

        public function gettask(Request $request,$taskid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata!=-1){
                    if($tokendata["type"]=="USER"){
                        $row=DB::table("tasks")
                            ->where("id","=",$taskid)
                            ->where("user_id","=",$tokendata["id"])
                            ->select("*")->first();

                        if($row){
                            $typedata=[];
                            $userdata=[];
                            $workerdata=NULL;

                            $typerow=DB::table("task_types")
                                ->where("id","=",$row->task_type_id)
                                ->select("*")->first();

                            $input=[];

                            $inputrow=DB::table("task_type_inputs")
                                ->where("task_type_id","=",$row->id)
                                ->select("*")->get();

                            for($j=0;$j<count($inputrow);$j=$j+1){
                                $input[]=[
                                    "name"=>$inputrow[$j]->name,
                                    "type"=>$inputrow[$j]->type
                                ];
                            }

                            $typedata[]=[
                                "id"=>$typerow->id,
                                "name"=>$typerow->name,
                                "inputs"=>$input,
                                "created_at"=>$this->timestarp($typerow->created_at)
                            ];

                            $userrow=DB::table("users")
                                ->where("id","=",$row->user_id)
                                ->select("*")->first();

                            $userdata=[
                                "id"=>$userrow->id,
                                "email"=>$userrow->email,
                                "nickname"=>$userrow->nickname,
                                "profile_image"=>url($userrow->profile_image),
                                "type"=>$userrow->type,
                                "created_at"=>$this->timestarp($userrow->created_at)
                            ];

                            if($row->worker_id!=NULL){
                                $workertaskdata=[];
                                $workerrow=DB::table("workers")
                                    ->where("id","=",$row->worker_id)
                                    ->select("*")->first();

                                $workertyperow=DB::table("worker_task_types")
                                    ->where("worker_id","=",$row->worker_id)
                                    ->select("*")->get();

                                for($i=0;$i<count($workertyperow);$i=$i+1){
                                    $workertasktyperow=DB::table("task_types")
                                        ->where("id","=",$workertyperow[$i]->task_type_id)
                                        ->select("*")->first();

                                    $input=[];

                                    $inputrow=DB::table("task_type_inputs")
                                        ->where("task_type_id","=",$row->id)
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
                                $workerdata=[
                                    "id"=>$workerrow->id,
                                    "name"=>$workerrow->name,
                                    "types"=>$workertaskdata,
                                    "is_idled"=>$row->status=="proccessing"?true:false,
                                    "created_at"=>$this->timestarp($workerrow->created_at)
                                ];
                            }

                            return response()->json([
                                "success"=>true,
                                "data"=>[
                                    "id"=>$row->id,
                                    "type"=>$typedata,
                                    "user"=>$userdata,
                                    "worker"=>$workerdata,
                                    "status"=>$row->status,
                                    "result"=>$row->result==NULL?NULL:url($row->result),
                                    "created_at"=>$this->timestarp($row->created_at),
                                    "updated_at"=>$this->timestarp($row->updated_at),
                                ]
                            ]);
                        }else{
                            return $this->error(7);
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

        public function newtask(Request $request){
            $requestdata=Validator::make($request->all(),[
                "name"=>"required|string",
                "inputs"=>"required|array"
            ],[
                "required"=>4,
                "string"=>5,
                "array"=>5,
                "in"=>5,
            ]);

            if(!$requestdata->fails()){
                if($request->header("Authorization")){
                    $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata["id"]!=-1){
                        if($tokendata["type"]=="ADMIN"){
                            $row=DB::table("task_types")
                                ->where("name","=",$request["name"])
                                ->select("*")->get();

                            if(count($row)==0){
                                DB::table("task_types")->insert([
                                    "name"=>$request["name"],
                                    "created_at"=>$this->time()
                                ]);
                                $row=DB::table("task_types")
                                    ->where("name","=",$request["name"])
                                    ->select("*")->get();

                                $row=$row[count($row)-1];
                                $tasktypeid=$row->id;

                                for($i=0;$i<count($request["inputs"]);$i=$i+1){
                                     DB::table("task_type_inputs")->insert([
                                        "task_type_id"=>$tasktypeid,
                                        "name"=>$request["inputs"][$i]["name"],
                                        "type"=>$request["inputs"][$i]["type"]
                                    ]);
                                }

                                return response()->json([
                                    "success"=>true,
                                    "data"=>[
                                        "id"=>$tasktypeid,
                                        "name"=>$request["name"],
                                        "inputs"=>$request["inputs"],
                                        "created_at"=>$row->created_at
                                    ]
                                ]);
                            }else{
                                return $this->error(8);
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

        public function newtasktype(Request $request){
            $requestdata=Validator::make($request->all(),[
                "name"=>"required|string",
                "inputs"=>"required|array",
                "inputs.*.name"=>"required|string",
                "inputs.*.type"=>"required|string|in:string,number,boolean"
            ],[
                "required"=>4,
                "string"=>5,
                "array"=>5,
                "in"=>5
            ]);

            if(!$requestdata->fails()){
                if(preg_match("/^([a-z]|_|[0-9])+$/",$request["name"])){
                    if($request->header("Authorization")){
                        $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                        if($tokendata!=-1){
                            if($tokendata["type"]=="ADMIN"){
                                $tempnamelist=[];

                                for($i=0;$i<count($request["inputs"]);$i=$i+1){
                                    if(!in_array($request["inputs"][$i]["name"],$tempnamelist)){
                                        $tempnamelist[]=$request["inputs"][$i]["name"];
                                    }else{
                                        return $this->error(8);
                                    }
                                }

                                $row=DB::table("task_types")
                                    ->where("name","=",$request["name"])
                                    ->where("deleted_at","=",NULL)
                                    ->select("*")->get();

                                if($row->isEmpty()){
                                    DB::table("task_types")->insert([
                                        "name"=>$request["name"],
                                        "created_at"=>$this->time()
                                    ]);
                                    $row=DB::table("task_types")
                                        ->where("name","=",$request["name"])
                                        ->select("*")->get();

                                    $row=$row[count($row)-1];
                                    $tasktypeid=$row->id;

                                    for($i=0;$i<count($request["inputs"]);$i=$i+1){
                                        DB::table("task_type_inputs")->insert([
                                            "task_type_id"=>$tasktypeid,
                                            "name"=>$request["inputs"][$i]["name"],
                                            "type"=>$request["inputs"][$i]["type"]
                                        ]);
                                    }

                                    return response()->json([
                                        "success"=>true,
                                        "data"=>[
                                            "id"=>$tasktypeid,
                                            "name"=>$request["name"],
                                            "inputs"=>$request["inputs"],
                                            "created_at"=>$row->created_at
                                        ]
                                    ]);
                                }else{
                                    return $this->error(16);
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
                    return $this->error(5);
                }
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function edittasktype(Request $request,$tasktypeid){
            $requestdata=Validator::make($request->all(),[
                "name"=>"required|string",
                "inputs"=>"required|array",
                "inputs.*.name"=>"required|string",
                "inputs.*.type"=>"required|string|in:string,number,boolean"
            ],[
                "required"=>4,
                "string"=>5,
                "array"=>5,
                "in"=>5,
            ]);

            if(!$requestdata->fails()){
                if(preg_match("/^([a-z]|_|[0-9])+$/",$request["name"])){
                    if($request->header("Authorization")){
                        $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                        if($tokendata!=-1){
                            if($tokendata["type"]=="ADMIN"){
                                $tempnamelist=[];

                                for($i=0;$i<count($request["inputs"]);$i=$i+1){
                                    if(!in_array($request["inputs"][$i]["name"],$tempnamelist)){
                                        $tempnamelist[]=$request["inputs"][$i]["name"];
                                    }else{
                                        return $this->error(8);
                                    }
                                }

                                $row=DB::table("task_types")
                                    ->where("id","=",$tasktypeid)
                                    ->where("deleted_at","=",NULL)
                                    ->select("*")->get();
                                if($row->isNotEmpty()){
                                    $row=DB::table("task_types")
                                        ->where("name","=",$request["name"])
                                        ->select("*")->first();

                                    if(!$row||$row->id==$tasktypeid){
                                        DB::table("task_types")
                                            ->where("id","=",$tasktypeid)
                                            ->update([
                                                "name"=>$request["name"]
                                            ]);
                                        $row=DB::table("task_types")
                                            ->where("name","=",$request["name"])
                                            ->select("*")->first();

                                        DB::table("task_type_inputs")
                                            ->where("task_type_id","=",$tasktypeid)
                                            ->delete();

                                        for($i=0;$i<count($request["inputs"]);$i=$i+1){
                                             DB::table("task_type_inputs")->insert([
                                                "task_type_id"=>$tasktypeid,
                                                "name"=>$request["inputs"][$i]["name"],
                                                "type"=>$request["inputs"][$i]["type"]
                                            ]);
                                        }

                                        return response()->json([
                                            "success"=>true,
                                            "data"=>[
                                                "id"=>$tasktypeid,
                                                "name"=>$request["name"],
                                                "inputs"=>$request["inputs"],
                                                "created_at"=>$row->created_at
                                            ]
                                        ]);
                                    }else{
                                        return $this->error(16);
                                    }
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
                }else{
                    return $this->error(5);
                }
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function deletetasktype(Request $request,$tasktypeid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata["id"]!=-1){
                    if($tokendata["type"]=="ADMIN"){
                        $row=DB::table("task_types")
                            ->where("id","=",$tasktypeid)
                            ->where("deleted_at","=",NULL)
                            ->select("*")->get();
                        if($row->isNotEmpty()){
                            DB::table("task_types")
                                ->where("id","=",$tasktypeid)
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

        public function canceltask(Request $request,$taskid){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata!=-1){
                    if($tokendata["type"]=="USER"){
                        $row=DB::table("tasks")
                            ->where("id","=",$taskid)
                            ->where("user_id","=",$tokendata["id"])
                            ->select("*")->get();

                        if($row->isNotEmpty()){
                            if($row[0]->status=="pending"){
                                DB::table("tasks")
                                    ->where("id","=",$taskid)
                                    ->update([
                                        "status"=>"canceled",
                                        "updated_at"=>$this->time()
                                    ]);

                                return response()->json([
                                    "success"=>true,
                                    "data"=>""
                                ]);
                            }else{
                                return $this->error(19);
                            }
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