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
                ["Login Failed",403],
                ["Username already taken",409],
                ["Email already taken",400],
                ["Invalid access token",401],
                ["Permission denied",403],
                ["Validation failed",400],
                ["Not found",404],
                ["Cover Not Found",404],
                ["MSG_TASKTYPE_INPUT_NAME_EXISTS",409],
                ["MSG_USER_QUOTA_IS_EMPTY",409],
                ["MSG_USER_NOT_EXISTS",404],
                ["MSG_NO_TASK_PENDING",404],
                ["MSG_TASKTYPE_NOT_EXISTS",404],
                ["MSG_WORKER_NOT_EXISTS",404],
                ["[delete]",400],
                ["MSG_TASKTYPE_TYPE_ERROR",400],
                ["MSG_TASKTYPE_NAME_EXISTS",409],
                ["MSG_WORKER_NAME_EXISTS",409],
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