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
            $row=DB::table("users")
                ->where("access_token","=",$token)
                ->select("*")->get();
            if($row->isNotEmpty()){
                return $row[0]->id;
            }else{
                return 0;
            }
        }

        public function user($row,$type){
            $mainrow=[
                "id"=>$row[0]->id,
                "email"=>$row[0]->email,
                "nickname"=>$row[0]->nickname,
                "profile_image"=>url($row[0]->profile_image),
                "type"=>$row[0]->type,
                "created_at"=>implode("T",explode(" ",$row[0]->created_at))
            ];
            if($type=="login"){
                $mainrow["access_token"]=$row[0]->access_token;
            }
            return $mainrow;
        }

        public function image($row){
            $data=[];
            for($i=0;$i<$row->count();$i=$i+1){
                $mainrow=[
                    "id"=>$row[$i]->id,
                    "url"=>url($row[$i]->url),
                    "title"=>$row[$i]->title,
                    "updated_at"=>$row[$i]->updated_at,
                    "created_at"=>$row[$i]->created_at,
                ];
                $data[]=$mainrow;
            }
            return $data;
        }

        public function imagedetail($row){
            $data=[];
            for($i=0;$i<count($row);$i=$i+1){
                $userrow=DB::table("users")
                    ->where("id","=",$row[$i]->user_id)
                    ->select("*")->get();
                $imageviewrow=DB::table("image_views")
                    ->select("*")->get();
                $mainrow=[
                    "id"=>$row[$i]->id,
                    "url"=>url($row[$i]->url),
                    "author"=>Controller::user($userrow,"normal"),
                    "title"=>$row[$i]->title,
                    "description"=>$row[$i]->description,
                    "width"=>$row[$i]->width,
                    "height"=>$row[$i]->height,
                    "mimetype"=>$row[$i]->mimetype,
                    "view_count"=>count($imageviewrow),
                    "updated_at"=>implode("T",explode(" ",$row[$i]->updated_at)),
                    "created_at"=>implode("T",explode(" ",$row[$i]->created_at))
                ];
                $data[]=$mainrow;
            }
            return $data;
        }

        public function controllercomment($row){
            $data=[];
            for($i=0;$i<count($row);$i=$i+1){
                $iddata=$_SESSION["idlist"];
                $id=$row[$i]->id;
                if(in_array($id,$iddata)){
                    $key=array_search($id,$iddata);
                    unset($iddata[$key]);
                    $_SESSION["idlist"]=$iddata;
                    $imagerow=DB::table("images")
                            ->where("id","=",$row[$i]->image_id)
                            ->select("*")->get()[0];
                    $userrow=DB::table("users")
                        ->where("id","=",$row[$i]->user_id)
                        ->select("*")->get();
                    $replycommentrow=DB::table("comments")
                        ->where("comment_id","=",$id)
                        ->select("*")->get();
                    if($imagerow->deleted_at==NULL){
                        $mainrow=[
                            "id"=>$row[$i]->id,
                            "user"=>Controller::user($userrow,"normal"),
                            "content"=>$row[$i]->content,
                            "comments"=>Controller::controllercomment($replycommentrow),
                            "created_at"=>implode("T",explode(" ",$row[$i]->created_at))
                        ];
                        $data[]=$mainrow;
                    }
                }
            }
            return $data;
        }

        public function controllerdelcomment($id){
            $row=DB::table("comments")
                ->where("comment_id","=",$id)
                ->select("*")->get();

            DB::table("comments")
                ->where("id","=",$id)
                ->delete();
            for($i=0;$i<count($row);$i=$i+1){
                Controller::controllerdelcomment($row[$i]->id);
            }
        }

        public function error($key){
            $data=[
                ["MSG_INVALID_LOGIN",403], // 0
                ["MSG_USER_EXISTS",409], // 1
                ["MSG_PASSWORD_NOT_SECURE",409], // 2
                ["MSG_INVALID_ACCESS_TOKEN",401], // 3
                ["MSG_PERMISSION_DENY",403], // 4
                ["MSG_MISSING_FIELD",400], // 5
                ["MSG_WRONG_DATA_TYPE",400], // 6
                ["MSG_IMAGE_NOT_EXISTS",404], // 7
                ["MSG_COMMENT_NOT_EXISTS",404], // 8
                ["MSG_USER_NOT_EXISTS",404], // 9
                ["MSG_INVALID_FILE_FORMAT",400], // 10
            ];

            return response()->json([
                "success"=>false,
                "message"=>$data[(int)$key][0]
            ],$data[(int)$key][1]);
        }
    }
?>