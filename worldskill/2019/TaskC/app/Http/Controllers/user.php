<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    include("error.php");
    include("function.php");

    class user extends Controller{
        public function event(Request $request){

        }

        public function getevent(Request $request){

        }

        public function login(Request $request){
            if($request->has("email")&&$request->has("password")){
                $email=$request->input("email");
                $password=$request->input("password");
                $row=DB::table("user")
                    ->where("email","=",$email)
                    ->select("*")->get();
                if($row->isNotEmpty()&&$password==$row[0]->password){
                    DB::table("user")
                        ->where("id","=",$row[0]->id)
                        ->update([
                            "accesstoken"=>hash("sha256",$email),
                        ]);
                    $row=DB::table("user")
                        ->where("email","=",$email)
                        ->select("*")->get();
                    return response()->json([
                        "success"=>true,
                        "message"=>"",
                        "data"=>$row[0]->accesstoken
                    ]);
                }else{
                    return loginerror();
                }
            }else{
                return missingfield();
            }
        }

        public function logout(Request $request){
            $userid=logincheck($request->input("token"));
            if($userid!=0){
                DB::table("user")
                    ->where("id","=",$userid)
                    ->update([
                        "login_token"=>NULL,
                    ]);
                return response()->json([
                    "success"=>true,
                    "message"=>"",
                    "data"=>""
                ]);
            }else{
                return tokenerror();
            }
        }

        public function registration(Request $request){

        }

        public function getregistration(Request $request){

        }
    }
?>