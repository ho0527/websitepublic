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

        public function time(){
            return date("Y-m-d H:i:s");
        }
    
        public function logincheck($token){
            $row=DB::table("user")
                ->where("accesstoken","=",$token)
                ->select("*")->get();
            if($row->isNotEmpty()){
                return $row[0]->id;
            }else{
                return 0;
            }
        }

        public function controllerdelcomment($id){
            $row=DB::table("comment")
                ->where("replyid","=",$id)
                ->select("*")->get();
    
            DB::table("comment")
                ->where("id","=",$id)
                ->delete();
            for($i=0;$i<count($row);$i=$i+1){
                Controller::controllerdelcomment($row[$i]->id);
            }
        }

        public function error($key){
            $data=[
                ["MSG_INVALID_LOGIN",403], // 0
                ["MSG_INVALID_TOKEN",401], // 1
                ["MSG_USER_DISABLED",403], // 2
                ["MSG_PERMISSION_DENY",403], // 3
                ["MSG_MISSING_FIELD",400], // 4
                ["MSG_WRONG_DATA_TYPE",400], // 5
                ["MSG_VIDEO_CAN_NOT_PROCESS",400], // 6
                ["MSG_WRONG_VIDEOS_LENGTH",400], // 7
                ["MSG_COVER_CAN_NOT_PROCESS",400], // 8
                ["MSG_CATEGORY_NOT_EXISTS",404], // 9
                ["MSG_VIDEO_NOT_EXISTS",404], // 10
                ["MSG_COMMENT_NOT_EXISTS",404], // 11
                ["MSG_PLAYLIST_NOT_EXISTS",404], // 12
                ["MSG_VIDEO_NOT_IN_PLAYLIST",404], // 13
                ["MSG_PROGRAM_NOT_EXISTS",404], // 14
                ["MSG_USER_NOT_EXISTS",404], // 15
                ["MSG_VIDEO_NOT_IN_PROGRAM",404], // 16
                ["MSG_VIDEO_ALREADY_IN_PLAYLIST",409], // 17
                ["MSG_VIDEO_ALREADY_IN_PROGRAM",409], // 18
                ["MSG_DUPLICATED_PLAYLIST",409], // 19
                ["MSG_DUPLICATED_PROGRAM",409], // 20
                ["MSG_VIDEO_NOT_PUBLIC",409], // 21
            ];

            return response()->json([
                "success"=>false,
                "message"=>$data[(int)$key][0],
                "data"=>""
            ],$data[(int)$key][1]);
        }
    }
