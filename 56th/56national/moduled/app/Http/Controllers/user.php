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
                "username"=>"required|string",
                "password"=>"required|string"
            ],[
                "required"=>4,
                "string"=>5
            ]);

            if(!$requestdata->fails()){
                $requestdata=$requestdata->validate();
                $row=DB::table("users")
                    ->where("username","=",$requestdata["username"])
                    ->select("*")->get();
                if($row->isNotEmpty()&&Hash::check($requestdata["password"],$row[0]->password_hash)){
                    if(!$row[0]->is_banned){
                        $token=hash("sha256",$row[0]->username);
                        DB::table("users")
                            ->where("user_id","=",$row[0]->user_id)
                            ->update([
                                "token"=>$token
                            ]);
                        return response()->json([
                            "success"=>true,
                            "data"=>[
                                "id"=>$row[0]->user_id,
                                "username"=>$row[0]->username,
                                "email"=>$row[0]->email,
                                "role"=>$row[0]->role,
                                "token"=>$token,
                                "created_at"=>$this->timestarp($row[0]->created_at)
                            ]
                        ]);
                    }else{
                            return $this->error(14);
                    }
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
                "username"=>"required|string",
                "password"=>"required|string"
            ],[
                "required"=>5,
                "string"=>5,
                "email"=>5,
            ]);

            if(!$requestdata->fails()){
                $requestdata=$requestdata->validate();
                $row=DB::table("users")
                    ->where("username","=",$requestdata["username"])
                    ->select("*")->get();
                if($row->isEmpty()){
                    $row=DB::table("users")
                        ->where("email","=",$requestdata["email"])
                        ->select("*")->get();
                    if($row->isEmpty()){
                        DB::table("users")->insert([
                            "email"=>$requestdata["email"],
                            "password_hash"=>Hash::make($requestdata["password"]),
                            "username"=>$requestdata["username"],
                            "role"=>"user",
                            "created_at"=>Controller::time()
                        ]);
                        $row=DB::table("users")
                            ->select("*")->get();
                        $row=$row[count($row)-1];
                        return response()->json([
                            "success"=>true,
                            "data"=>[
                                "id"=>$row->user_id,
                                "email"=>$row->email,
                                "username"=>$row->username,
                                "role"=>$row->role,
                                "created_at"=>$this->timestarp($row->created_at),
                                "updated_at"=>$this->timestarp($row->updated_at),
                            ]
                        ],201);
                    }else{
                        return Controller::error(2);
                    }
                }else{
                    return Controller::error(1);
                }
            }else{
                return $this->error($requestdata->errors()->first());
            }
        }

        public function signout(Request $request){
            if($request->header("X-Authorization")){
                $tokendata=$this->logincheck($request->header("X-Authorization"));
                if($tokendata!=-1){
                    DB::table("users")
                        ->where("user_id","=",$tokendata["id"])
                        ->update([
                            "token"=>NULL
                        ]);
                    return response()->json([
                        "success"=>true
                    ]);
                }else{
                    return $this->error(3);
                }
            }else{
                return $this->error(9);
            }
        }

        public function getuserlist(Request $request){
            if($request->header("X-Authorization")){
                $tokendata=$this->logincheck($request->header("X-Authorization"));
                if($tokendata!=-1){
                    if($tokendata["role"]=="admin"){
                        $requestdata=Validator::make($request->all(),[
                            "limit"=>"integer",
                            "cursor"=>"string"
                        ],[
                            "string"=>19,
                            "integer"=>19,
                        ]);

                        if(!$requestdata->fails()){
                            $requestdata=$requestdata->validated();

                            $limit=$requestdata["limit"]??10;
                            $cursor=$requestdata["cursor"]??null;
                            $cursororg=$cursor;

                            if($cursor!=null){
                                $cursor=json_decode(base64_decode($cursor),true);
                                if(isset($cursor["id"])){
                                    $cursor=$cursor["id"];
                                }else{
                                    return $this->error(20);
                                }
                            }else{
                                $cursor=0;
                            }

                            if($limit<1||100<$limit){
                                return $this->error(19);
                            }

                            $data=[];
                            $row=DB::table("users")
                                ->where("user_id",">",$cursor)
                                ->limit($limit)
                                ->select("*")->get();

                            for($i=0;$i<count($row);$i=$i+1){
                                $data[]=[
                                    "id"=>$row[$i]->user_id,
                                    "email"=>$row[$i]->email,
                                    "username"=>$row[$i]->username,
                                    "role"=>$row[$i]->role,
                                    "is_banned"=>$row[$i]->is_banned,
                                    "created_at"=>$this->timestarp($row[$i]->created_at)
                                ];
                            }

                            return response()->json([
                                "success"=>true,
                                "data"=>$data,
                                "meta"=>[
                                    "prev_cursor"=>$cursororg,
                                    "next_cursor"=>count($row)==$limit?base64_encode(json_encode(["id"=>$row[count($row)-1]->user_id])):null
                                ]
                            ]);
                        }else{
                            return $this->error($requestdata->errors()->first());
                        }
                    }else{
                        return $this->error(8);
                    }
                }else{
                    return $this->error(3);
                }
            }else{
                return $this->error(9);
            }
        }

        public function edituserrole(Request $request, int $userid){
            if($request->header("X-Authorization")){
                $tokendata=$this->logincheck($request->header("X-Authorization"));
                if($tokendata!=-1){
                    if($tokendata["role"]=="admin"){
                        $requestdata=Validator::make($request->all(),[
                            "role"=>"required|string|in:user,admin"
                        ],[
                            "required"=>5,
                            "string"=>5,
                            "in"=>5,
                        ]);
                        if(!$requestdata->fails()){
                            $requestdata=$requestdata->validated();
                            $row=DB::table("users")
                                ->where("user_id","=",$userid)
                                ->select("*")->first();
                            if($row){
                                if($row->is_banned){
                                    return $this->error(11);
                                }
                                if($row->role=="admin"){
                                    $adminrow=DB::table("users")
                                        ->where("role","=","admin")
                                        ->where("user_id","!=",$userid)
                                        ->select("*")->get();
                                    if($adminrow->isEmpty()){
                                        return $this->error(10);
                                    }
                                }
                                DB::table("users")
                                    ->where("user_id","=",$userid)
                                    ->update([
                                        "role"=>$requestdata["role"]
                                    ]);
                                $row=DB::table("users")
                                    ->where("user_id","=",$userid)
                                    ->select("*")->first();
                                return response()->json([
                                    "success"=>true,
                                    "data"=>[
                                        "id"=>$row->user_id,
                                        "email"=>$row->email,
                                        "username"=>$row->username,
                                        "role"=>$row->role,
                                        "is_banned"=>$row->is_banned,
                                        "created_at"=>$this->timestarp($row->created_at),
                                        "updated_at"=>$this->timestarp($row->updated_at)
                                    ]
                                ]);
                            }else{
                                return $this->error(6);
                            }
                        }else{
                            return $this->error($requestdata->errors()->first());
                        }
                    }else{
                        return $this->error(8);
                    }
                }else{
                    return $this->error(3);
                }
            }else{
                return $this->error(9);
            }
        }

        public function banuser(Request $request, int $userid){
            if($request->header("X-Authorization")){
                $tokendata=$this->logincheck($request->header("X-Authorization"));
                if($tokendata!=-1){
                    if($tokendata["role"]=="admin"){
                        $row=DB::table("users")
                            ->where("user_id","=",$userid)
                            ->select("*")->first();
                        if($row){
                            if($tokendata["id"]==$userid){
                                return $this->error(12);
                            }
                            if($row->role=="admin"){
                                return $this->error(13);
                            }
                            DB::table("users")
                                ->where("user_id","=",$userid)
                                ->update([
                                    "is_banned"=>true,
                                    "token"=>NULL
                                ]);
                            $row=DB::table("users")
                                ->where("user_id","=",$userid)
                                ->select("*")->first();
                            return response()->json([
                                "success"=>true,
                                "data"=>[
                                    "id"=>$row->user_id,
                                    "email"=>$row->email,
                                    "username"=>$row->username,
                                    "role"=>$row->role,
                                    "is_banned"=>$row->is_banned,
                                    "updated_at"=>$this->timestarp($row->updated_at)
                                ]
                            ]);
                        }else{
                            return $this->error(6);
                        }
                    }else{
                        return $this->error(8);
                    }
                }else{
                    return $this->error(3);
                }
            }else{
                return $this->error(9);
            }
        }

        public function unbanuser(Request $request, int $userid){
            if($request->header("X-Authorization")){
                $tokendata=$this->logincheck($request->header("X-Authorization"));
                if($tokendata!=-1){
                    if($tokendata["role"]=="admin"){
                        $row=DB::table("users")
                            ->where("user_id","=",$userid)
                            ->select("*")->first();
                        if($row){
                            DB::table("users")
                                ->where("user_id","=",$userid)
                                ->update([
                                    "is_banned"=>false
                                ]);
                            $row=DB::table("users")
                                ->where("user_id","=",$userid)
                                ->select("*")->first();
                            return response()->json([
                                "success"=>true,
                                "data"=>[
                                    "id"=>$row->user_id,
                                    "email"=>$row->email,
                                    "username"=>$row->username,
                                    "role"=>$row->role,
                                    "is_banned"=>$row->is_banned,
                                    "updated_at"=>$this->timestarp($row->updated_at)
                                ]
                            ]);
                        }else{
                            return $this->error(6);
                        }
                    }else{
                        return $this->error(8);
                    }
                }else{
                    return $this->error(3);
                }
            }else{
                return $this->error(9);
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
                        if($tokendata["role"]=="ADMIN"){
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
