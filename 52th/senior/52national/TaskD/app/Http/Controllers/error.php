<?php
    function loginerror(){
        return response()->json(["success"=>false,"message"=>"MSG_INVALID_LOGIN","data"=>""],403);
    }

    function userexist(){
        return response()->json(["success"=>false,"message"=>"MSG_USER_EXISTS","data"=>""],409);
    }

    function passworderror(){
        return response()->json(["success"=>false,"message"=>"MSG_PASSWORD_NOT_SECURE","data"=>""],409);
    }

    function tokenerror(){
        return response()->json(["success"=>false,"message"=>"MSG_INVALID_ACCESS_TOKEN","data"=>""],401);
    }

    function nopermission(){
        return response()->json(["success"=>false,"message"=>"MSG_PERMISSION_DENY","data"=>""],403);
    }

    function missingfield(){
        return response()->json(["success"=>false,"message"=>"MSG_MISSING_FIELD","data"=>""],400);
    }

    function datatypeerror(){
        return response()->json(["success"=>false,"message"=>"MSG_WROND_DATA_TYPE","data"=>""],400);
    }

    function imageerror(){
        return response()->json(["success"=>false,"message"=>"MSG_IMAGE_CAN_NOT_PROCESS","data"=>""],400);
    }

    function posterror(){
        return response()->json(["success"=>false,"message"=>"MSG_POST_NOT_EXISTS","data"=>""],404);
    }

    function commenterror(){
        return response()->json(["success"=>false,"message"=>"MSG_COMMENT_NOT_EXISTS","data"=>""],404);
    }

    function usererror(){
        return response()->json(["success"=>false,"message"=>"MSG_USER_NOT_EXISTS","data"=>""],404);
    }
?>