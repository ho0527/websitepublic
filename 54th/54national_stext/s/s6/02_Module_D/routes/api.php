<?php
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Route;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Validator; # validate

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

	function resp($data){
		return response()->json([
			"success"=>true,
			"data"=>$data
		],200);
	}

	function error($data){
		$arr=[
			1=>["MSG_INVALID_LOGIN",403],
			2=>["MSG_USER_EXISTS",409],
			3=>["MSG_INVALID_ACCESS_TOKEN",401],
			4=>["MSG_PERMISSION_DENY",403],
			5=>["MSG_MISSING_FIELD",400],
			6=>["MSG_WRONG_DATA_TYPE",400],
			7=>["MSG_IMAGE_CAN_NOT_PROCESS",400],
			8=>["MSG_TASK_NOT_EXISTS",404],
			9=>["MSG_TASKTYPE_INPUT_NAME_EXISTS",409],
			10=>["MSG_USER_QUOTA_IS_EMPTY",409],
			11=>["MSG_USER_NOT_EXISTS",404],
			12=>["MSG_NO_TASK_PENDING",404],
			13=>["MSG_TASKTYPE_NOT_EXISTS",404],
			14=>["MSG_WORKER_NOT_EXISTS",404],
			15=>["MSG_TASKTYPE_TYPE_ERROR",400],
			16=>["MSG_TASKTYPE_NAME_EXISTS",409],
			17=>["MSG_WORKER_NAME_EXISTS",409],
			18=>["MSG_TASK_END_OR_CANCEL",400]
		];
		return response()->json([
			"success"=>false,
			"message"=>$arr[$data][0]
		],$arr[$data][1]);
	}

	function nowtime(){
		date_default_timezone_set("Asia/Taipei");
		return date("Y-m-d H:i:s");
	}

	function formattime($data){
		return implode("T",explode(" ",$data));
	}

	#使⽤者登入
	Route::POST("user/login",function(Request $request){
		$requestdata=Validator::make($request->all(),[
			"email"=>"required|string|email",
			"password"=>"required|string"
		],[
			"required"=>5,
			"string"=>6,
			"email"=>6
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
					"id"=>$row->id,
					"email"=>$row->email,
					"nickname"=>$row->nickname,
					"profile_image"=>url($row->profile_image),
					"type"=>$row->type,
					"access_token"=>$row->access_token,
					"created_at"=>formattime($row->created_at)
				]);
			}else{
				return error(1);
			}
		}else{
			return error($requestdata->errors()->first());
		}
	});

	#使⽤者登出
	Route::POST("user/logout",function(Request $request){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				DB::table("users")
					->where("id","=",$tokenrow->id)
					->update([
						"access_token"=>""
					]);
				return resp("");
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	#使⽤者註冊
	Route::POST("user/register",function(Request $request){
		$requestdata=Validator::make($request->all(),[
			"email"=>"required|string|email",
			"password"=>"required|string",
			"nickname"=>"required|string",
			"profile_image"=>"required|mimes:jpg,png"
		],[
			"required"=>5,
			"string"=>6,
			"email"=>6,
			"profile_image"=>7
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$row=DB::table("users")
				->where("email","=",$requestdata["email"])
				->select("*")->first();
			if(!$row){
				$link="storage/app/".$requestdata["profile_image"]->store("/image");
				DB::table("users")->insert([
					"email"=>$requestdata["email"],
					"password_hash"=>Hash::make($requestdata["password"]),
					"nickname"=>$requestdata["nickname"],
					"profile_image"=>$link,
					"type"=>"USER"
				]);
				$row=DB::table("users")
					->where("email","=",$requestdata["email"])
					->select("*")->first();
				DB::table("user_quota_transactions")->insert([
					"user_id"=>$row->id,
					"value"=>10,
					"reason"=>"CREATE_USER"
				]);
				return resp([
					"id"=>$row->id,
					"email"=>$row->email,
					"nickname"=>$row->nickname,
					"profile_image"=>url($row->profile_image),
					"type"=>$row->type,
					"created_at"=>formattime($row->created_at)
				]);
			}else{
				return error(2);
			}
		}else{
			return error($requestdata->errors()->first());
		}
	});

	#取得任務類型
	Route::GET("task/type",function(Request $request){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				$requestdata=Validator::make($request->all(),[
					"order_by"=>"string|in:created_at",
					"order_type"=>"string|in:asc,desc",
					"page"=>"integer|min:1",
					"page_size"=>"integer|min:1"
				],[
					"string"=>6,
					"integer"=>6,
					"min"=>6,
					"in"=>6
				]);
		
				if(!$requestdata->fails()){
					$requestdata=$requestdata->validate();
					$row=DB::table("task_types")
						->where("deleted_at","=",NULL)
						->orderBy($requestdata["order_by"]??"created_at",$requestdata["order_type"]??"asc")
						->select("*")->get();

					$page=$requestdata["page"]??1;
					$pagesize=$requestdata["page_size"]??10;

					$data=[];
					for($i=($page-1)*$pagesize;$i<min($page*$pagesize,count($row));$i=$i+1){
						$tasktypeinputdata=[];
						$tasktypeinputrow=DB::table("task_type_inputs")
							->where("task_type_id","=",$row[$i]->id)
							->select("*")->get();
						for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
							$tasktypeinputdata[]=[
								"name"=>$tasktypeinputrow[$j]->name,
								"type"=>$tasktypeinputrow[$j]->type
							];
						}
						$data[]=[
							"id"=>$row[$i]->id,
							"name"=>$row[$i]->name,
							"inputs"=>$tasktypeinputdata,
							"created_at"=>formattime($row[$i]->created_at)
						];
					}
					return resp([
						"total_counts"=>count($row),
						"posts"=>$data
					]);
				}else{
					return error($requestdata->errors()->first());
				}
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	#新增任務類型
	Route::POST("task/type",function(Request $request){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				if($tokenrow->type=="ADMIN"){
					$requestdata=Validator::make($request->all(),[
						"name"=>"required|string",
						"inputs"=>"array"
					],[
						"required"=>5,
						"string"=>6,
						"array"=>6
					]);
			
					if(!$requestdata->fails()){
						$requestdata=$requestdata->validate();
						if(isset($requestdata["inputs"])){
							$row=DB::table("task_types")
								->where("name","=",$requestdata["name"])
								->where("deleted_at","=",NULL)
								->select("*")->first();
							if(!$row){
								$tasktypeinputlist=[];
								for($i=0;$i<count($requestdata["inputs"]);$i=$i+1){
									if(isset($requestdata["inputs"][$i]["name"])&&isset($requestdata["inputs"][$i]["type"])){
										if(in_array($requestdata["inputs"][$i]["type"],["string","number","boolean"])){
											if(!in_array($requestdata["inputs"][$i]["name"],$tasktypeinputlist)){
												$tasktypeinputlist[]=$requestdata["inputs"][$i]["name"];
											}else{
												return error(9);
											}
										}else{
											return error(6);
										}
									}else{
										return error(6);
									}
								}
								DB::table("task_types")->insert([
									"name"=>$requestdata["name"]
								]);
								$row=DB::table("task_types")
									->where("name","=",$requestdata["name"])
									->where("deleted_at","=",NULL)
									->select("*")->first();
								$tasktypeinputdata=[];
								for($i=0;$i<count($requestdata["inputs"]);$i=$i+1){
									DB::table("task_type_inputs")->insert([
										"task_type_id"=>$row->id,
										"name"=>$requestdata["inputs"][$i]["name"],
										"type"=>$requestdata["inputs"][$i]["type"]
									]);
									$tasktypeinputdata[]=[
										"name"=>$requestdata["inputs"][$i]["name"],
										"type"=>$requestdata["inputs"][$i]["type"]
									];
								}
								return resp([
									"id"=>$row->id,
									"name"=>$row->name,
									"inputs"=>$tasktypeinputdata,
									"created_at"=>formattime($row->created_at)
								]);
							}else{
								return error(16);
							}
						}else{
							return error(5);
						}
					}else{
						return error($requestdata->errors()->first());
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

	#新增任務
	Route::POST("task",function(Request $request){
		return "ERROR";
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				if($tokenrow->type=="USER"){
					$requestdata=Validator::make($request->all(),[
						"type"=>"required|integer",
						"inputs"=>"required"
					],[
						"required"=>5,
						"integer"=>6
					]);
			
					if(!$requestdata->fails()){
						$requestdata=$requestdata->validate();
						if(is_array($requestdata["inputs"])){
							$row=DB::table("tasks")
								->where("id","=",$requestdata["type"])
								->select("*")->get();
							if($row){
								$totalcountrow=DB::table("task_type_inputs")
									->where("id","=",$row->id)
									->select("*")->get();
								$totalcount=count($totalcountrow);
								$count=0;
								foreach($requestdata["inputs"] as $key=>$input){
									$tasktypeinputrow=DB::table("task_type_inputs")
										->where("id","=",$row->id)
										->where("name","=",$key)
										->select("*")->first();
									if(!$taskinputrow){
										return error(6);
									}
									$count=$count+1;
								}
								if($totalcount!=$count){
									return error(5);
								}

								DB::table("tasks")->insert([
									"task_type_id"=>$requestdata["type"],
									"user_id"=>$tokenrow->id,
									"status"=>"pending"
								]);
								return "";
		
		
								$userrow=DB::table("users")
									->where("id","=",$row->user_id)
									->select("*")->first();
								$tasktyperow=DB::table("task_types")
									->where("id","=",$row->task_type_id)
									->where("deleted_at","=",NULL)
									->select("*")->first();
								$workerrow=DB::table("workers")
									->where("id","=",$row->worker_id)
									->where("deleted_at","=",NULL)
									->select("*")->first();
								$taskinputrow=DB::table("task_inputs")
									->where("task_id","=",$row->id)
									->select("*")->get();
								$tasktypeinputdata=[];
								$taskinputdata=[];
								$workertasktypedata=[];
		
								for($i=0;$i<count($taskinputrow);$i=$i+1){
									$taskinputdata[]=[
										"name"=>$taskinputrow[$i]->name,
										"type"=>$taskinputrow[$i]->type,
										"value"=>$taskinputrow[$i]->value
									];
								}
		
								if($tasktyperow){
									$tasktypeinputrow=DB::table("task_type_inputs")
										->where("task_type_id","=",$row->id)
										->select("*")->get();
									for($i=0;$i<count($tasktypeinputrow);$i=$i+1){
										$tasktypeinputdata[]=[
											"name"=>$tasktypeinputrow[$i]->name,
											"type"=>$tasktypeinputrow[$i]->type
										];
									}
								}
								if($workerrow){
									$workertasktyperow=DB::table("worker_task_types")
										->where("worker_id","=",$row->worker_id)
										->select("*")->get();
									for($i=0;$i<count($workertasktyperow);$i=$i+1){
										$tasktyperow=DB::table("task_types")
											->where("id","=",$workertasktyperow[$i]->task_type_id)
											->where("deleted_at","=",NULL)
											->select("*")->first();
										if($tasktyperow){
											$tasktypeinputdata=[];
											$tasktypeinputrow=DB::table("task_type_inputs")
												->where("task_type_id","=",$tasktyperow->id)
												->select("*")->get();
											for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
												$tasktypeinputdata[]=[
													"name"=>$tasktypeinputrow[$j]->name,
													"type"=>$tasktypeinputrow[$j]->type
												];
											}
											$workertasktypedata[]=[
												"id"=>$tasktyperow->id,
												"name"=>$tasktyperow->name,
												"inputs"=>$tasktypeinputdata,
												"created_at"=>formattime($tasktyperow->created_at)
											];
										}
									}
									$taskrow=DB::table("tasks")
										->where("worker_id","=",$row->worker_id)
										->where("status","=","processing")
										->select("*")->first();
								}
								return resp([
									"id"=>$row->id,
									"type"=>$tasktyperow?[
										"id"=>$tasktyperow->id,
										"name"=>$tasktyperow->name,
										"inputs"=>$tasktypeinputdata,
										"created_at"=>formattime($tasktyperow->created_at)
									]:null,
									"user"=>[
										"id"=>$userrow->id,
										"email"=>$userrow->email,
										"nickname"=>$userrow->nickname,
										"profile_image"=>url($userrow->profile_image),
										"type"=>$userrow->type,
										"created_at"=>formattime($userrow->created_at)
									],
									"worker"=>$workerrow?[
										"id"=>$workerrow->id,
										"name"=>$workerrow->name,
										"types"=>$workertasktypedata,
										"is_idled"=>$taskrow?true:false,
										"created_at"=>formattime($workerrow->created_at)
									]:null,
									"taskinput"=>$taskinputdata,
									"status"=>$row->status,
									"result"=>$row->result?url($row->result):null,
									"created_at"=>formattime($row->created_at),
									"updated_at"=>formattime($row->updated_at)
								]);
							}else{
								return error(13);
							}
						}else{ 
							return error(6);
						}
					}else{
						return error($requestdata->errors()->first());
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

	#查詢任務列表
	Route::GET("task",function(Request $request){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				$requestdata=Validator::make($request->all(),[
					"order_by"=>"string|in:created_at,updated_at",
					"order_type"=>"string|in:asc,desc",
					"page"=>"integer|min:1",
					"page_size"=>"integer|min:1",
					"status"=>"string"
				],[
					"string"=>6,
					"integer"=>6,
					"min"=>6,
					"in"=>6
				]);
		
				if(!$requestdata->fails()){
					$requestdata=$requestdata->validate();
					$temprow=DB::table("tasks")
						->orderBy($requestdata["order_by"]??"created_at",$requestdata["order_type"]??"desc")
						->select("*")->get();

					$row=[];
					$page=$requestdata["page"]??1;
					$pagesize=$requestdata["page_size"]??10;
					$status=isset($requestdata["status"])?explode(",",$requestdata["status"]):"";

					for($i=0;$i<count($temprow);$i=$i+1){
						if($status==""||in_array($temprow[$i]->status,$status))
							$row[]=$temprow[$i];
					}

					$data=[];
					for($i=($page-1)*$pagesize;$i<min($page*$pagesize,count($row));$i=$i+1){
						$data[]=[
							"id"=>$row[$i]->id,
							"status"=>$row[$i]->status,
							"created_at"=>formattime($row[$i]->created_at),
							"updated_at"=>formattime($row[$i]->updated_at)
						];
					}
					return resp([
						"total_counts"=>count($row),
						"posts"=>$data
					]);
				}else{
					return error($requestdata->errors()->first());
				}
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	#取得指定任務資訊
	Route::GET("task/{task_id}",function(Request $request,$taskid){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				if($tokenrow->type=="USER"){
					$row=DB::table("tasks")
						->where("id","=",$taskid)
						->where("user_id","=",$tokenrow->id)
						->select("*")->first();
					if($row){
						$tasktyperow=DB::table("task_types")
							->where("id","=",$row->task_type_id)
							->where("deleted_at","=",NULL)
							->select("*")->first();
						$workerrow=DB::table("workers")
							->where("id","=",$row->worker_id)
							->where("deleted_at","=",NULL)
							->select("*")->first();
						$taskinputrow=DB::table("task_inputs")
							->where("task_id","=",$row->id)
							->select("*")->get();
						$tasktypeinputdata=[];
						$taskinputdata=[];
						$workertasktypedata=[];

						for($i=0;$i<count($taskinputrow);$i=$i+1){
							$taskinputdata[]=[
								"name"=>$taskinputrow[$i]->name,
								"type"=>$taskinputrow[$i]->type,
								"value"=>$taskinputrow[$i]->value
							];
						}

						if($tasktyperow){
							$tasktypeinputrow=DB::table("task_type_inputs")
								->where("task_type_id","=",$row->id)
								->select("*")->get();
							for($i=0;$i<count($tasktypeinputrow);$i=$i+1){
								$tasktypeinputdata[]=[
									"name"=>$tasktypeinputrow[$i]->name,
									"type"=>$tasktypeinputrow[$i]->type
								];
							}
						}
						if($workerrow){
							$workertasktyperow=DB::table("worker_task_types")
								->where("worker_id","=",$row->worker_id)
								->select("*")->get();
							for($i=0;$i<count($workertasktyperow);$i=$i+1){
								$tasktyperow=DB::table("task_types")
									->where("id","=",$workertasktyperow[$i]->task_type_id)
									->where("deleted_at","=",NULL)
									->select("*")->first();
								if($tasktyperow){
									$tasktypeinputdata=[];
									$tasktypeinputrow=DB::table("task_type_inputs")
										->where("task_type_id","=",$tasktyperow->id)
										->select("*")->get();
									for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
										$tasktypeinputdata[]=[
											"name"=>$tasktypeinputrow[$j]->name,
											"type"=>$tasktypeinputrow[$j]->type
										];
									}
									$workertasktypedata[]=[
										"id"=>$tasktyperow->id,
										"name"=>$tasktyperow->name,
										"inputs"=>$tasktypeinputdata,
										"created_at"=>formattime($tasktyperow->created_at)
									];
								}
							}
							$taskrow=DB::table("tasks")
								->where("worker_id","=",$row->worker_id)
								->where("status","=","processing")
								->select("*")->first();
						}
						return resp([
							"id"=>$row->id,
							"type"=>$tasktyperow?[
								"id"=>$tasktyperow->id,
								"name"=>$tasktyperow->name,
								"inputs"=>$tasktypeinputdata,
								"created_at"=>formattime($tasktyperow->created_at)
							]:null,
							"user"=>[
								"id"=>$tokenrow->id,
								"email"=>$tokenrow->email,
								"nickname"=>$tokenrow->nickname,
								"profile_image"=>url($tokenrow->profile_image),
								"type"=>$tokenrow->type,
								"created_at"=>formattime($tokenrow->created_at)
							],
							"worker"=>$workerrow?[
								"id"=>$workerrow->id,
								"name"=>$workerrow->name,
								"types"=>$workertasktypedata,
								"is_idled"=>$taskrow?true:false,
								"created_at"=>formattime($workerrow->created_at)
							]:null,
							"taskinput"=>$taskinputdata,
							"status"=>$row->status,
							"result"=>$row->result?url($row->result):null,
							"created_at"=>formattime($row->created_at),
							"updated_at"=>formattime($row->updated_at)
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

	#刪除任務類型
	Route::DELETE("task/type/{typetype_id}",function(Request $request,$tasktypeid){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				if($tokenrow->type=="ADMIN"){
					$row=DB::table("task_types")
						->where("id","=",$tasktypeid)
						->where("deleted_at","=",NULL)
						->select("*")->first();
					if($row){
						DB::table("task_types")
							->where("id","=",$tasktypeid)
							->update([
								"deleted_at"=>nowtime()
							]);
						return resp("");
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

	#新增使⽤者額度
	Route::POST("user/quota/{user_id}",function(Request $request,$userid){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				if($tokenrow->type=="ADMIN"){
					$requestdata=Validator::make($request->all(),[
						"value"=>"required|integer"
					],[
						"required"=>5,
						"integer"=>6
					]);
			
					if(!$requestdata->fails()){
						$requestdata=$requestdata->validate();
						$row=DB::table("users")
							->where("id","=",$userid)
							->select("*")->first();
						if($row){
							$total=(int)DB::table("user_quota_transactions")
								->where("user_id","=",$userid)
								->sum("value");
							if(0<=$total+$requestdata["value"]){
								DB::table("user_quota_transactions")->insert([
									"user_id"=>$userid,
									"value"=>$requestdata["value"],
									"reason"=>"RECHARGE"
								]);
								return resp("");
							}else{
								return error(10);
							}
						}else{
							return error(11);
						}
					}else{
						return error($requestdata->errors()->first());
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

	#新增worker
	Route::POST("worker",function(Request $request){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				if($tokenrow->type=="ADMIN"){
					$requestdata=Validator::make($request->all(),[
						"name"=>"required|string",
						"tasktypelist"=>"array"
					],[
						"required"=>5,
						"string"=>6,
						"array"=>6
					]);
			
					if(!$requestdata->fails()){
						$requestdata=$requestdata->validate();
						if(isset($requestdata["tasktypelist"])){
							$row=DB::table("workers")
								->where("name","=",$requestdata["name"])
								->where("deleted_at","=",NULL)
								->select("*")->first();
							if(!$row){
								$tasktypeinputlist=[];
								for($i=0;$i<count($requestdata["tasktypelist"]);$i=$i+1){
									$tasktyperow=DB::table("task_types")
										->where("id","=",$requestdata["tasktypelist"][$i])
										->where("deleted_at","=",NULL)
										->select("*")->first();
									if(!$tasktyperow){
										return error(13);
									}
								}
								DB::table("workers")->insert([
									"name"=>$requestdata["name"],
									"asscess_token"=>hash("sha256",$requestdata["name"])
								]);
								$row=DB::table("workers")
									->where("name","=",$requestdata["name"])
									->where("deleted_at","=",NULL)
									->select("*")->first();
								$tasktypeinputdata=[];
								$tasktypedata=[];
								for($i=0;$i<count($requestdata["tasktypelist"]);$i=$i+1){
									DB::table("worker_task_types")->insert([
										"worker_id"=>$row->id,
										"task_type_id"=>$requestdata["tasktypelist"][$i]
									]);
									$tasktyperow=DB::table("task_types")
										->where("id","=",$requestdata["tasktypelist"][$i])
										->select("*")->first();
									$tasktypeinputdata=[];
									$tasktypeinputrow=DB::table("task_type_inputs")
										->where("task_type_id","=",$tasktyperow->id)
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
										"created_at"=>formattime($tasktyperow->created_at)
									];
								}
								return resp([
									"id"=>$row->id,
									"name"=>$row->name,
									"types"=>$tasktypedata,
									"is_idled"=>false,
									"created_at"=>formattime($row->created_at)
								]);
							}else{
								return error(17);
							}
						}else{
							return error(5);
						}
					}else{
						return error($requestdata->errors()->first());
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

	#修改worker
	Route::PUT("worker/{worker_id}",function(Request $request,$workerid){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				if($tokenrow->type=="ADMIN"){
					$requestdata=Validator::make($request->all(),[
						"name"=>"string",
						"tasktypelist"=>"array"
					],[
						"string"=>6,
						"array"=>6
					]);
			
					if(!$requestdata->fails()){
						$requestdata=$requestdata->validate();
						$row=DB::table("workers")
							->where("id","=",$workerid)
							->where("deleted_at","=",NULL)
							->select("*")->first();
						if($row){
							if(isset($requestdata["tasktypelist"])){
								for($i=0;$i<count($requestdata["tasktypelist"]);$i=$i+1){
									$tasktyperow=DB::table("task_types")
										->where("id","=",$requestdata["tasktypelist"][$i])
										->where("deleted_at","=",NULL)
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
							if(isset($requestdata["name"])){
								$row=DB::table("workers")
									->where("id","!=",$workerid)
									->where("name","=",$requestdata["name"])
									->where("deleted_at","=",NULL)
									->select("*")->first();
								if(!$row){
									DB::table("workers")
										->where("id","=",$workerid)
										->update([
											"name"=>$requestdata["name"]
										]);
								}else{
									return error(17);
								}
							}
							$row=DB::table("workers")
								->where("id","=",$workerid)
								->select("*")->first();
							$tasktypedata=[];
							$workertasktyperow=DB::table("worker_task_types")
								->where("worker_id","=",$workerid)
								->select("*")->get();
							for($i=0;$i<count($workertasktyperow);$i=$i+1){
								$tasktyperow=DB::table("task_types")
									->where("id","=",$workertasktyperow[$i]->task_type_id)
									->where("deleted_at","=",NULL)
									->select("*")->first();
								if($tasktyperow){
									$tasktypeinputdata=[];
									$tasktypeinputrow=DB::table("task_type_inputs")
										->where("task_type_id","=",$tasktyperow->id)
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
										"created_at"=>formattime($tasktyperow->created_at)
									];
								}
							}
							$taskrow=DB::table("tasks")
								->where("worker_id","=",$workerid)
								->where("status","=","processing")
								->select("*")->first();
							return resp([
								"id"=>$row->id,
								"name"=>$row->name,
								"types"=>$tasktypedata,
								"is_idled"=>$taskrow?true:false,
								"created_at"=>formattime($row->created_at)
							]);
						}else{
							return error(14);
						}
					}else{
						return error($requestdata->errors()->first());
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

	#刪除worker
	Route::DELETE("worker/{worker_id}",function(Request $request,$workerid){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				if($tokenrow->type=="ADMIN"){
					$row=DB::table("workers")
						->where("id","=",$workerid)
						->where("deleted_at","=",NULL)
						->select("*")->first();
					if($row){
						DB::table("workers")
							->where("id","=",$workerid)
							->update([
								"deleted_at"=>nowtime()
							]);
						return resp("");
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

	#查詢使⽤者列表
	Route::GET("user",function(Request $request){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				if($tokenrow->type=="ADMIN"){
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
							"created_at"=>formattime($row[$i]->created_at)
						];
					}
					return resp([
						"total_counts"=>count($row),
						"posts"=>$data
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

	#修改使⽤者
	Route::PUT("user/{user_id}",function(Request $request,$userid){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				if($tokenrow->type=="ADMIN"){
					$requestdata=Validator::make($request->all(),[
						"nickname"=>"required|string"
					],[
						"required"=>5,
						"string"=>6
					]);
			
					if(!$requestdata->fails()){
						$requestdata=$requestdata->validate();
						$row=DB::table("users")
							->where("id","=",$userid)
							->select("*")->first();
						if($row){
							$total=DB::table("users")
								->where("id","=",$userid)
								->update([
									"nickname"=>$requestdata["nickname"]
								]);
							$row=DB::table("users")
								->where("id","=",$userid)
								->select("*")->first();
							return resp([
								"id"=>$row->id,
								"email"=>$row->email,
								"nickname"=>$row->nickname,
								"profile_image"=>url($row->profile_image),
								"type"=>$row->type,
								"created_at"=>formattime($row->created_at)
							]);
						}else{
							return error(11);
						}
					}else{
						return error($requestdata->errors()->first());
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

	#取消指定任務
	Route::POST("task/{task_id}/cancel",function(Request $request,$taskid){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				if($tokenrow->type=="USER"){
					$row=DB::table("tasks")
						->where("id","=",$taskid)
						->where("user_id","=",$tokenrow->id)
						->select("*")->first();
					if($row){
						if(in_array($row->status,["pending","processing"])){
							DB::table("tasks")
								->where("id","=",$taskid)
								->update([
									"status"=>"canceled"
								]);
							return resp("");
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

	#查詢剩餘額度
	Route::GET("user/leftquota",function(Request $request){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				$total=(int)DB::table("user_quota_transactions")
					->where("user_id","=",$tokenrow->id)
					->sum("value");
				return resp($total);
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	#查詢額度變更紀錄
	Route::GET("user/quota",function(Request $request){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				$row=DB::table("user_quota_transactions")
					->where("user_id","=",$tokenrow->id)
					->select("*")->get();
				$data=[];
				for($i=0;$i<count($row);$i=$i+1){
					$data[]=[
						"id"=>$row[$i]->id,
						"value"=>$row[$i]->value,
						"reason"=>$row[$i]->reason,
						"created_at"=>formattime($row[$i]->created_at)
					];
				}
				return resp([
					"total_counts"=>count($row),
					"quotas"=>$data
				]);
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	#取得需執⾏任務
	Route::GET("worker/task",function(Request $request){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("workers")
				->where("asscess_token","=",$token)
				->where("deleted_at","=",NULL)
				->select("*")->first();
			if($tokenrow){
				$row=DB::table("tasks")
					->where("worker_id","=",$tokenrow->id)
					->where("status","=","processing")
					->select("*")->first();
				if(!$row){
					$row=DB::table("tasks")
						->where("status","=","pending")
						->select("*")->first();
					if($row){
						DB::table("tasks")
							->where("id","=",$row->id)
							->update([
								"worker_id"=>$tokenrow->id,
								"status"=>"processing",
								"updated_at"=>nowtime()
							]);
						$row=DB::table("tasks")
							->where("id","=",$row->id)
							->select("*")->first();
					}else{
						return error(12);
					}
				}
				$userrow=DB::table("users")
					->where("id","=",$row->user_id)
					->select("*")->first();
				$tasktyperow=DB::table("task_types")
					->where("id","=",$row->task_type_id)
					->where("deleted_at","=",NULL)
					->select("*")->first();
				$workerrow=DB::table("workers")
					->where("id","=",$row->worker_id)
					->where("deleted_at","=",NULL)
					->select("*")->first();
				$taskinputrow=DB::table("task_inputs")
					->where("task_id","=",$row->id)
					->select("*")->get();
				$tasktypeinputdata=[];
				$taskinputdata=[];
				$workertasktypedata=[];

				for($i=0;$i<count($taskinputrow);$i=$i+1){
					$taskinputdata[]=[
						"name"=>$taskinputrow[$i]->name,
						"type"=>$taskinputrow[$i]->type,
						"value"=>$taskinputrow[$i]->value
					];
				}

				if($tasktyperow){
					$tasktypeinputrow=DB::table("task_type_inputs")
						->where("task_type_id","=",$row->id)
						->select("*")->get();
					for($i=0;$i<count($tasktypeinputrow);$i=$i+1){
						$tasktypeinputdata[]=[
							"name"=>$tasktypeinputrow[$i]->name,
							"type"=>$tasktypeinputrow[$i]->type
						];
					}
				}
				if($workerrow){
					$workertasktyperow=DB::table("worker_task_types")
						->where("worker_id","=",$row->worker_id)
						->select("*")->get();
					for($i=0;$i<count($workertasktyperow);$i=$i+1){
						$tasktyperow=DB::table("task_types")
							->where("id","=",$workertasktyperow[$i]->task_type_id)
							->where("deleted_at","=",NULL)
							->select("*")->first();
						if($tasktyperow){
							$tasktypeinputdata=[];
							$tasktypeinputrow=DB::table("task_type_inputs")
								->where("task_type_id","=",$tasktyperow->id)
								->select("*")->get();
							for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
								$tasktypeinputdata[]=[
									"name"=>$tasktypeinputrow[$j]->name,
									"type"=>$tasktypeinputrow[$j]->type
								];
							}
							$workertasktypedata[]=[
								"id"=>$tasktyperow->id,
								"name"=>$tasktyperow->name,
								"inputs"=>$tasktypeinputdata,
								"created_at"=>formattime($tasktyperow->created_at)
							];
						}
					}
					$taskrow=DB::table("tasks")
						->where("worker_id","=",$row->worker_id)
						->where("status","=","processing")
						->select("*")->first();
				}
				return resp([
					"id"=>$row->id,
					"type"=>$tasktyperow?[
						"id"=>$tasktyperow->id,
						"name"=>$tasktyperow->name,
						"inputs"=>$tasktypeinputdata,
						"created_at"=>formattime($tasktyperow->created_at)
					]:null,
					"user"=>[
						"id"=>$userrow->id,
						"email"=>$userrow->email,
						"nickname"=>$userrow->nickname,
						"profile_image"=>url($userrow->profile_image),
						"type"=>$userrow->type,
						"created_at"=>formattime($userrow->created_at)
					],
					"worker"=>$workerrow?[
						"id"=>$workerrow->id,
						"name"=>$workerrow->name,
						"types"=>$workertasktypedata,
						"is_idled"=>$taskrow?true:false,
						"created_at"=>formattime($workerrow->created_at)
					]:null,
					"taskinput"=>$taskinputdata,
					"status"=>$row->status,
					"result"=>$row->result?url($row->result):null,
					"created_at"=>formattime($row->created_at),
					"updated_at"=>formattime($row->updated_at)
				]);
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});

	#回報任務結果
	Route::POST("worker/task/{task_id}",function(Request $request,$taskid){
		if($request->header("X-Authorization")){
			$token=explode(" ",$request->header("X-Authorization"))[1];
			$tokenrow=DB::table("workers")
				->where("asscess_token","=",$token)
				->where("deleted_at","=",NULL)
				->select("*")->first();
			if($tokenrow){
				$requestdata=Validator::make($request->all(),[
					"status"=>"required|string|in:finished,failed",
					"result"=>"required|mimes:jpg,png"
				],[
					"required"=>5,
					"string"=>6,
					"in"=>6,
					"mimes"=>7
				]);
		
				if(!$requestdata->fails()){
					$requestdata=$requestdata->validate();
					$row=DB::table("tasks")
						->where("id","=",$taskid)
						->where("worker_id","=",$tokenrow->id)
						->select("*")->first();
					if($row){
						if($row->status=="processing"){
							$link="storage/app/".$requestdata["result"]->store("image");
							DB::table("tasks")
								->where("id","=",$taskid)
								->update([
									"status"=>$requestdata["status"],
									"result"=>$link,
									"updated_at"=>nowtime()
								]);
							$userrow=DB::table("users")
								->where("id","=",$row->user_id)
								->select("*")->first();
							$tasktyperow=DB::table("task_types")
								->where("id","=",$row->task_type_id)
								->where("deleted_at","=",NULL)
								->select("*")->first();
							$workerrow=DB::table("workers")
								->where("id","=",$row->worker_id)
								->where("deleted_at","=",NULL)
								->select("*")->first();
							$taskinputrow=DB::table("task_inputs")
								->where("task_id","=",$row->id)
								->select("*")->get();
							$tasktypeinputdata=[];
							$taskinputdata=[];
							$workertasktypedata=[];
	
							for($i=0;$i<count($taskinputrow);$i=$i+1){
								$taskinputdata[]=[
									"name"=>$taskinputrow[$i]->name,
									"type"=>$taskinputrow[$i]->type,
									"value"=>$taskinputrow[$i]->value
								];
							}
	
							if($tasktyperow){
								$tasktypeinputrow=DB::table("task_type_inputs")
									->where("task_type_id","=",$row->id)
									->select("*")->get();
								for($i=0;$i<count($tasktypeinputrow);$i=$i+1){
									$tasktypeinputdata[]=[
										"name"=>$tasktypeinputrow[$i]->name,
										"type"=>$tasktypeinputrow[$i]->type
									];
								}
							}
							if($workerrow){
								$workertasktyperow=DB::table("worker_task_types")
									->where("worker_id","=",$row->worker_id)
									->select("*")->get();
								for($i=0;$i<count($workertasktyperow);$i=$i+1){
									$tasktyperow=DB::table("task_types")
										->where("id","=",$workertasktyperow[$i]->task_type_id)
										->where("deleted_at","=",NULL)
										->select("*")->first();
									if($tasktyperow){
										$tasktypeinputdata=[];
										$tasktypeinputrow=DB::table("task_type_inputs")
											->where("task_type_id","=",$tasktyperow->id)
											->select("*")->get();
										for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
											$tasktypeinputdata[]=[
												"name"=>$tasktypeinputrow[$j]->name,
												"type"=>$tasktypeinputrow[$j]->type
											];
										}
										$workertasktypedata[]=[
											"id"=>$tasktyperow->id,
											"name"=>$tasktyperow->name,
											"inputs"=>$tasktypeinputdata,
											"created_at"=>formattime($tasktyperow->created_at)
										];
									}
								}
								$taskrow=DB::table("tasks")
									->where("worker_id","=",$row->worker_id)
									->where("status","=","processing")
									->select("*")->first();
							}
							return resp([
								"id"=>$row->id,
								"type"=>$tasktyperow?[
									"id"=>$tasktyperow->id,
									"name"=>$tasktyperow->name,
									"inputs"=>$tasktypeinputdata,
									"created_at"=>formattime($tasktyperow->created_at)
								]:null,
								"user"=>[
									"id"=>$userrow->id,
									"email"=>$userrow->email,
									"nickname"=>$userrow->nickname,
									"profile_image"=>url($userrow->profile_image),
									"type"=>$userrow->type,
									"created_at"=>formattime($userrow->created_at)
								],
								"worker"=>$workerrow?[
									"id"=>$workerrow->id,
									"name"=>$workerrow->name,
									"types"=>$workertasktypedata,
									"is_idled"=>$taskrow?true:false,
									"created_at"=>formattime($workerrow->created_at)
								]:null,
								"taskinput"=>$taskinputdata,
								"status"=>$row->status,
								"result"=>$row->result?url($row->result):null,
								"created_at"=>formattime($row->created_at),
								"updated_at"=>formattime($row->updated_at)
							]);
						}else{ 
							return error(18);
						}
					}else{ 
						return error(8);
					}
				}else{
					return error($requestdata->errors()->first());
				}
			}else{
				return error(3);
			}
		}else{
			return error(3);
		}
	});
?>