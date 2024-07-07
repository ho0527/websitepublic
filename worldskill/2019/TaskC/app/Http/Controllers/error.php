<?php
    function loginerror(){
        return response()->json(["success"=>false,"message"=>"MSG_INVALID_LOGIN","data"=>""],403);
    }

    function tokenerror(){
        return response()->json(["success"=>false,"message"=>"MSG_INVALID_TOKEN","data"=>""],401);
    }

    function userdisabled(){
        return response()->json(["success"=>false,"message"=>"MSG_USER_DISABLED","data"=>""],403);
    }

    function nopermission(){
        return response()->json(["success"=>false,"message"=>"MSG_PERMISSION_DENY","data"=>""],403);
    }

    function missingfield(){
        return response()->json(["success"=>false,"message"=>"MSG_MISSING_FIELD","data"=>""],400);
    }

    function datatypeerror(){
        return response()->json(["success"=>false,"message"=>"MSG_WRONG_DATA_TYPE","data"=>""],400);
    }

    function videoprocesserror(){
        return response()->json(["success"=>false,"message"=>"MSG_VIDEO_CAN_NOT_PROCESS","data"=>""],400);
    }

    function videolengtherror(){
        return response()->json(["success"=>false,"message"=>"MSG_WRONG_VIDEOS_LENGTH","data"=>""],400);
    }

    function covererror(){
        return response()->json(["success"=>false,"message"=>"MSG_COVER_CAN_NOT_PROCESS","data"=>""],400);
    }

    function categorynotfound(){
        return response()->json(["success"=>false,"message"=>"MSG_CATEGORY_NOT_EXISTS","data"=>""],404);
    }

    function videonotfound(){
        return response()->json(["success"=>false,"message"=>"MSG_VIDEO_NOT_EXISTS","data"=>""],404);
    }

    function commentnotfound(){
        return response()->json(["success"=>false,"message"=>"MSG_COMMENT_NOT_EXISTS","data"=>""],404);
    }

    function playlistnotfound(){
        return response()->json(["success"=>false,"message"=>"MSG_PLAYLIST_NOT_EXISTS","data"=>""],404);
    }

    function videonotinplaylist(){
        return response()->json(["success"=>false,"message"=>"MSG_VIDEO_NOT_IN_PLAYLIST","data"=>""],404);
    }

    function programnotfound(){
        return response()->json(["success"=>false,"message"=>"MSG_PROGRAM_NOT_EXISTS","data"=>""],404);
    }

    function usernotfound(){
        return response()->json(["success"=>false,"message"=>"MSG_USER_NOT_EXISTS","data"=>""],404);
    }

    function videonotinprogram(){
        return response()->json(["success"=>false,"message"=>"MSG_VIDEO_NOT_IN_PROGRAM","data"=>""],404);
    }

    function videoinplaylist(){
        return response()->json(["success"=>false,"message"=>"MSG_VIDEO_ALREADY_IN_PLAYLIST","data"=>""],409);
    }

    function videoinprogram(){
        return response()->json(["success"=>false,"message"=>"MSG_VIDEO_ALREADY_IN_PROGRAM","data"=>""],409);
    }

    function playlisterror(){
        return response()->json(["success"=>false,"message"=>"MSG_DUPLICATED_PLAYLIST","data"=>""],409);
    }

    function programerror(){
        return response()->json(["success"=>false,"message"=>"MSG_DUPLICATED_PROGRAM","data"=>""],409);
    }

    function videonotpublic(){
        return response()->json(["success"=>false,"message"=>"MSG_VIDEO_NOT_PUBLIC","data"=>""],409);
    }
?>