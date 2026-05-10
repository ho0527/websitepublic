<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;

    class user extends Controller{
        public function signin(Request $request){
            $requestdata=Validator::make($request->all(),[
                "email"=>"required|string|email",
                "password"=>"required|string"
            ],[
                "required"=>4,
                "string"=>5,
                "email"=>5
            ]);

            if(!$requestdata->fails()){
                $requestdata=$requestdata->validate();
                $row=DB::table("users")
                    ->where("email","=",$requestdata["email"])
                    ->select("*")->get();
                if($row->isNotEmpty()&&Hash::check($requestdata["password"],$row[0]->password_hash)){
                    $token=hash("sha256",$row[0]->email);
                    DB::table("users")
                        ->where("id","=",$row[0]->id)
                        ->update([
                            "access_token"=>$token
                        ]);
                    return response()->json([
                        "success"=>true,
                        "data"=>[
                            "id"=>$row[0]->id,
                            "email"=>$row[0]->email,
                            "nickname"=>$row[0]->nickname,
                            "profile_image"=>url($row[0]->profile_image),
                            "type"=>$row[0]->type,
                            "access_token"=>$token,
                            "created_at"=>$this->timestarp($row[0]->created_at)
                        ]
                    ]);
                }else{
                    return $this->error(0);
                }
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function signup(Request $request){
            $requestdata=Validator::make($request->all(),[
                "email"=>"required|string|email",
                "nickname"=>"required|string",
                "password"=>"required|string",
                "profile_image"=>"required|mimes:png,jpg"
            ],[
                "required"=>4,
                "string"=>5,
                "email"=>5,
                "mimes"=>6
            ]);

            if(!$requestdata->fails()){
                $requestdata=$requestdata->validate();
                $row=DB::table("users")
                    ->where("email","=",$requestdata["email"])
                    ->select("*")->get();
                if($row->isEmpty()){
                    $path="/storage/".$requestdata["profile_image"]->store("images");
                    DB::table("users")->insert([
                        "email"=>$requestdata["email"],
                        "password_hash"=>Hash::make($requestdata["password"]),
                        "nickname"=>$requestdata["nickname"],
                        "profile_image"=>$path,
                        "type"=>"USER",
                        "created_at"=>Controller::time()
                    ]);
                    $row=DB::table("users")
                        ->select("*")->get();
                    $row=$row[count($row)-1];
                    DB::table("user_quota_transactions")->insert([
                        "user_id"=>$row->id,
                        "value"=>10,
                        "reason"=>"CREATE_USER",
                        "created_at"=>Controller::time()
                    ]);
                    return response()->json([
                        "success"=>true,
                        "data"=>[
                            "id"=>$row->id,
                            "email"=>$row->email,
                            "nickname"=>$row->nickname,
                            "profile_image"=>url($row->profile_image),
                            "type"=>$row->type,
                            "created_at"=>$this->timestarp($row->created_at)
                        ]
                    ]);
                }else{
                    return Controller::error(1);
                }
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function signout(Request $request){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata!=-1){
                    DB::table("users")
                        ->where("id","=",$tokendata["id"])
                        ->update([
                            "access_token"=>NULL
                        ]);
                    return response()->json([
                        "success"=>true,
                        "data"=>""
                    ]);
                }else{
                    return $this->error(2);
                }
            }else{
                return $this->error(2);
            }
        }

        public function getuserlist(Request $request){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata!=-1){
                    if($tokendata["type"]=="ADMIN"){
                        $row=DB::table("users")
                            ->select("*")->get();
                        $data=[];
                        for($i=0;$i<count($row);$i=$i+1){
                            $data[]=[
                                "id"=>$row[$i]->id,
                                "email"=>$row[$i]->email,
                                "nickname"=>$row[$i]->nickname,
                                "profile_image"=>url($row[$i]->profile_image),
                                "type"=>$row[$i]->type,
                                "created_at"=>$this->timestarp($row[$i]->created_at)
                            ];
                        }
                        return response()->json([
                            "success"=>true,
                            "data"=>[
                                "total_count"=>count($data),
                                "users"=>$data
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
        }

        public function getleftquota(Request $request){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata!=-1){
                    if($tokendata["type"]=="USER"){
                        $row=DB::table("user_quota_transactions")
                            ->where("user_id","=",$tokendata["id"])
                            ->sum("value");
                        return response()->json([
                            "success"=>true,
                            "data"=>(int)$row
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
        }

        public function getquotalist(Request $request){
            if($request->header("Authorization")){
                $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                if($tokendata!=-1){
                    if($tokendata["type"]=="USER"){
                        $row=DB::table("user_quota_transactions")
                            ->where("user_id","=",$tokendata["id"])
                            ->select("*")->get();

                        $data=[];

                        for($i=0;$i<count($row);$i=$i+1){
                            $data[]=[
                                "id"=>$row[$i]->id,
                                "value"=>$row[$i]->value,
                                "reason"=>$row[$i]->reason,
                                "created_at"=>$this->timestarp($row[$i]->created_at)
                            ];
                        }

                        return response()->json([
                            "success"=>true,
                            "data"=>$data
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
        }

        public function edituser(Request $request,$userid){
            $requestdata=Validator::make($request->all(),[
                "email"=>"string|email",
                "nickname"=>"string",
                "password"=>"string"
            ],[
                "required"=>4,
                "string"=>5,
                "email"=>5,
            ]);
            if(!$requestdata->fails()){
                $requestdata=$requestdata->validated();
                if($request->header("Authorization")){
                    $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata!=-1){
                        if($tokendata["type"]=="ADMIN"){
                            $row=DB::table("users")
                                ->where("id","=",$userid)
                                ->select("*")->get();
                            if($row->isNotEmpty()){
                                DB::table("users")
                                    ->where("id","=",$userid)
                                    ->update([
                                        "email"=>$request["email"]??$row[0]->email,
                                        "nickname"=>$request["nickname"]??$row[0]->nickname,
                                        "password_hash"=>Hash::make($request["password"])??$row[0]->password_hash
                                    ]);
                                $row=DB::table("users")
                                    ->where("id","=",$userid)
                                    ->select("*")->get();
                                return response()->json([
                                    "success"=>true,
                                    "data"=>[
                                        "id"=>$row[0]->id,
                                        "email"=>$row[0]->email,
                                        "nickname"=>$row[0]->nickname,
                                        "profile_image"=>url($row[0]->profile_image),
                                        "type"=>$row[0]->type,
                                        "created_at"=>$this->timestarp($row[0]->created_at)
                                    ]
                                ]);
                            }else{
                                return $this->error(10);
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

        public function newquota(Request $request,$userid){
            $requestdata=Validator::make($request->all(),[
                "value"=>"required|integer",
            ],[
                "required"=>4,
                "integer"=>5
            ]);

            if(!$requestdata->fails()){
                $requestdata=$requestdata->validated();
                if($request->header("Authorization")){
                    $tokendata=$this->logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
                    if($tokendata!=-1){
                        if($tokendata["type"]=="ADMIN"){
                            $row=DB::table("users")
                                ->where("id","=",$userid)
                                ->select("*")->get();
                            if(count($row)==1){
                                DB::table("user_quota_transactions")->insert([
                                    "user_id"=>$userid,
                                    "value"=>$requestdata["value"],
                                    "reason"=>"RECHARGE",
                                    "created_at"=>Controller::time()
                                ]);

                                return response()->json([
                                    "success"=>true,
                                    "data"=>""
                                ]);
                            }else{
                                return $this->error(10);
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
    }
?>