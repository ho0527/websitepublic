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

        public function timestarp($data){
            return implode("T",explode(" ",$data));
        }

        public function time(){
            return date("Y-m-d H:i:s");
        }

        public function logincheck($token){
            $row=DB::table("users")
                ->where("access_token","=",$token)
                ->select("*")->get();
            if($row->isNotEmpty()){
                return [
                    "id"=>$row[0]->id,
                    "type"=>$row[0]->type
                ];
            }else{
                return -1;
            }
        }

        public function error($key){
            $data=[
                ["MSG_INVALID_LOGIN",403],
                ["MSG_USER_EXISTS",409],
                ["MSG_INVALID_ACCESS_TOKEN",401],
                ["MSG_PERMISSION_DENY",403],
                ["MSG_MISSING_FIELD",400],
                ["MSG_WRONG_DATA_TYPE",400],
                ["MSG_IMAGE_CAN_NOT_PROCESS",400],
                ["MSG_TASK_NOT_EXISTS",404],
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
                ["MSG_WORKER_NOT_EXISTS_IN_TASKTYPE",404],
                ["MSG_TASK_IS_PROCESSING_OR_FINISHED",400]
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