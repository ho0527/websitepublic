<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


date_default_timezone_set("Asia/Taipei");
$time=date("Y-m-d H:i:s");

function editdate($date){
	return implode("T",explode(" ",$date));
}

function error($id){
	$errorcode=[
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
		["MSG_TASKTYPE_TYPE_ERROR",400],
		["MSG_TASKTYPE_NAME_EXISTS",409],
		["MSG_WORKER_NAME_EXISTS",409],
		["MSG_TASK_IS_END",400],
	];

	return response()->json([
		"success"=>false,
		"message"=>$errorcode[$id][0]
	],$errorcode[$id][1]);
}

Route::POST("user/login",function(Request $request){
	$requestdata=Validator::make($request->all(),[
		"email"=>"required|string|email",
		"password"=>"required|string"
	],[
		"required"=>4,
		"string"=>5,
		"email"=>5
	]);

	if(!$requestdata->fails()){
		$requestdata=$requestdata->validate();
		$row=DB::table("users")
			->where("email","=",$requestdata["email"])
			->select("*")->first();
		if($row&&Hash::check($requestdata["password"],$row->password_hash)){
			$token=hash("sha256",$requestdata["email"]);
			DB::table("users")
				->where("email","=",$requestdata["email"])
				->update([
					"access_token"=>$token
				]);
			return response()->json([
				"success"=>true,
				"data"=>[
					"id"=>$row->id,
					"email"=>$row->email,
					"nickname"=>$row->nickname,
					"profile_image"=>url($row->profile_image),
					"type"=>$row->type,
					"access_token"=>$token,
					"created_at"=>editdate($row->created_at),
				]
			]);
		}else{
			return error(0);
		}
	}else{
		return error($requestdata->errors()->first());
	}
});

Route::POST("user/logout",function(Request $request){
	if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
		$token=explode(" ",$request->header("X-Authorization"))[1];
		$row=DB::table("users")
			->where("access_token","=",$token)
			->select("*")->first();
		if($row){
			DB::table("users")
				->where("id","=",$row->id)
				->update([
					"access_token"=>NULL
				]);
			return response()->json([
				"success"=>true,
				"data"=>""
			]);
		}else{
			return error(2);
		}
	}else{
		return error(2);
	}
});

Route::POST("user/register",function(Request $request){
	$requestdata=Validator::make($request->all(),[
		"email"=>"required|string|email",
		"password"=>"required|string",
		"nickname"=>"required|string",
		"profile_image"=>"required|mimes:jpg,png"
	],[
		"required"=>4,
		"string"=>5,
		"email"=>5,
		"mimes"=>6
	]);

	if(!$requestdata->fails()){
		$requestdata=$requestdata->validate();
		$row=DB::table("users")
			->where("email","=",$requestdata["email"])
			->select("*")->first();
		if(!$row){
			$path="/storage/".$requestdata["profile_image"]->store("images");
			DB::table("users")
				->insert([
					"email"=>$requestdata["email"],
					"password_hash"=>Hash::make($requestdata["password"]),
					"nickname"=>$requestdata["nickname"],
					"profile_image"=>$path,
					"type"=>"USER"
				]);
			$row=DB::table("users")
				->where("email","=",$requestdata["email"])
				->select("*")->first();
			return response()->json([
				"success"=>true,
				"data"=>[
					"id"=>$row->id,
					"email"=>$row->email,
					"nickname"=>$row->nickname,
					"profile_image"=>url($row->profile_image),
					"type"=>$row->type,
					"created_at"=>editdate($row->created_at),
				]
			]);
		}else{
			return error(1);
		}
	}else{
		return error($requestdata->errors()->first());
	}
});

Route::GET("task/type",function(Request $request){
	$requestdata=Validator::make($request->all(),[
		"order_by"=>"string|in:created_at",
		"order_type"=>"string|in:asc,desc",
		"page"=>"int|min:1",
		"page_size"=>"int|min:1"
	],[
		"required"=>4,
		"string"=>5,
		"int"=>5,
		"in"=>5
	]);

	if(!$requestdata->fails()){
		$requestdata=$requestdata->validate();
		if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$row=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($row){
				$row=DB::table("task_types")
					->orderBy("created_at",$requestdata["ordertype"]??"asc")
					->select("*")->get();
				$data=[];
				for($i=($requestdata["page"]??1-1)*($requestdata["page_size"]??10);$i<min(count($row),($requestdata["page"]??1)*($requestdata["page_size"]??10));$i=$i+1){
					$input=[];

					$tasktypeinputrow=DB::table("task_type_inputs")
						->where("task_type_id","=",$row[$i]->id)
						->select("*")->get();

					for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
						$input[]=[
							"name"=>$tasktypeinputrow[$j]->name,
							"type"=>$tasktypeinputrow[$j]->type
						];
					}

					$data[]=[
						"id"=>$row[$i]->id,
						"name"=>$row[$i]->name,
						"inputs"=>$input,
						"created_at"=>editdate($row[$i]->created_at)
					];
				}

				return response()->json([
					"success"=>true,
					"data"=>[
						"total_count"=>count($row),
						"posts"=>$data
					]
				]);
			}else{
				return error(2);
			}
		}else{
			return error(2);
		}
	}else{
		return error($requestdata->errors()->first());
	}
});

Route::POST("task/type",function(Request $request){
	$requestdata=Validator::make($request->all(),[
		"name"=>"required|string",
		"inputs"=>"required|array"
	],[
		"required"=>4,
		"string"=>5,
		"array"=>5,
		"int"=>5
	]);

	if(!$requestdata->fails()){
		$requestdata=$requestdata->validate();
		if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$row=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($row){
				if($row->type=="ADMIN"){
					$row=DB::table("task_types")
						->where("name","=",$requestdata["name"])
						->where("deleted_at","=",NULL)
						->select("*")->first();
					if(!$row){
						$inputnamelist=[];
						for($i=0;$i<count($requestdata["inputs"]);$i=$i+1){
							if(!isset($requestdata["inputs"][$i]["name"])||!isset($requestdata["inputs"][$i]["type"])) return error(4);
							if(!in_array($requestdata["inputs"][$i]["type"],["string","number","boolean"])) return error(5);

							if(!in_array($requestdata["inputs"][$i]["name"],$inputnamelist)){
								$inputnamelist[]=$requestdata["inputs"][$i]["name"];
							}else{
								return error(8);
							}
						}

						DB::table("task_types")
							->insert([
								"name"=>$request["name"]
							]);

						$row=DB::table("task_types")
							->where("name","=",$requestdata["name"])
							->select("*")->first();

						$input=[];

						for($i=0;$i<count($requestdata["inputs"]);$i=$i+1){
							DB::table("task_type_inputs")
								->insert([
									"task_type_id"=>$row->id,
									"name"=>$requestdata["inputs"][$i]["name"],
									"type"=>$requestdata["inputs"][$i]["type"],
								]);
							$input[]=[
								"name"=>$requestdata["inputs"][$i]["name"],
								"type"=>$requestdata["inputs"][$i]["type"]
							];
						}

						return response()->json([
							"success"=>true,
							"data"=>[
								"id"=>$row->id,
								"name"=>$row->name,
								"inputs"=>$input,
								"created_at"=>editdate($row->created_at)
							]
						]);
					}else{
						return error(15);
					}
				}else{
					return error(3);
				}
			}else{
				return error(2);
			}
		}else{
			return error(2);
		}
	}else{
		return error($requestdata->errors()->first());
	}
});

Route::POST("task",function(Request $request){
	return "error";
	$requestdata=Validator::make($request->all(),[
		"name"=>"required|string",
		"inputs"=>"required|array"
	],[
		"required"=>4,
		"string"=>5,
		"array"=>5,
		"int"=>5
	]);

	if(!$requestdata->fails()){
		$requestdata=$requestdata->validate();
		if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$row=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($row){
				if($row->type=="ADMIN"){
					$row=DB::table("task_types")
						->where("name","=",$requestdata["name"])
						->where("deleted_at","=",NULL)
						->select("*")->first();
					if(!$row){
						$inputnamelist=[];
						for($i=0;$i<count($requestdata["inputs"]);$i=$i+1){
							if(!isset($requestdata["inputs"][$i]["name"])||!isset($requestdata["inputs"][$i]["type"])) return error(4);
							if(!in_array($requestdata["inputs"][$i]["type"],["string","number","boolean"])) return error(5);

							if(!in_array($requestdata["inputs"][$i]["name"],$inputnamelist)){
								$inputnamelist[]=$requestdata["inputs"][$i]["name"];
							}else{
								return error(8);
							}
						}

						DB::table("task_types")
							->insert([
								"name"=>$request["name"]
							]);

						$row=DB::table("task_types")
							->where("name","=",$requestdata["name"])
							->select("*")->first();

						$input=[];

						for($i=0;$i<count($requestdata["inputs"]);$i=$i+1){
							DB::table("task_type_inputs")
								->insert([
									"task_type_id"=>$row->id,
									"name"=>$requestdata["inputs"][$i]["name"],
									"type"=>$requestdata["inputs"][$i]["type"],
								]);
							$input[]=[
								"name"=>$requestdata["inputs"][$i]["name"],
								"type"=>$requestdata["inputs"][$i]["type"]
							];
						}

						return response()->json([
							"success"=>true,
							"data"=>[
								"id"=>$row->id,
								"name"=>$row->name,
								"inputs"=>$input,
								"created_at"=>editdate($row->created_at)
							]
						]);
					}else{
						return error(15);
					}
				}else{
					return error(3);
				}
			}else{
				return error(2);
			}
		}else{
			return error(2);
		}
	}else{
		return error($requestdata->errors()->first());
	}
});

Route::GET("task",function(Request $request){
	$requestdata=Validator::make($request->all(),[
		"order_by"=>"string|in:created_at",
		"order_type"=>"string|in:asc,desc",
		"page"=>"int|min:1",
		"page_size"=>"int|min:1",
		"status"=>"string"
	],[
		"required"=>4,
		"string"=>5,
		"int"=>5,
		"in"=>5
	]);

	if(!$requestdata->fails()){
		$requestdata=$requestdata->validate();
		if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$row=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($row){
				$temprow=DB::table("tasks")
					->orderBy("created_at",$requestdata["ordertype"]??"asc")
					->select("*")->get();

				$row=[];
				for($i=0;$i<count($temprow);$i=$i+1){
					if(in_array($temprow[$i]->status,explode(",",($requestdata["status"]??"")))||($requestdata["status"]??"")==""){
						$row[]=$temprow[$i];
					}
				}

				$data=[];
				for($i=($requestdata["page"]??1-1)*($requestdata["page_size"]??10);$i<min(count($row),($requestdata["page"]??1)*($requestdata["page_size"]??10));$i=$i+1){
					$data[]=[
						"id"=>$row[$i]->id,
						"status"=>$row[$i]->status,
						"created_at"=>editdate($row[$i]->created_at),
						"updated_at"=>editdate($row[$i]->updated_at)
					];
				}

				return response()->json([
					"success"=>true,
					"data"=>[
						"total_count"=>count($row),
						"posts"=>$data
					]
				]);
			}else{
				return error(2);
			}
		}else{
			return error(2);
		}
	}else{
		return error($requestdata->errors()->first());
	}

});

Route::GET("task/{task_id}",function(Request $request){

});

Route::DELETE("task/type/{typetype_id}",function(Request $request,$typetype_id){
	if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
		$token=explode(" ",$request->header("X-Authorization"))[1];
		$row=DB::table("users")
			->where("access_token","=",$token)
			->select("*")->first();
		if($row){
			if($row->type=="ADMIN"){
				$row=DB::table("task_types")
					->where("id","=",$typetype_id)
					->where("deleted_at","=",NULL)
					->select("*")->first();
				if($row){
					$row=DB::table("task_types")
						->where("id","=",$typetype_id)
						->update([
							"deleted_at"=>date("Y-m-d H:i:s")
						]);

					return response()->json([
						"success"=>true,
						"data"=>""
					]);
				}else{
					return error(12);
				}
			}else{
				return error(3);
			}
		}else{
			return error(2);
		}
	}else{
		return error(2);
	}
});

Route::POST("user/quota/{user_id}",function(Request $request,$user_id){
	$requestdata=Validator::make($request->all(),[
		"value"=>"required|integer"
	],[
		"required"=>4,
		"integer"=>5
	]);

	if(!$requestdata->fails()){
		$requestdata=$requestdata->validate();
		if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$userrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($userrow){
				if($userrow->type=="ADMIN"){
					$userrow=DB::table("users")
						->where("id","=",$user_id)
						->select("*")->first();
					if($userrow){
						$row=DB::table("user_quota_transactions")
							->insert([
								"user_id"=>$userrow->id,
								"value"=>$user_id,
								"reason"=>"RECHARGE"
							]);

						return response()->json([
							"success"=>true,
							"data"=>""
						]);
					}else{
						return error(10);
					}
				}else{
					return error(3);
				}
			}else{
				return error(2);
			}
		}else{
			return error(2);
		}
	}else{
		return error($requestdata->errors()->first());
	}
});

Route::POST("worker",function(Request $request){
	$requestdata=Validator::make($request->all(),[
		"name"=>"required|string",
		"tasktypelist"=>"required|string"
	],[
		"required"=>4,
		"string"=>5,
		"array"=>5,
		"int"=>5
	]);

	if(!$requestdata->fails()){
		$requestdata=$requestdata->validate();
		if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$userrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($userrow){
				if($userrow->type=="ADMIN"){
					$row=DB::table("workers")
						->where("name","=",$requestdata["name"])
						->where("deleted_at","=",NULL)
						->select("*")->first();
					if(!$row){
						$tasktypeinputlist=explode(",",$requestdata["tasktypelist"]);
						for($i=0;$i<count($tasktypeinputlist);$i=$i+1){
							$tastyperow=DB::table("task_types")
								->where("id","=",$tasktypeinputlist[$i])
								->select("*")->first();
							if(!$tastyperow) return error(12);
						}
						DB::table("workers")
							->insert([
								"name"=>$requestdata["name"],
								"asscess_token"=>hash("sha256",$requestdata["name"])
							]);

						$row=DB::table("workers")
							->where("name","=",$requestdata["name"])
							->where("deleted_at","=",NULL)
							->select("*")->first();

						$type=[];

						for($i=0;$i<count($tasktypeinputlist);$i=$i+1){
							DB::table("worker_task_types")
								->insert([
									"worker_id"=>$row->id,
									"task_type_id"=>$tasktypeinputlist[$i]
								]);

							$tastyperow=DB::table("task_types")
								->where("id","=",$tasktypeinputlist[$i])
								->select("*")->first();

							$tasktypeinputrow=DB::table("task_type_inputs")
								->where("id","=",$tasktypeinputlist[$i])
								->select("*")->get("");

							$input=[];

							for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
								$input[]=[
									"name"=>$tasktypeinputrow[$j]->name,
									"type"=>$tasktypeinputrow[$j]->type
								];
							}

							$type[]=[
								"id"=>$tastyperow->id,
								"name"=>$tastyperow->name,
								"inputs"=>$input,
								"created_at"=>editdate($tastyperow->created_at),
							];
						}


						return response()->json([
							"success"=>true,
							"data"=>[
								"id"=>$row->id,
								"name"=>$row->name,
								"types"=>$type,
								"is_idled"=>false,
								"created_at"=>editdate($row->created_at)
							]
						]);
					}else{
						return error(16);
					}
				}else{
					return error(3);
				}
			}else{
				return error(2);
			}
		}else{
			return error(2);
		}
	}else{
		return error($requestdata->errors()->first());
	}
});

Route::PUT("worker/{worker_id}",function(Request $request,$worker_id){
	return "error";
	$requestdata=Validator::make($request->all(),[
		"name"=>"string",
		"tasktypelist"=>"string"
	],[
		"required"=>4,
		"string"=>5,
		"array"=>5,
		"int"=>5
	]);

	if(!$requestdata->fails()){
		$requestdata=$requestdata->validate();
		if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$userrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($userrow){
				if($userrow->type=="ADMIN"){
					$row=DB::table("workers")
						->where("id","=",$worker_id)
						->where("deleted_at","=",NULL)
						->select("*")->first();
					if($row){
						$row=DB::table("workers")
							->where("name","=",$requestdata["name"]??"")
							->where("id","!=",$worker_id)
							->where("deleted_at","=",NULL)
							->select("*")->first();
						if(!$row){
							if(isset($requestdata["tasktypelist"])){
								$tasktypeinputlist=explode(",",$requestdata["tasktypelist"]);
								for($i=0;$i<count($tasktypeinputlist);$i=$i+1){
									$tastyperow=DB::table("task_types")
										->where("id","=",$tasktypeinputlist[$i])
										->select("*")->first();
									if(!$tastyperow) return error(12);
								}
								DB::table("worker_task_types")
									->where("id","=",$worker_id)
									->delete();
								for($i=0;$i<count($tasktypeinputlist);$i=$i+1){
									DB::table("worker_task_types")
										->insert([
											"worker_id"=>$row->id,
											"task_type_id"=>$tasktypeinputlist[$i]
										]);
								}
							}
							if(isset($requestdata["name"])){
								DB::table("workers")
									->where("id","=",$worker_id)
									->update([
										"name"=>$requestdata["name"]
									]);
							}
	
							$row=DB::table("workers")
								->where("id","=",$worker_id)
								->select("*")->first();
	
							$type=[];
	
							$tasktypeinputlist=[];
							$tastyperow=DB::table("worker_task_types")
								->where("worker_id","=",$row->id)
								->select("*")->get();
							for($i=0;$i<count($tastyperow);$i=$i+1){
								$tasktypeinputlist[]=$tastyperow[$i]->id;
							}
							for($i=0;$i<count($tasktypeinputlist);$i=$i+1){
								$tastyperow=DB::table("task_types")
									->where("id","=",$tasktypeinputlist[$i])
									->select("*")->first();
	
								$tasktypeinputrow=DB::table("task_type_inputs")
									->where("id","=",$tasktypeinputlist[$i])
									->select("*")->get("");
	
								$input=[];
	
								for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
									$input[]=[
										"name"=>$tasktypeinputrow[$j]->name,
										"type"=>$tasktypeinputrow[$j]->type
									];
								}
	
								$type[]=[
									"id"=>$tastyperow->id,
									"name"=>$tastyperow->name,
									"inputs"=>$input,
									"created_at"=>editdate($tastyperow->created_at),
								];
							}
	
	
							return response()->json([
								"success"=>true,
								"data"=>[
									"id"=>$row->id,
									"name"=>$row->name,
									"types"=>$type,
									"is_idled"=>false,
									"created_at"=>editdate($row->created_at)
								]
							]);
						}else{
							return error(16);
						}
					}else{
						return error(13);
					}
				}else{
					return error(3);
				}
			}else{
				return error(2);
			}
		}else{
			return error(2);
		}
	}else{
		return error($requestdata->errors()->first());
	}
});

Route::DELETE("worker/{worker_id}",function(Request $request,$worker_id){
	if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
		$token=explode(" ",$request->header("X-Authorization"))[1];
		$row=DB::table("users")
			->where("access_token","=",$token)
			->select("*")->first();
		if($row){
			if($row->type=="ADMIN"){
				$row=DB::table("workers")
					->where("id","=",$worker_id)
					->where("deleted_at","=",NULL)
					->select("*")->first();
				if($row){
					$row=DB::table("workers")
						->where("id","=",$worker_id)
						->update([
							"deleted_at"=>date("Y-m-d H:i:s")
						]);

					return response()->json([
						"success"=>true,
						"data"=>""
					]);
				}else{
					return error(13);
				}
			}else{
				return error(3);
			}
		}else{
			return error(2);
		}
	}else{
		return error(2);
	}
});

Route::GET("user",function(Request $request){
	if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
		$token=explode(" ",$request->header("X-Authorization"))[1];
		$row=DB::table("users")
			->where("access_token","=",$token)
			->select("*")->first();
		if($row){
			if($row->type=="ADMIN"){
				$row=DB::table("users")
					->select("*")->get();
				$data=[];
				for($i=0;$i<count($row);$i=$i+1){
					$data[]=[
						"id"=>$row[$i]->id,
						"email"=>$row[$i]->email,
						"nickname"=>$row[$i]->nickname,
						"profile_image"=>url($row[$i]->profile_image),
						"type"=>$row[$i]->type,
						"created_at"=>editdate($row[$i]->created_at),
					];
				}

				return response()->json([
					"success"=>true,
					"data"=>[
						"total_count"=>count($row),
						"posts"=>$data
					]
				]);
			}else{
				return error(3);
			}
		}else{
			return error(2);
		}
	}else{
		return error(2);
	}
});

Route::PUT("user/{user_id}",function(Request $request){

});

Route::DELETE("task/cancel/{task_id}",function(Request $request,$task_id){
	if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
		$token=explode(" ",$request->header("X-Authorization"))[1];
		$row=DB::table("users")
			->where("access_token","=",$token)
			->select("*")->first();
		if($row){
			if($row->type=="USER"){
				$row=DB::table("tasks")
					->where("id","=",$task_id)
					->select("*")->first();
				if($row){
					if(in_array($row->status,["pending","processing","canceled"])){
						$row=DB::table("tasks")
							->where("id","=",$task_id)
							->update([
								"status"=>"canceled"
							]);
						return response()->json([
							"success"=>true,
							"data"=>""
						]);
					}else{
						return error(17);
					}
				}else{
					return error(7);
				}
			}else{
				return error(3);
			}
		}else{
			return error(2);
		}
	}else{
		return error(2);
	}
});

Route::GET("user/leftquota",function(Request $request){
	if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
		$token=explode(" ",$request->header("X-Authorization"))[1];
		$userrow=DB::table("users")
			->where("access_token","=",$token)
			->select("*")->first();
		if($userrow){
			if($userrow->type=="USER"){
				$row=DB::table("user_quota_transactions")
					->where("user_id","=",$userrow->id)
					->select("*")->get();
				$total=0;

				for($i=0;$i<count($row);$i=$i+1){
					$total=$total+$row[$i]->value;
				}

				return response()->json([
					"success"=>true,
					"data"=>$total
				]);
			}else{
				return error(3);
			}
		}else{
			return error(2);
		}
	}else{
		return error(2);
	}
});

Route::GET("user/quota",function(Request $request){
	if($request->header("X-Authorization")&&isset(explode(" ",$request->header("X-Authorization"))[1])){
		$token=explode(" ",$request->header("X-Authorization"))[1];
		$userrow=DB::table("users")
			->where("access_token","=",$token)
			->select("*")->first();
		if($userrow){
			if($userrow->type=="USER"){
				$row=DB::table("user_quota_transactions")
					->where("user_id","=",$userrow->id)
					->select("*")->get();
				$data=[];

				for($i=0;$i<count($row);$i=$i+1){
					$data[]=[
						"id"=>$row[$i]->id,
						"value"=>$row[$i]->value,
						"reason"=>$row[$i]->reason,
						"created_at"=>$row[$i]->created_at,
					];
				}

				return response()->json([
					"success"=>true,
					"data"=>[
						"total_count"=>count($row),
						"quotas"=>$data
					]
				]);
			}else{
				return error(3);
			}
		}else{
			return error(2);
		}
	}else{
		return error(2);
	}
});

Route::GET("worker/task",function(Request $request){

});

Route::POST("worker/task/{task_id}",function(Request $request){

});
