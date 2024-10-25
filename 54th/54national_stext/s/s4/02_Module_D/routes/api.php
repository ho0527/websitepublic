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

	function gettime(){
		return date("Y-m-d H:i:s");
	}

	function totime($data){
		return implode("T",explode(" ",$data));
	}

	function updateurl($data){
		if($data==""||$data==NULL)
			return $data;
		else
			return url($data);
	}

	function resp($data,$code=200){
		return response()->json($data,$code);
	}

	function error($id){
		$errorlist=[
			"1"=>["MSG_INVALID_LOGIN",403],
			"2"=>["MSG_USER_EXISTS",409],
			"3"=>["MSG_INVALID_ACCESS_TOKEN",401],
			"4"=>["MSG_PERMISSION_DENY",403],
			"5"=>["MSG_MISSING_FIELD",400],
			"6"=>["MSG_WRONG_DATA_TYPE",400],
			"7"=>["MSG_IMAGE_CAN_NOT_PROCESS",400],
			"8"=>["MSG_TASK_NOT_EXISTS",404],
			"9"=>["MSG_TASKTYPE_INPUT_NAME_EXISTS",409],
			"10"=>["MSG_USER_QUOTA_IS_EMPTY",409],
			"11"=>["MSG_USER_NOT_EXISTS",404],
			"12"=>["MSG_NO_TASK_PENDING",404],
			"13"=>["MSG_TASKTYPE_NOT_EXISTS",404],
			"14"=>["MSG_WORKER_NOT_EXISTS",404],
			"15"=>["MSG_TASKTYPE_TYPE_ERROR",400],
			"16"=>["MSG_TASKTYPE_NAME_EXISTS",409],
			"17"=>["MSG_WORKER_NAME_EXISTS",409],
			"18"=>["MSG_TASK_END_OR_CANCEL",400]
		];

		return resp([
			"success"=>false,
			"message"=>$errorlist[$id][0]
		],$errorlist[$id][1]);
	}

	Route::POST("/user/login",function(Request $request){
		$requestdata=Validator::make($request->all(),[
			"email"=>"required|string|email",
			"password"=>"required|string"
		],[
			"required"=>"5",
			"string"=>"6",
			"email"=>"6"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$row=DB::table("users")
				->where("email","=",$requestdata["email"])
				->select("*")->first();
			if($row&&Hash::check($requestdata["password"],$row->password_hash)){
				DB::table("users")
					->where("email","=",$requestdata["email"])
					->update([
						"access_token"=>hash("sha256",$requestdata["email"])
					]);
				$row=DB::table("users")
					->where("email","=",$requestdata["email"])
					->select("*")->first();
				return resp([
					"success"=>true,
					"data"=>[
						"id"=>$row->id,
						"email"=>$row->email,
						"nickname"=>$row->nickname,
						"profile_image"=>updateurl($row->profile_image),
						"type"=>$row->type,
						"access_token"=>$row->access_token,
						"created_at"=>totime($row->created_at)
					]
				]);
			}else{
				return error(1);
			}
		}else{
			return error($requestdata->errors()->first());
		}
	});

	Route::POST("/user/logout",function(Request $request){
		$header=$request->header("X-Authorization");
		if($header&&isset(explode(" ",$header)[1])){
			$userrow=DB::table("users")
				->where("access_token","=",explode(" ",$header)[1])
				->get("*")->first();
			if($userrow){
				DB::table("users")
					->where("access_token","=",explode(" ",$header)[1])
					->update([
						"access_token"=>NULL
					]);
				return resp([
					"success"=>true,
					"data"=>""
				]);
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	Route::POST("/user/register",function(Request $request){
		$requestdata=Validator::make($request->all(),[
			"email"=>"required|string|email",
			"password"=>"required|string",
			"nickname"=>"required|string",
			"profile_image"=>"required|mimes:jpg,png"
		],[
			"required"=>"5",
			"string"=>"6",
			"email"=>"6",
			"mimes"=>"7"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$row=DB::table("users")
				->where("email","=",$requestdata["email"])
				->select("*")->first();
			if(!$row){
				$path=$requestdata["profile_image"]->store("image");
				DB::table("users")
					->insert([
						"email"=>$requestdata["email"],
						"password_hash"=>Hash::make($requestdata["password"]),
						"nickname"=>$requestdata["nickname"],
						"type"=>"USER",
						"profile_image"=>$path,
						"access_token"=>"",
						"created_at"=>gettime()
					]);
				$row=DB::table("users")
					->where("email","=",$requestdata["email"])
					->select("*")->first();
				DB::table("user_quota_transactions")
					->insert([
						"user_id"=>$row->id,
						"value"=>10,
						"reason"=>"CREATE_USER",
						"created_at"=>gettime()
					]);
				return resp([
					"success"=>true,
					"data"=>[
						"id"=>$row->id,
						"email"=>$row->email,
						"nickname"=>$row->nickname,
						"profile_image"=>updateurl($row->profile_image),
						"type"=>$row->type,
						"created_at"=>totime($row->created_at)
					]
				]);
			}else{
				return error(2);
			}
		}else{
			return error($requestdata->errors()->first());
		}
	});

	Route::GET("/task/type",function(Request $request){
		$requestdata=Validator::make($request->all(),[
			"order_by"=>"string|in:created_at",
			"order_type"=>"string|in:asc,desc",
			"page"=>"integer|min:1",
			"page_size"=>"integer|min:1",
		],[
			"string"=>"6",
			"integer"=>"6",
			"min"=>"6",
			"in"=>"6"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$header=$request->header("X-Authorization");
			if($header&&isset(explode(" ",$header)[1])){
				$userrow=DB::table("users")
					->where("access_token","=",explode(" ",$header)[1])
					->get("*")->first();
				if($userrow){
					$orderby=$requestdata["order_by"]??"created_at";
					$ordertype=$requestdata["order_type"]??"asc";
					$page=$requestdata["page"]??1;
					$pagesize=$requestdata["page_size"]??10;

					$row=DB::table("task_types")
						->orderBy($orderby,$ordertype)
						->select("*")->get();

					$data=[];

					for($i=($page-1)*$pagesize;$i<min(($page)*$pagesize,count($row));$i=$i+1){
						$inputdata=[];

						$tasktypeinputrow=DB::table("task_type_inputs")
							->where("task_type_id","=",$row[$i]->id)
							->select("*")->get();

						for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
							$inputdata[]=[
								"name"=>$tasktypeinputrow[$j]->name,
								"type"=>$tasktypeinputrow[$j]->type
							];
						}

						$data[]=[
							"id"=>$row[$i]->id,
							"name"=>$row[$i]->name,
							"inputs"=>$inputdata,
							"created_at"=>totime($row[$i]->created_at)
						];
					}

					return resp([
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
				return error(3);
			}
		}else{
			return error($requestdata->errors()->first());
		}

	});

	Route::POST("/task/type",function(Request $request){
		$requestdata=Validator::make($request->all(),[
			"name"=>"required|string",
			"inputs"=>"array"
		],[
			"required"=>"5",
			"string"=>"6",
			"array"=>"6"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$header=$request->header("X-Authorization");
			if($header&&isset(explode(" ",$header)[1])){
				$userrow=DB::table("users")
					->where("access_token","=",explode(" ",$header)[1])
					->get("*")->first();
				if($userrow){
					if($userrow->type=="ADMIN"){
						$row=DB::table("task_types")
							->where("name","=",$requestdata["name"])
							->where("deleted_at","=",NULL)
							->select("*")->first();
						if(!$row){
							$inputlist=[];
							for($i=0;$i<count($requestdata["inputs"]);$i=$i+1){
								if(isset($requestdata["inputs"][$i]["name"])&&isset($requestdata["inputs"][$i]["type"])){
									if(in_array($requestdata["inputs"][$i]["type"],["string","number","boolean"])){
										if(!in_array($requestdata["inputs"][$i]["name"],$inputlist))
											$inputlist[]=$requestdata["inputs"][$i]["name"];
										else
											return error(9);
									}else{
										return error(6);
									}
								}else{
									return error(5);
								}
							}

							DB::table("task_types")->insert([
								"name"=>$requestdata["name"],
								"created_at"=>gettime()
							]);

							$row=DB::table("task_types")
								->where("name","=",$requestdata["name"])
								->where("deleted_at","=",NULL)
								->select("*")->first();

							$inputdata=[];

							for($i=0;$i<count($requestdata["inputs"]);$i=$i+1){
								DB::table("task_type_inputs")->insert([
									"task_type_id"=>$row->id,
									"name"=>$requestdata["inputs"][$i]["name"],
									"type"=>$requestdata["inputs"][$i]["type"]
								]);
								$inputdata[]=[
									"name"=>$requestdata["inputs"][$i]["name"],
									"type"=>$requestdata["inputs"][$i]["type"]
								];
							}
							return resp([
								"success"=>true,
								"data"=>[
									"id"=>$row->id,
									"name"=>$row->name,
									"inputs"=>$inputdata,
									"created_at"=>totime($row->created_at)
								]
							]);
						}else{
							return error(16);
						}
					}else{
						return error(4);
					}
				}else{
					return error(3);
				}
			}else{
				return error(3);
			}
		}else{
			return error($requestdata->errors()->first());
		}
	});

	Route::POST("/task",function(Request $request){
		return "[ERROR]";
	});

	Route::GET("/task",function(Request $request){
		$requestdata=Validator::make($request->all(),[
			"order_by"=>"string|in:created_at,updated_at",
			"order_type"=>"string|in:asc,desc",
			"page"=>"integer|min:1",
			"page_size"=>"integer|min:1",
			"status"=>"string",
		],[
			"string"=>"6",
			"integer"=>"6",
			"min"=>"6",
			"in"=>"6"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$header=$request->header("X-Authorization");
			if($header&&isset(explode(" ",$header)[1])){
				$userrow=DB::table("users")
					->where("access_token","=",explode(" ",$header)[1])
					->get("*")->first();
				if($userrow){
					$orderby=$requestdata["order_by"]??"created_at";
					$ordertype=$requestdata["order_type"]??"asc";
					$page=$requestdata["page"]??1;
					$pagesize=$requestdata["page_size"]??10;
					$status=isset($requestdata["status"])?explode(",",$requestdata["status"]):"";

					$temprow=DB::table("tasks")
						->orderBy($orderby,$ordertype)
						->select("*")->get();

					$row=[];
					$data=[];

					for($i=0;$i<count($temprow);$i=$i+1){
						if($status==""||in_array($temprow[$i]->status,$status)){
							$row[]=$temprow[$i];
						}
					}

					for($i=($page-1)*$pagesize;$i<min(($page)*$pagesize,count($row));$i=$i+1){
						$data[]=[
							"id"=>$row[$i]->id,
							"status"=>$row[$i]->status,
							"created_at"=>totime($row[$i]->created_at),
							"updated_at"=>totime($row[$i]->updated_at)
						];
					}

					return resp([
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
				return error(3);
			}
		}else{
			return error($requestdata->errors()->first());
		}
	});

	Route::GET("/task/{task_id}",function(Request $request,$taskid){
		$header=$request->header("X-Authorization");
		if($header&&isset(explode(" ",$header)[1])){
			$userrow=DB::table("users")
				->where("access_token","=",explode(" ",$header)[1])
				->get("*")->first();
			if($userrow){
				if($userrow->type=="USER"){
					$row=DB::table("tasks")
						->where("id","=",$taskid)
						->where("user_id","=",$userrow->id)
						->select("*")->first();
					if($row){
						$tasktypeinputdata=[];
						$taskinputdata=[];
						$workerdata=null;

						$tasktyperow=DB::table("task_types")
							->where("id","=",$row->task_type_id)
							->select("*")->first();

						$tasktypeinputrow=DB::table("task_type_inputs")
							->where("task_type_id","=",$row->task_type_id)
							->select("*")->get();

						for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
							$tasktypeinputdata[]=[
								"name"=>$tasktypeinputrow[$j]->name,
								"type"=>$tasktypeinputrow[$j]->type
							];
						}

						$userrow=DB::table("users")
							->where("id","=",$row->user_id)
							->select("*")->first();

						if($row->worker_id!=NULL){
							$workertasktype=[];

							$workerrow=DB::table("workers")
								->where("id","=",$row->worker_id)
								->select("*")->first();
							$idled=false;
							$taskrow=DB::table("tasks")
								->where("worker_id","=",$workerrow->id)
								->where("status","=","processing")
								->select("*")->first();
							if($taskrow){
								$idled=true;
							}

							$workertasktyperow=DB::table("worker_task_types")
								->where("worker_id","=",$workerrow->id)
								->select("*")->get();

							for($i=0;$i<count($workertasktyperow);$i=$i+1){
								$workertasktyptasktypeerow=DB::table("task_types")
									->where("id","=",$workertasktyperow[$i]->task_type_id)
									->select("*")->first();
								$workertasktyptasktypeinputerow=DB::table("task_type_inputs")
									->where("task_type_id","=",$workertasktyperow[$i]->task_type_id)
									->select("*")->get();

								$workertasktypeinputdata=[];
								for($j=0;$j<count($workertasktyptasktypeinputerow);$j=$j+1){
									$workertasktypeinputdata[]=[
										"name"=>$workertasktyptasktypeinputerow[$j]->name,
										"type"=>$workertasktyptasktypeinputerow[$j]->type
									];
								}

								$workertasktype[]=[
									"id"=>$workertasktyptasktypeerow->id,
									"name"=>$workertasktyptasktypeerow->name,
									"inputs"=>$workertasktypeinputdata,
									"created_at"=>totime($workertasktyptasktypeerow->created_at)
								];
							}

							$workerdata=[
								"id"=>$workerrow->id,
								"name"=>$workerrow->name,
								"types"=>$workertasktype,
								"is_idled"=>$idled,
								"created_at"=>$workerrow->created_at
							];
						}

						$taskinputrow=DB::table("task_inputs")
							->where("task_id","=",$taskid)
							->select("*")->get();
						for($i=0;$i<count($taskinputrow);$i=$i+1){
							$taskinputdata[]=[
								"name"=>$taskinputrow[$i]->name,
								"type"=>$taskinputrow[$i]->type,
								"value"=>$taskinputrow[$i]->value
							];
						}
						return resp([
							"success"=>true,
							"data"=>[
								"id"=>$row->id,
								"type"=>[
									"id"=>$tasktyperow->id,
									"name"=>$tasktyperow->name,
									"inputs"=>$tasktypeinputdata,
									"created_at"=>$tasktyperow->created_at
								],
								"user"=>[
									"id"=>$userrow->id,
									"email"=>$userrow->email,
									"nickname"=>$userrow->nickname,
									"profile_image"=>updateurl($userrow->profile_image),
									"type"=>$userrow->type,
									"created_at"=>totime($userrow->created_at)
								],
								"worker"=>$workerdata,
								"taskinput"=>$taskinputdata,
								"status"=>$row->status,
								"result"=>updateurl($row->result),
								"created_at"=>totime($row->created_at),
								"updated_at"=>totime($row->updated_at)
							]
						]);
					}else{
						return error(8);
					}
				}else{
					return error(4);
				}
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	Route::DELETE("/task/type/{typetype_id}",function(Request $request,$tasktypeid){
		$header=$request->header("X-Authorization");
		if($header&&isset(explode(" ",$header)[1])){
			$userrow=DB::table("users")
				->where("access_token","=",explode(" ",$header)[1])
				->get("*")->first();
			if($userrow){
				if($userrow->type=="ADMIN"){
					$row=DB::table("task_types")
						->where("id","=",$tasktypeid)
						->where("deleted_at","=",NULL)
						->select("*")->first();
					if($row){
						$row=DB::table("task_types")
							->where("id","=",$tasktypeid)
							->update([
								"deleted_at"=>gettime()
							]);

						return resp([
							"success"=>true,
							"data"=>""
						]);
					}else{
						return error(13);
					}
				}else{
					return error(4);
				}
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	Route::POST("/user/quota/{user_id}",function(Request $request,$userid){
		$requestdata=Validator::make($request->all(),[
			"value"=>"required|integer"
		],[
			"required"=>"5",
			"integer"=>"6"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$header=$request->header("X-Authorization");
			if($header&&isset(explode(" ",$header)[1])){
				$userrow=DB::table("users")
					->where("access_token","=",explode(" ",$header)[1])
					->get("*")->first();
				if($userrow){
					if($userrow->type=="ADMIN"){
						$row=DB::table("users")
							->where("id","=",$userid)
							->select("*")->first();
						if($row){
							$totalcount=DB::table("user_quota_transactions")
								->where("user_id","=",$userid)
								->sum("value");
							if(0<=$totalcount+$request["value"]){
								DB::table("user_quota_transactions")->insert([
									"user_id"=>$userid,
									"value"=>$requestdata["value"],
									"reason"=>"RECHARGE"
								]);
								return resp([
									"success"=>true,
									"data"=>""
								]);
							}else{
								return error(10);
							}
						}else{
							return error(11);
						}
					}else{
						return error(4);
					}
				}else{
					return error(3);
				}
			}else{
				return error(3);
			}
		}else{
			return error($requestdata->errors()->first());
		}
	});

	Route::POST("/worker",function(Request $request){
		$requestdata=Validator::make($request->all(),[
			"name"=>"required|string",
			"tasktypelist"=>"array"
		],[
			"required"=>"5",
			"string"=>"6",
			"array"=>"6"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$header=$request->header("X-Authorization");
			if($header&&isset(explode(" ",$header)[1])){
				$userrow=DB::table("users")
					->where("access_token","=",explode(" ",$header)[1])
					->get("*")->first();
				if($userrow){
					if($userrow->type=="ADMIN"){
						$row=DB::table("workers")
							->where("name","=",$requestdata["name"])
							->where("deleted_at","=",NULL)
							->select("*")->first();
						if(!$row){
							if(isset($requestdata["tasktypelist"])){
								for($i=0;$i<count($requestdata["tasktypelist"]);$i=$i+1){
									$tasktyperow=DB::table("task_types")
										->where("id","=",$requestdata["tasktypelist"][$i])
										->select("*")->first();
									if(!$tasktyperow){
										return error(13);
									}
								}

								DB::table("workers")->insert([
									"name"=>$requestdata["name"],
									"asscess_token"=>hash("sha256",$requestdata["name"]),
									"created_at"=>gettime()
								]);

								$row=DB::table("workers")
									->where("name","=",$requestdata["name"])
									->where("deleted_at","=",NULL)
									->select("*")->first();

								$tasktypedata=[];

								for($i=0;$i<count($requestdata["tasktypelist"]);$i=$i+1){
									DB::table("worker_task_types")->insert([
										"worker_id"=>$row->id,
										"task_type_id"=>$requestdata["tasktypelist"][$i]
									]);
									$tasktypeinputdata=[];
									$tasktyperow=DB::table("task_types")
										->where("id","=",$requestdata["tasktypelist"][$i])
										->select("*")->first();
									$tasktypeinputrow=DB::table("task_type_inputs")
										->where("task_type_id","=",$requestdata["tasktypelist"][$i])
										->select("*")->get();
									for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
										$tasktypeinputdata[]=[
											"name"=>$tasktypeinputrow[$j]->name,
											"type"=>$tasktypeinputrow[$j]->type
										];
									}
									$tasktypedata[]=[
										"id"=>$tasktyperow->id,
										"name"=>$tasktyperow->name,
										"inputs"=>$tasktypeinputdata,
										"created_at"=>totime($tasktyperow->created_at)
									];
								}

								$idled=false;
								$taskrow=DB::table("tasks")
									->where("worker_id","=",$row->id)
									->where("status","=","processing")
									->select("*")->first();
								if($taskrow){
									$idled=true;
								}

								return resp([
									"success"=>true,
									"data"=>[
										"id"=>$row->id,
										"name"=>$row->name,
										"types"=>$tasktypedata,
										"is_idled"=>$idled,
										"created_at"=>$row->created_at
									]
								]);
							}else{
								return error(6);
							}
						}else{
							return error(17);
						}
					}else{
						return error(4);
					}
				}else{
					return error(3);
				}
			}else{
				return error(3);
			}
		}else{
			return error($requestdata->errors()->first());
		}
	});

	Route::PUT("/worker/{worker_id}",function(Request $request,$workerid){
		return "[ERROR]";
		$requestdata=Validator::make($request->all(),[
			"name"=>"string",
			"tasktypelist"=>"array"
		],[
			"string"=>"6",
			"array"=>"6"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$header=$request->header("X-Authorization");
			if($header&&isset(explode(" ",$header)[1])){
				$userrow=DB::table("users")
					->where("access_token","=",explode(" ",$header)[1])
					->get("*")->first();
				if($userrow){
					if($userrow->type=="ADMIN"){
						$row=DB::table("workers")
							->where("id","=",$workerid)
							->where("deleted_at","=",NULL)
							->select("*")->first();
						if($row){
							if(isset($requestdata["name"])){
								$row2=DB::table("workers")
									->where("id","!=",$workerid)
									->where("name","=",$requestdata["name"])
									->where("deleted_at","=",NULL)
									->select("*")->first();
								if($row2)
									return error(17);
							}

							if(isset($requestdata["tasktypelist"])){
								for($i=0;$i<count($requestdata["tasktypelist"]);$i=$i+1){
									$tasktyperow=DB::table("task_types")
										->where("id","=",$requestdata["tasktypelist"][$i])
										->select("*")->first();
									if(!$tasktyperow){
										return error(13);
									}
								}
								DB::table("worker_task_types")
									->where("worker_id","=",$workerid)
									->delete();
								for($i=0;$i<count($requestdata["tasktypelist"]);$i=$i+1){
									DB::table("worker_task_types")->insert([
										"worker_id"=>$workerid,
										"task_type_id"=>$requestdata["tasktypelist"][$i]
									]);
								}
							}
	
							$row=DB::table("workers")
								->where("id","!=",$workerid)
								->select("*")->first();
	
							DB::table("workers")
								->where("id","=",$workerid)
								->update([
									"name"=>$requestdata["name"]??$row->name
								]);
	
							$row=DB::table("workers")
								->where("id","!=",$workerid)
								->select("*")->first();
	
							$tasktypedata=[];
	
							$workertasktyperow=DB::table("worker_task_types")
								->where("worker_id","=",$workerid)
								->select("*")->get();
	
							for($i=0;$i<count($workertasktyperow);$i=$i+1){
								$tasktypeinputdata=[];
								$tasktyperow=DB::table("task_types")
									->where("id","=",$workertasktyperow[$i]->task_type_id)
									->select("*")->first();
								$tasktypeinputrow=DB::table("task_type_inputs")
									->where("task_type_id","=",$workertasktyperow[$i]->task_type_id)
									->select("*")->get();
								for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
									$tasktypeinputdata[]=[
										"name"=>$tasktypeinputrow[$j]->name,
										"type"=>$tasktypeinputrow[$j]->type
									];
								}
								$tasktypedata[]=[
									"id"=>$tasktyperow->id,
									"name"=>$tasktyperow->name,
									"inputs"=>$tasktypeinputdata,
									"created_at"=>totime($tasktyperow->created_at)
								];
							}
	
							$idled=false;
							$taskrow=DB::table("tasks")
								->where("worker_id","=",$row->id)
								->where("status","=","processing")
								->select("*")->first();
							if($taskrow){
								$idled=true;
							}
	
							return resp([
								"success"=>true,
								"data"=>[
									"id"=>$row->id,
									"name"=>$row->name,
									"types"=>$tasktypedata,
									"is_idled"=>$idled,
									"created_at"=>$row->created_at
								]
							]);
						}else{
							return error(14);
						}
					}else{
						return error(4);
					}
				}else{
					return error(3);
				}
			}else{
				return error(3);
			}
		}else{
			return error($requestdata->errors()->first());
		}
	});

	Route::DELETE("/worker/{worker_id}",function(Request $request,$workerid){
		$header=$request->header("X-Authorization");
		if($header&&isset(explode(" ",$header)[1])){
			$userrow=DB::table("users")
				->where("access_token","=",explode(" ",$header)[1])
				->get("*")->first();
			if($userrow){
				if($userrow->type=="ADMIN"){
					$row=DB::table("workers")
						->where("id","=",$workerid)
						->where("deleted_at","=",NULL)
						->select("*")->first();
					if($row){
						$row=DB::table("workers")
							->where("id","=",$workerid)
							->update([
								"deleted_at"=>gettime()
							]);

						return resp([
							"success"=>true,
							"data"=>""
						]);
					}else{
						return error(14);
					}
				}else{
					return error(4);
				}
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	Route::GET("/user",function(Request $request){
		$header=$request->header("X-Authorization");
		if($header&&isset(explode(" ",$header)[1])){
			$userrow=DB::table("users")
				->where("access_token","=",explode(" ",$header)[1])
				->get("*")->first();
			if($userrow){
				if($userrow->type=="ADMIN"){
					$row=DB::table("users")
						->select("*")->get();
					$data=[];
					for($i=0;$i<count($row);$i=$i+1){
						$data[]=[
							"id"=>$row[$i]->id,
							"email"=>$row[$i]->email,
							"nickname"=>$row[$i]->nickname,
							"profile_image"=>updateurl($row[$i]->profile_image),
							"type"=>$row[$i]->type,
							"created_at"=>totime($row[$i]->created_at)
						];
					}
					return resp([
						"success"=>true,
						"data"=>[
							"total_count"=>count($row),
							"users"=>$data
						]
					]);
				}else{
					return error(4);
				}
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	Route::PUT("/user/{user_id}",function(Request $request,$userid){
		$requestdata=Validator::make($request->all(),[
			"nickname"=>"string"
		],[
			"string"=>"6"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$header=$request->header("X-Authorization");
			if($header&&isset(explode(" ",$header)[1])){
				$userrow=DB::table("users")
					->where("access_token","=",explode(" ",$header)[1])
					->get("*")->first();
				if($userrow){
					if($userrow->type=="ADMIN"){
						$row=DB::table("users")
							->where("id","=",$userid)
							->select("*")->first();
						if($row){
							DB::table("users")
								->where("id","=",$userid)
								->update([
									"nickname"=>$requestdata["nickname"]??$row->nickname
								]);
							$row=DB::table("users")
								->where("id","=",$userid)
								->select("*")->first();
							return resp([
								"success"=>true,
								"data"=>[
									"id"=>$row->id,
									"email"=>$row->email,
									"nickname"=>$row->nickname,
									"profile_image"=>updateurl($row->profile_image),
									"type"=>$row->type,
									"created_at"=>totime($row->created_at)
								]
							]);
						}else{
							return error(11);
						}
					}else{
						return error(4);
					}
				}else{
					return error(3);
				}
			}else{
				return error(3);
			}
		}else{
			return error($requestdata->errors()->first());
		}
	});

	Route::POST("/task/{task_id}/cancel",function(Request $request,$taskid){
		$header=$request->header("X-Authorization");
		if($header&&isset(explode(" ",$header)[1])){
			$userrow=DB::table("users")
				->where("access_token","=",explode(" ",$header)[1])
				->get("*")->first();
			if($userrow){
				if($userrow->type=="USER"){
					$row=DB::table("tasks")
						->where("id","=",$taskid)
						->select("*")->first();
					if($row){
						if(in_array($row->status,["pending","processing"])){
							$row=DB::table("tasks")
								->where("id","=",$taskid)
								->update([
									"status"=>"canceled"
								]);

							return resp([
								"success"=>true,
								"data"=>""
							]);
						}else{
							return error(18);
						}
					}else{
						return error(8);
					}
				}else{
					return error(4);
				}
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	Route::GET("/user/leftquota",function(Request $request){
		$header=$request->header("X-Authorization");
		if($header&&isset(explode(" ",$header)[1])){
			$userrow=DB::table("users")
				->where("access_token","=",explode(" ",$header)[1])
				->get("*")->first();
			if($userrow){
				$totalcount=DB::table("user_quota_transactions")
					->where("user_id","=",$userrow->id)
					->sum("value");
				return resp([
					"success"=>true,
					"data"=>(int)$totalcount
				]);
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	Route::GET("/user/quota",function(Request $request){
		$header=$request->header("X-Authorization");
		if($header&&isset(explode(" ",$header)[1])){
			$userrow=DB::table("users")
				->where("access_token","=",explode(" ",$header)[1])
				->get("*")->first();
			if($userrow){
				$row=DB::table("user_quota_transactions")
					->where("user_id","=",$userrow->id)
					->select("*")->get();
				$data=[];
				for($i=0;$i<count($row);$i=$i+1){
					$data[]=[
						"id"=>$row[$i]->id,
						"value"=>$row[$i]->value,
						"reason"=>$row[$i]->reason,
						"created_at"=>$row[$i]->created_at
					];
				}
				return resp([
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
			return error(3);
		}
	});

	Route::GET("/worker/task",function(Request $request){
		$header=$request->header("X-Authorization");
		if($header&&isset(explode(" ",$header)[1])){
			$workerrow=DB::table("workers")
				->where("asscess_token","=",explode(" ",$header)[1])
				->get("*")->first();
			if($workerrow){
				$row=DB::table("tasks")
					->where("worker_id","=",$workerrow->id)
					->where("status","=","processing")
					->select("*")->first();
				$taskid=null;
				if(!$row){
					$row2=DB::table("tasks")
						->where("status","=","pending")
						->select("*")->first();
					if($row2){
						DB::table("tasks")
							->where("id","=",$row2->id)
							->update([
								"worker_id"=>$workerrow->id,
								"status"=>"processing",
								"updated_at"=>gettime()
							]);
						$taskid=$row2->id;
					}else{
						return error(12);
					}
				}else{
					$taskid=$row->id;
				}
				$row=DB::table("tasks")
					->where("id","=",$taskid)
					->select("*")->first();

				$tasktypeinputdata=[];
				$taskinputdata=[];
				$workerdata=null;

				$tasktyperow=DB::table("task_types")
					->where("id","=",$row->task_type_id)
					->select("*")->first();

				$tasktypeinputrow=DB::table("task_type_inputs")
					->where("task_type_id","=",$row->task_type_id)
					->select("*")->get();

				for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
					$tasktypeinputdata[]=[
						"name"=>$tasktypeinputrow[$j]->name,
						"type"=>$tasktypeinputrow[$j]->type
					];
				}

				$userrow=DB::table("users")
					->where("id","=",$row->user_id)
					->select("*")->first();

				if($row->worker_id!=NULL){
					$workertasktype=[];

					$workerrow=DB::table("workers")
						->where("id","=",$row->worker_id)
						->select("*")->first();
					$idled=false;
					$taskrow=DB::table("tasks")
						->where("worker_id","=",$workerrow->id)
						->where("status","=","processing")
						->select("*")->first();
					if($taskrow){
						$idled=true;
					}

					$workertasktyperow=DB::table("worker_task_types")
						->where("worker_id","=",$workerrow->id)
						->select("*")->get();

					for($i=0;$i<count($workertasktyperow);$i=$i+1){
						$workertasktyptasktypeerow=DB::table("task_types")
							->where("id","=",$workertasktyperow[$i]->task_type_id)
							->select("*")->first();
						$workertasktyptasktypeinputerow=DB::table("task_type_inputs")
							->where("task_type_id","=",$workertasktyperow[$i]->task_type_id)
							->select("*")->get();

						$workertasktypeinputdata=[];
						for($j=0;$j<count($workertasktyptasktypeinputerow);$j=$j+1){
							$workertasktypeinputdata[]=[
								"name"=>$workertasktyptasktypeinputerow[$j]->name,
								"type"=>$workertasktyptasktypeinputerow[$j]->type
							];
						}

						$workertasktype[]=[
							"id"=>$workertasktyptasktypeerow->id,
							"name"=>$workertasktyptasktypeerow->name,
							"inputs"=>$workertasktypeinputdata,
							"created_at"=>totime($workertasktyptasktypeerow->created_at)
						];
					}

					$workerdata=[
						"id"=>$workerrow->id,
						"name"=>$workerrow->name,
						"types"=>$workertasktype,
						"is_idled"=>$idled,
						"created_at"=>$workerrow->created_at
					];
				}

				$taskinputrow=DB::table("task_inputs")
					->where("task_id","=",$taskid)
					->select("*")->get();
				for($i=0;$i<count($taskinputrow);$i=$i+1){
					$taskinputdata[]=[
						"name"=>$taskinputrow[$i]->name,
						"type"=>$taskinputrow[$i]->type,
						"value"=>$taskinputrow[$i]->value
					];
				}
				return resp([
					"success"=>true,
					"data"=>[
						"id"=>$row->id,
						"type"=>[
							"id"=>$tasktyperow->id,
							"name"=>$tasktyperow->name,
							"inputs"=>$tasktypeinputdata,
							"created_at"=>$tasktyperow->created_at
						],
						"user"=>[
							"id"=>$userrow->id,
							"email"=>$userrow->email,
							"nickname"=>$userrow->nickname,
							"profile_image"=>updateurl($userrow->profile_image),
							"type"=>$userrow->type,
							"created_at"=>totime($userrow->created_at)
						],
						"worker"=>$workerdata,
						"taskinput"=>$taskinputdata,
						"status"=>$row->status,
						"result"=>NULL,
						"created_at"=>totime($row->created_at),
						"updated_at"=>totime($row->updated_at)
					]
				]);
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	Route::POST("/worker/task/{task_id}",function(Request $request,$taskid){
		$requestdata=Validator::make($request->all(),[
			"status"=>"required|string|in:finished,failed",
			"result"=>"required|mimes:jpg,png"
		],[
			"required"=>"5",
			"string"=>"6",
			"in"=>"6",
			"mimes"=>"7"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$header=$request->header("X-Authorization");
			if($header&&isset(explode(" ",$header)[1])){
				$workerrow=DB::table("workers")
					->where("asscess_token","=",explode(" ",$header)[1])
					->get("*")->first();
				if($workerrow){
					$row=DB::table("tasks")
						->where("id","=",$taskid)
						->where("worker_id","=",$workerrow->id)
						->select("*")->first();
					if($row){
						if(in_array($row->status,["pending","processing"])){
							$path=$requestdata["result"]->store("image");

							DB::table("tasks")
								->where("id","=",$taskid)
								->update([
									"status"=>$requestdata["status"],
									"result"=>$path,
									"updated_at"=>gettime()
								]);

							$row=DB::table("tasks")
								->where("id","=",$taskid)
								->where("worker_id","=",$workerrow->id)
								->select("*")->first();

							$tasktypeinputdata=[];
							$taskinputdata=[];
							$workerdata=null;

							$tasktyperow=DB::table("task_types")
								->where("id","=",$row->task_type_id)
								->select("*")->first();

							$tasktypeinputrow=DB::table("task_type_inputs")
								->where("task_type_id","=",$row->task_type_id)
								->select("*")->get();

							for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
								$tasktypeinputdata[]=[
									"name"=>$tasktypeinputrow[$j]->name,
									"type"=>$tasktypeinputrow[$j]->type
								];
							}

							$userrow=DB::table("users")
								->where("id","=",$row->user_id)
								->select("*")->first();

							if($row->worker_id!=NULL){
								$workertasktype=[];

								$workerrow=DB::table("workers")
									->where("id","=",$row->worker_id)
									->select("*")->first();
								$idled=false;
								$taskrow=DB::table("tasks")
									->where("worker_id","=",$workerrow->id)
									->where("status","=","processing")
									->select("*")->first();
								if($taskrow){
									$idled=true;
								}

								$workertasktyperow=DB::table("worker_task_types")
									->where("worker_id","=",$workerrow->id)
									->select("*")->get();

								for($i=0;$i<count($workertasktyperow);$i=$i+1){
									$workertasktyptasktypeerow=DB::table("task_types")
										->where("id","=",$workertasktyperow[$i]->task_type_id)
										->select("*")->first();
									$workertasktyptasktypeinputerow=DB::table("task_type_inputs")
										->where("task_type_id","=",$workertasktyperow[$i]->task_type_id)
										->select("*")->get();

									$workertasktypeinputdata=[];
									for($j=0;$j<count($workertasktyptasktypeinputerow);$j=$j+1){
										$workertasktypeinputdata[]=[
											"name"=>$workertasktyptasktypeinputerow[$j]->name,
											"type"=>$workertasktyptasktypeinputerow[$j]->type
										];
									}

									$workertasktype[]=[
										"id"=>$workertasktyptasktypeerow->id,
										"name"=>$workertasktyptasktypeerow->name,
										"inputs"=>$workertasktypeinputdata,
										"created_at"=>totime($workertasktyptasktypeerow->created_at)
									];
								}

								$workerdata=[
									"id"=>$workerrow->id,
									"name"=>$workerrow->name,
									"types"=>$workertasktype,
									"is_idled"=>$idled,
									"created_at"=>$workerrow->created_at
								];
							}

							$taskinputrow=DB::table("task_inputs")
								->where("task_id","=",$taskid)
								->select("*")->get();
							for($i=0;$i<count($taskinputrow);$i=$i+1){
								$taskinputdata[]=[
									"name"=>$taskinputrow[$i]->name,
									"type"=>$taskinputrow[$i]->type,
									"value"=>$taskinputrow[$i]->value
								];
							}
							return resp([
								"success"=>true,
								"data"=>[
									"id"=>$row->id,
									"type"=>[
										"id"=>$tasktyperow->id,
										"name"=>$tasktyperow->name,
										"inputs"=>$tasktypeinputdata,
										"created_at"=>$tasktyperow->created_at
									],
									"user"=>[
										"id"=>$userrow->id,
										"email"=>$userrow->email,
										"nickname"=>$userrow->nickname,
										"profile_image"=>updateurl($userrow->profile_image),
										"type"=>$userrow->type,
										"created_at"=>totime($userrow->created_at)
									],
									"worker"=>$workerdata,
									"taskinput"=>$taskinputdata,
									"status"=>$row->status,
									"result"=>updateurl($row->result),
									"created_at"=>totime($row->created_at),
									"updated_at"=>totime($row->updated_at)
								]
							]);
						}else{
							return error(18);
						}
					}else{
						return error(8);
					}
				}else{
					return error(3);
				}
			}else{
				return error(3);
			}
		}else{
			return error($requestdata->errors()->first());
		}
	});
?>