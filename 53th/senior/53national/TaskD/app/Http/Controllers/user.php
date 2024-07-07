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
                "email"=>"required|email|string",
                "password"=>"required|string",
            ],[
                "required"=>5,
                "email"=>6,
                "string"=>6,
            ]);

            if(!$requestdata->fails()){
                $requestdata=$requestdata->validate();
                $row=DB::table("users")
                    ->where("email","=",$requestdata["email"])
                    ->select("*")->get();
                if($row->isNotEmpty()&&Hash::check($requestdata["password"],$row[0]->password)){
                    DB::table("users")
                        ->where("id","=",$row[0]->id)
                        ->update([
                            "access_token"=>hash("sha256",$requestdata["email"]),
                        ]);
                    $row=DB::table("users")
                        ->where("email","=",$requestdata["email"])
                        ->select("*")->get();
                    return response()->json([
                        "success"=>true,
                        "data"=>Controller::user($row,"login")
                    ]);
                }else{
                    return Controller::error(0);
                }
            }else{
                return Controller::error($requestdata->errors()->first());
            }
        }

        public function register(Request $request){
            $requestdata=Validator::make($request->all(),[
                    "email"=>"required|email|string",
                    "nickname"=>"required|string",
                    "password"=>"required|string|min:5",
                    "profile_image"=>"required|mimes:png,jpg",
                ],[
                "required"=>5,
                "email"=>6,
                "string"=>6,
                "mimes"=>6,
                "min"=>2,
            ]);

            if(!$requestdata->fails()){
                $requestdata=$requestdata->validate();
                $row=DB::table("users")
                    ->where("email","=",$requestdata["email"])
                    ->select("*")->get();
                if($row->isEmpty()){
                    $path=$requestdata["profile_image"]->store("images");
                    DB::table("users")->insert([
                        "email"=>$requestdata["email"],
                        "password"=>Hash::make($requestdata["password"]),
                        "nickname"=>$requestdata["nickname"],
                        "profile_image"=>$path,
                        "type"=>"USER",
                        "created_at"=>Controller::time()
                    ]);
                    $row=DB::table("users")
                        ->select("*")->get();
                    return response()->json([
                        "success"=>true,
                        "data"=>Controller::user([$row[count($row)-1]],"normal")
                    ]);
                }else{
                    return Controller::error(1);
                }
            }else{
                return Controller::error($requestdata->errors()->first());
            }
        }

        public function logout(Request $request){
            $userid=Controller::logincheck(explode("Bearer ",$request->header("Authorization"))[1]);
            if($userid){
                DB::table("users")
                    ->where("id","=",$userid)
                    ->update([
                        "access_token"=>NULL,
                    ]);
                return response()->json([
                    "success"=>true
                ]);
            }else{
                return Controller::error(3);
            }
        }
    }
?>