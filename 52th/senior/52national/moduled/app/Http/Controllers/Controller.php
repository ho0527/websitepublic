<?php
    namespace App\Http\Controllers;

    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use Illuminate\Foundation\Bus\DispatchesJobs;
    use Illuminate\Foundation\Validation\ValidatesRequests;
    use Illuminate\Routing\Controller as BaseController;

    class Controller extends BaseController{
        use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
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
    }
