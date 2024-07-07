<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;

    class user extends Controller{
        public function login(Request $request){
            $requestdata=Validator::make($request->all(),[
                "email"=>"required",
                "password"=>"required",
            ],[
                "required"=>4
            ]);
            if(!$requestdata->fails()){
                $requestdata=$requestdata->validate();
                $row=DB::table("user")
                    ->where("email","=",$requestdata["email"])
                    ->select("*")->get();
                if($row->isNotEmpty()&&$requestdata["password"]==$row[0]->password){
                    DB::table("user")
                        ->where("id","=",$row[0]->id)
                        ->update([
                            "accesstoken"=>hash("sha256",$requestdata["email"]),
                        ]);
                    $row=DB::table("user")
                        ->where("email","=",$requestdata["email"])
                        ->select("*")->get();
                    return response()->json([
                        "success"=>true,
                        "message"=>"",
                        "data"=>$row[0]->accesstoken
                    ]);
                }else{
                    return Controller::error(0);
                }
            }else{
                return Controller::error($requestdata->messages()->first());
            }
        }

        public function logout(Request $request){
            $userid=Controller::logincheck();
            if($userid){
                DB::table("user")
                    ->where("id","=",$userid)
                    ->update([
                        "accesstoken"=>NULL,
                    ]);
                return response()->json([
                    "success"=>true,
                    "message"=>"",
                    "data"=>""
                ]);
            }else{
                return Controller::error(1);
            }
        }

        public function getuser(Request $request){
            $userid=Controller::logincheck();
            if($userid){
                if($userid=="1"){
                    $data=[];
                    $row=DB::table("user")
                        ->select("*")->get();
                    for($i=0;$i<count($row);$i=$i+1){
                        $enabled=true;
                        if($row[$i]->disabled=="true"){
                            $enabled=false;
                        }
                        $data[]=[
                            "id"=>$row[$i]->id,
                            "email"=>$row[$i]->email,
                            "nickname"=>$row[$i]->nickname,
                            "enabled"=>$enabled,
                            "user_type"=>$row[$i]->permission,
                            "created_at"=>$row[$i]->createdat,
                            "updated_at"=>$row[$i]->createdat,
                        ];
                    }
                    return response()->json([
                        "success"=>true,
                        "message"=>"",
                        "data"=>$data
                    ]);
                }else{
                    return Controller::error(3);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function banuser(Request $request,$userid){
            $loginuserid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($loginuserid){
                if($loginuserid=="1"){
                    if($request->has("ban")){
                        if($request->input("ban")){
                            $row=DB::table("user")
                                ->where("id","=",$userid)
                                ->select("*")->get();
                            if($row->isNotEmpty()){
                                if($row[0]->disabled=="false"){
                                    DB::table("user")
                                        ->where("id","=",$userid)
                                        ->update([
                                            "disabled"=>"true",
                                        ]);
                                }else{
                                    DB::table("user")
                                        ->where("id","=",$userid)
                                        ->update([
                                            "disabled"=>"false",
                                        ]);
                                }
                                return response()->json([
                                    "success"=>true,
                                    "message"=>"",
                                    "data"=>""
                                ]);
                            }else{
                                return Controller::error(15);
                            }
                        }else{
                            return Controller::error(5);
                        }
                    }else{
                        return Controller::error(4);
                    }
                }else{
                    return Controller::error(3);
                }
            }else{
                return Controller::error(1);
            }
        }

        public function getblocklist(Request $request){
            $loginuserid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($loginuserid){
                if($loginuserid=="1"){
                    $row=DB::table("blocklist")
                        ->latest()
                        ->select("*")->get();

                    $data=[];

                    for($i=0;$i<count($row);$i=$i+1){
                        $data[]=[
                            "id"=>$row[$i]->id,
                            "punished_user_id"=>$row[$i]->userid,
                            "from"=>$row[$i]->from,
                            "to"=>$row[$i]->to,
                            "reason"=>$row[$i]->reason,
                            "created_at"=>$row[$i]->createat
                        ];
                    }

                    return response()->json([
                        "success"=>true,
                        "message"=>"",
                        "data"=>$data
                    ]);
                }else{
                    return Controller::error(3);
                }
            }else{
                return Controller::error(1);
            }
        }
    }
?>