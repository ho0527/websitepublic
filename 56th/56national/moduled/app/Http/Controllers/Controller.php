<?php
    namespace App\Http\Controllers;

    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use Illuminate\Foundation\Bus\DispatchesJobs;
    use Illuminate\Foundation\Validation\ValidatesRequests;
    use Illuminate\Routing\Controller as BaseController;
    use Illuminate\Support\Facades\DB;

    session_start();
    date_default_timezone_set("Asia/Taipei");

    class Controller extends BaseController{
        use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

        public function timestarp(string $data){
            return implode("T",explode(" ",$data));
        }

        public function time(){
            return date("Y-m-d H:i:s");
        }

        public function logincheck(string $token){
            $token=str_replace("Bearer ","",$token);
            $row=DB::table("users")
                ->where("token","=",$token)
                ->select("*")->get();
            if($row->isNotEmpty()){
                return [
                    "id"=>$row[0]->user_id,
                    "role"=>$row[0]->role
                ];
            }else{
                return -1;
            }
        }

        public function error(int $key){
            $data=[
                ["Login Failed",403], // 0
                ["Username already taken",409], // 1
                ["Email already taken",400], // 2
                ["Invalid access token",401], // 3
                ["Permission denied",403], // 4
                ["Validation failed",400], // 5
                ["Not found",404], // 6
                ["Cover Not Found",404], // 7
                ["Admin access required",403], // 8
                ["Access Token is required",401], // 9
                ["Last admin domotion forbidden",403], // 10
                ["Banned user update failed",409], // 11
                ["Cannot ban self",400], // 12
                ["Cannot ban order admin",403], // 13
                ["User is banned",403], // 14
                ["Invalid year format",400], // 15
                ["Too many covers provided",400], // 16
                ["Invalid file type",400], // 17
                ["Access denied",403], // 18
                ["Invalid parameter",400], // 19
                ["Invalid cursor",400], // 20
                ["[delete]",404],
                ["MSG_TASK_IS_END",400]
            ];

            return response()->json([
                "success"=>false,
                "message"=>$data[(int)$key][0]
            ],$data[(int)$key][1]);
        }

        public function random($len=30){
            $arr=["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9"];
            $data="";
            for($i=0;$i<$len;$i=$i+1){
                $data=$data.$arr[rand(0,count($arr)-1)];
            }
            return $data;
        }
    }
?>