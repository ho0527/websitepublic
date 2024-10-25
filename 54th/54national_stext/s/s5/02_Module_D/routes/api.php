<?php
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Route;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Validator; // validate

	function resp($data){
		return response()->json([
			"success"=>true,
			"data"=>$data
		],200);
	}

	function nowtime(){
		date_default_timezone_set("Asia/Taipei");
		return date("Y-m-d H:i:s");
	}

	function formattime($data){
		return implode("T",explode(" ",$data));
	}

	function returnerror($key){
		$arr=[
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

		return response()->json([
			"success"=>false,
			"message"=>$arr[$key][0]
		],$arr[$key][1]);
	}

	Route::POST("user/login",function(Request $request){
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
			$userrow=DB::table("users")
				->where("email","=",$requestdata["email"])
				->select("*")->first();
			if($userrow&&Hash::check($requestdata["password"],$userrow->password_hash)){
				DB::table("users")
					->where("email","=",$requestdata["email"])
					->update([
						"access_token"=>hash("sha256",$requestdata["email"])
					]);
				$userrow=DB::table("users")
					->where("email","=",$requestdata["email"])
					->select("*")->first();
				return resp([
					"id"=>$userrow->id,
					"email"=>$userrow->email,
					"nickname"=>$userrow->nickname,
					"profile_image"=>url($userrow->profile_image),
					"type"=>$userrow->type,
					"access_token"=>$userrow->access_token,
					"created_at"=>formattime($userrow->created_at)
				]);
			}else{
				return returnerror("1");
			}
		}else{
			return returnerror($requestdata->errors()->first());
		}
	});

	Route::POST("user/logout",function(Request $request){
		$header=$request->header("X-Authorization");
		if($header){
			$token=explode(" ",$header)[1];
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
				return returnerror("3");
			}
		}else{
			return returnerror("3");
		}
	});

	Route::POST("user/register",function(Request $request){
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
			$userrow=DB::table("users")
				->where("email","=",$requestdata["email"])
				->select("*")->first();
			if(!$userrow){
				$path="/storage/app/".$requestdata["profile_image"]->store("image");
				DB::table("users")->insert([
					"email"=>$requestdata["email"],
					"password_hash"=>Hash::make($requestdata["password"]),
					"nickname"=>$requestdata["nickname"],
					"profile_image"=>$path,
					"type"=>"USER"
				]);
				$userrow=DB::table("users")
					->where("email","=",$requestdata["email"])
					->select("*")->first();
				DB::table("user_quota_transactions")->insert([
					"user_id"=>$userrow->id,
					"value"=>10,
					"reason"=>"CREATE_USER"
				]);
				return resp([
					"id"=>$userrow->id,
					"email"=>$userrow->email,
					"nickname"=>$userrow->nickname,
					"profile_image"=>url($userrow->profile_image),
					"type"=>$userrow->type,
					"created_at"=>formattime($userrow->created_at)
				]);
			}else{
				return returnerror("2");
			}
		}else{
			return returnerror($requestdata->errors()->first());
		}
	});

	Route::GET("task/type",function(Request $request){
		$requestdata=Validator::make($request->all(),[
			"order_by"=>"string|in:created_at",
			"order_type"=>"string|in:asc,desc",
			"page"=>"integer",
			"page_size"=>"integer"
		],[
			"string"=>"6",
			"in"=>"6",
			"integer"=>"6"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$header=$request->header("X-Authorization");
			if($header){
				$token=explode(" ",$header)[1];
				$tokenrow=DB::table("users")
					->where("access_token","=",$token)
					->select("*")->first();
				if($tokenrow){
					$orderby=$requestdata["order_by"]??"created_at";
					$ordertype=$requestdata["order_type"]??"asc";
					$page=$requestdata["page"]??1;
					$pagesize=$requestdata["page_size"]??10;

					$row=DB::table("task_types")
						->orderBy($orderby,$ordertype)
						->where("deleted_at","=",NULL)
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
								"type"=>$tasktypeinputrow[$j]->type,
							];
						}

						$data[]=[
							"id"=>$row[$i]->id,
							"name"=>$row[$i]->name,
							"inputs"=>$inputdata,
							"created_at"=>formattime($row[$i]->created_at)
						];
					}
					return resp([
						"total_count"=>count($row),
						"posts"=>$data
					]);
				}else{
					return returnerror("3");
				}
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror($requestdata->errors()->first());
		}
	});

	Route::POST("task/type",function(Request $request){
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
			if($header){
				$token=explode(" ",$header)[1];
				$tokenrow=DB::table("users")
					->where("access_token","=",$token)
					->select("*")->first();
				if($tokenrow){
					if($tokenrow->type=="ADMIN"){
						if(isset($requestdata["inputs"])){
							$row=DB::table("task_types")
								->where("name","=",$requestdata["name"])
								->where("deleted_at","=",NULL)
								->select("*")->first();
							if(!$row){
								$inputlist=[];
								for($i=0;$i<count($requestdata["inputs"]);$i=$i+1){
									if(in_array($requestdata["inputs"][$i]["name"],$inputlist)){
										return returnerror("9");
									}else if(!in_array($requestdata["inputs"][$i]["type"],["string","number","boolean"])){
										return returnerror("6");
									}
									$inputlist[]=$requestdata["inputs"][$i]["name"];
								}
	
								DB::table("task_types")->insert([
									"name"=>$requestdata["name"]
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
									"id"=>$row->id,
									"name"=>$row->name,
									"inputs"=>$inputdata,
									"created_at"=>formattime($row->created_at)
								]);
							}else{
								return returnerror("16");
							}
						}else{
							return returnerror("5");
						}
					}else{
						return returnerror("4");
					}
				}else{
					return returnerror("3");
				}
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror($requestdata->errors()->first());
		}
	});

	Route::POST("task",function(Request $request){});

	Route::GET("task",function(Request $request){
		$requestdata=Validator::make($request->all(),[
			"order_by"=>"string|in:created_at,updated_at",
			"order_type"=>"string|in:asc,desc",
			"page"=>"integer",
			"page_size"=>"integer",
			"status"=>"string"
		],[
			"string"=>"6",
			"in"=>"6",
			"integer"=>"6"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$header=$request->header("X-Authorization");
			if($header){
				$token=explode(" ",$header)[1];
				$tokenrow=DB::table("users")
					->where("access_token","=",$token)
					->select("*")->first();
				if($tokenrow){
					$orderby=$requestdata["order_by"]??"created_at";
					$ordertype=$requestdata["order_type"]??"asc";
					$page=$requestdata["page"]??1;
					$pagesize=$requestdata["page_size"]??10;
					$status=$requestdata["status"]??"";

					$temprow=DB::table("tasks")
						->orderBy($orderby,$ordertype)
						->select("*")->get();

					$row=[];

					for($i=0;$i<count($temprow);$i=$i+1){
						if($status==""||in_array($temprow[$i]->status,explode(",",$status))){
							$row[]=$temprow[$i];
						}
					}
					
					$data=[];

					for($i=($page-1)*$pagesize;$i<min(($page)*$pagesize,count($row));$i=$i+1){
						$data[]=[
							"id"=>$row[$i]->id,
							"status"=>$row[$i]->status,
							"updated_at"=>formattime($row[$i]->updated_at),
							"created_at"=>formattime($row[$i]->created_at)
						];
					}
					return resp([
						"total_count"=>count($row),
						"posts"=>$data
					]);
				}else{
					return returnerror("3");
				}
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror($requestdata->errors()->first());
		}
	});

	Route::GET("task/{task_id}",function(Request $request,$taskid){
		$header=$request->header("X-Authorization");
		if($header){
			$token=explode(" ",$header)[1];
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
						$userrow=DB::table("users")
							->where("id","=",$row->user_id)
							->select("*")->first();
						$tasktyperow=DB::table("task_types")
							->where("id","=",$row->task_type_id)
							->where("deleted_at","=",NULL)
							->select("*")->first();
						$taskinputrow=DB::table("task_inputs")
							->where("task_id","=",$row->id)
							->select("*")->get();
						$workerrow=DB::table("workers")
							->where("id","=",$row->worker_id)
							->where("deleted_at","=",NULL)
							->select("*")->first();
						$taskinput=[];
						$tasktypelist=[];
						$tasktypeinput=[];
						$tasktypeinputrow=DB::table("task_type_inputs")
							->where("task_type_id","=",$tasktyperow->id)
							->select("*")->get();
						
						for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
							$tasktypeinput[]=[
								"name"=>$tasktypeinputrow[$j]->name,
								"type"=>$tasktypeinputrow[$j]->type
							];
						}
						
						for($j=0;$j<count($taskinputrow);$j=$j+1){
							$taskinput[]=[
								"name"=>$taskinputrow[$j]->name,
								"type"=>$taskinputrow[$j]->type,
								"value"=>$taskinputrow[$j]->value
							];
						}
						$workertasktype=DB::table("worker_task_types")
							->where("id","=",$row->worker_id)
							->select("*")->get();
						for($i=0;$i<count($workertasktype);$i=$i+1){
							$tasktypelist[]=$workertasktype[$i]->task_type_id;
						}
						$workertasktypedata=[];
						for($i=0;$i<count($tasktypelist);$i=$i+1){
							$inputdata=[];
							$workertasktyperow=DB::table("task_types")
								->where("id","=",$tasktypelist[$i])
								->where("deleted_at","=",NULL)
								->select("*")->first();
							if($workertasktyperow){
								$tasktypeinputrow=DB::table("task_type_inputs")
									->where("task_type_id","=",$workertasktyperow->id)
									->select("*")->get();
								
								for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
									$inputdata[]=[
										"name"=>$tasktypeinputrow[$j]->name,
										"type"=>$tasktypeinputrow[$j]->type,
									];
								}
		
								$workertasktypedata[]=[
									"id"=>$workertasktyperow->id,
									"name"=>$workertasktyperow->name,
									"inputs"=>$inputdata,
									"created_at"=>formattime($workertasktyperow->created_at)
								];
							}
						}
						$isidledrow=DB::table("tasks")
							->where("worker_id","=",$row->worker_id)
							->where("status","=","processing")
							->select("*")->first();
						if($isidledrow)
							$isidled=true;
						else
							$isidled=false;
						return resp([
							"id"=>$row->id,
							"type"=>[
								"id"=>$tasktyperow->id,
								"name"=>$tasktyperow->name,
								"inputs"=>$tasktypeinput,
								"created_at"=>formattime($tasktyperow->created_at)
							],
							"user"=>[
								"id"=>$userrow->id,
								"email"=>$userrow->email,
								"nickname"=>$userrow->nickname,
								"profile_image"=>url($userrow->profile_image),
								"type"=>$userrow->type,
								"created_at"=>formattime($userrow->created_at)
							],
							"worker"=>[
								"id"=>$workerrow->id,
								"name"=>$workerrow->name,
								"types"=>$workertasktypedata,
								"is_idled"=>$isidled,
								"created_at"=>formattime($workerrow->created_at)
							],
							"taskinput"=>$taskinput,
							"status"=>$row->status,
							"result"=>$row->result,
							"created_at"=>formattime($row->created_at),
							"updated_at"=>formattime($row->updated_at)
						]);
					}else{
						return returnerror("8");
					}
				}else{
					return returnerror("4");
				}
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror("3");
		}
	});

	Route::DELETE("task/type/{typetype_id}",function(Request $request,$tasktypeid){
		$header=$request->header("X-Authorization");
		if($header){
			$token=explode(" ",$header)[1];
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
						$data=[];
						DB::table("task_types")
							->where("id","=",$tasktypeid)
							->update([
								"deleted_at"=>nowtime()
							]);

						return resp("");
					}else{
						return returnerror("13");
					}
				}else{
					return returnerror("4");
				}
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror("3");
		}
	});

	Route::POST("user/quota/{user_id}",function(Request $request,$userid){
		$requestdata=Validator::make($request->all(),[
			"value"=>"required|integer"
		],[
			"required"=>"5",
			"integer"=>"6"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$header=$request->header("X-Authorization");
			if($header){
				$token=explode(" ",$header)[1];
				$tokenrow=DB::table("users")
					->where("access_token","=",$token)
					->select("*")->first();
				if($tokenrow){
					if($tokenrow->type=="ADMIN"){
						$row=DB::table("users")
							->where("id","=",$userid)
							->select("*")->first();
						if($row){
							$oldtotalcount=DB::table("user_quota_transactions")
								->where("user_id","=",$userid)
								->sum("value");
							if(0<=$oldtotalcount+$requestdata["value"]){
								DB::table("user_quota_transactions")->insert([
									"user_id"=>$userid,
									"value"=>$requestdata["value"],
									"reason"=>"RECHARGE"
								]);

								return resp("");
							}else{
								return returnerror("10");
							}
						}else{
							return returnerror("11");
						}
					}else{
						return returnerror("4");
					}
				}else{
					return returnerror("3");
				}
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror($requestdata->errors()->first());
		}
	});

	Route::POST("worker",function(Request $request){
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
			if($header){
				$token=explode(" ",$header)[1];
				$tokenrow=DB::table("users")
					->where("access_token","=",$token)
					->select("*")->first();
				if($tokenrow){
					if($tokenrow->type=="ADMIN"){
						if(isset($requestdata["tasktypelist"])){
							if(is_array($requestdata["tasktypelist"])){
								$row=DB::table("workers")
									->where("name","=",$requestdata["name"])
									->where("deleted_at","=",NULL)
									->select("*")->first();
								if(!$row){
									for($i=0;$i<count($requestdata["tasktypelist"]);$i=$i+1){
										$tasktyperow=DB::table("task_types")
											->where("id","=",$requestdata["tasktypelist"][$i])
											->where("deleted_at","=",NULL)
											->select("*")->first();
										if(!$tasktyperow){
											return returnerror("13");
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
		
									$data=[];
		
									for($i=0;$i<count($requestdata["tasktypelist"]);$i=$i+1){
										$inputdata=[];
										$tasktyperow=DB::table("task_types")
											->where("id","=",$requestdata["tasktypelist"][$i])
											->where("deleted_at","=",NULL)
											->select("*")->first();
				
										$tasktypeinputrow=DB::table("task_type_inputs")
											->where("task_type_id","=",$tasktyperow->id)
											->select("*")->get();
										
										for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
											$inputdata[]=[
												"name"=>$tasktypeinputrow[$j]->name,
												"type"=>$tasktypeinputrow[$j]->type,
											];
										}
				
										$data[]=[
											"id"=>$tasktyperow->id,
											"name"=>$tasktyperow->name,
											"inputs"=>$inputdata,
											"created_at"=>formattime($tasktyperow->created_at)
										];
									}
		
									for($i=0;$i<count($requestdata["tasktypelist"]);$i=$i+1){
										DB::table("worker_task_types")->insert([
											"worker_id"=>$row->id,
											"task_type_id"=>$requestdata["tasktypelist"][$i]
										]);
									}
		
									return resp([
										"id"=>$row->id,
										"name"=>$row->name,
										"types"=>$data,
										"is_idled"=>false,
										"created_at"=>formattime($row->created_at)
									]);
								}else{
									return returnerror("17");
								}
							}else{
								return returnerror("6");
							}
						}else{
							return returnerror("5");
						}
					}else{
						return returnerror("4");
					}
				}else{
					return returnerror("3");
				}
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror($requestdata->errors()->first());
		}
	});

	Route::PUT("worker/{worker_id}",function(Request $request){});

	Route::DELETE("worker/{worker_id}",function(Request $request,$workerid){
		$header=$request->header("X-Authorization");
		if($header){
			$token=explode(" ",$header)[1];
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
						$data=[];
						DB::table("workers")
							->where("id","=",$workerid)
							->update([
								"deleted_at"=>nowtime()
							]);

						return resp("");
					}else{
						return returnerror("14");
					}
				}else{
					return returnerror("4");
				}
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror("3");
		}
	});

	Route::GET("user",function(Request $request){
		$header=$request->header("X-Authorization");
		if($header){
			$token=explode(" ",$header)[1];
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
						"total_count"=>count($row),
						"users"=>$data
					]);
				}else{
					return returnerror("4");
				}
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror("3");
		}
	});

	Route::PUT("user/{user_id}",function(Request $request,$userid){
		$requestdata=Validator::make($request->all(),[
			"nickname"=>"required|string"
		],[
			"required"=>"5",
			"string"=>"6"
		]);

		if(!$requestdata->fails()){
			$requestdata=$requestdata->validate();
			$header=$request->header("X-Authorization");
			if($header){
				$token=explode(" ",$header)[1];
				$tokenrow=DB::table("users")
					->where("access_token","=",$token)
					->select("*")->first();
				if($tokenrow){
					if($tokenrow->type=="ADMIN"){
						$row=DB::table("users")
							->where("id","=",$userid)
							->select("*")->first();
						if($row){
							DB::table("users")
								->where("id","=",$userid)
								->update([
									"nickname"=>$requestdata["nickname"]
								]);
							$userrow=DB::table("users")
								->where("id","=",$userid)
								->select("*")->first();
							return resp([
								"id"=>$userrow->id,
								"email"=>$userrow->email,
								"nickname"=>$userrow->nickname,
								"profile_image"=>url($userrow->profile_image),
								"type"=>$userrow->type,
								"created_at"=>formattime($userrow->created_at)
							]);
						}else{
							return returnerror("11");
						}
					}else{
						return returnerror("4");
					}
				}else{
					return returnerror("3");
				}
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror($requestdata->errors()->first());
		}
	});

	Route::POST("task/{task_id}/cancel",function(Request $request,$taskid){
		$header=$request->header("X-Authorization");
		if($header){
			$token=explode(" ",$header)[1];
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
						if($row->status=="pending"||$row->status=="processing"){
							DB::table("tasks")
								->where("id","=",$taskid)
								->update([
									"status"=>"canceled",
									"updated_at"=>nowtime()
								]);
	
							return resp("");
						}else{
							return returnerror("18");
						}
					}else{
						return returnerror("8");
					}
				}else{
					return returnerror("4");
				}
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror("3");
		}
	});

	Route::GET("user/leftquota",function(Request $request){
		$header=$request->header("X-Authorization");
		if($header){
			$token=explode(" ",$header)[1];
			$tokenrow=DB::table("users")
				->where("access_token","=",$token)
				->select("*")->first();
			if($tokenrow){
				$count=DB::table("user_quota_transactions")
					->where("user_id","=",$tokenrow->id)
					->sum("value");
				return resp((int)$count);
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror("3");
		}
	});

	Route::GET("user/quota",function(Request $request){
		$header=$request->header("X-Authorization");
		if($header){
			$token=explode(" ",$header)[1];
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
					"total_count"=>count($row),
					"quotas"=>$data
				]);
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror("3");
		}
	});

	Route::GET("worker/task",function(Request $request){});

	Route::POST("worker/task/{task_id}",function(Request $request,$taskid){
		$requestdata=Validator::make($request->all(),[
			"status"=>"required|string|in:finished,finshed",
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
			if($header){
				$token=explode(" ",$header)[1];
				$tokenrow=DB::table("workers")
					->where("asscess_token","=",$token)
					->select("*")->first();
				if($tokenrow){
					$row=DB::table("tasks")
						->where("id","=",$taskid)
						->where("worker_id","=",$tokenrow->id)
						->select("*")->first();
					if($row){
						if($row->status=="processing"){
							$path="/storage/app/".$requestdata["result"]->store("image");
							DB::table("tasks")
								->where("id","=",$taskid)
								->update([
									"status"=>$requestdata["status"],
									"result"=>$path,
									"updated_at"=>nowtime()
								]);

							$row=DB::table("tasks")
								->where("id","=",$taskid)
								->where("worker_id","=",$tokenrow->id)
								->select("*")->first();

							$userrow=DB::table("users")
								->where("id","=",$row->user_id)
								->select("*")->first();
							$tasktyperow=DB::table("task_types")
								->where("id","=",$row->task_type_id)
								->where("deleted_at","=",NULL)
								->select("*")->first();
							$taskinputrow=DB::table("task_inputs")
								->where("task_id","=",$row->id)
								->select("*")->get();
							$workerrow=DB::table("workers")
								->where("id","=",$row->worker_id)
								->where("deleted_at","=",NULL)
								->select("*")->first();
							$taskinput=[];
							$tasktypelist=[];
							$tasktypeinput=[];
							$tasktypeinputrow=[];
							if($tasktyperow){
								$tasktypeinputrow=DB::table("task_type_inputs")
									->where("task_type_id","=",$tasktyperow->id)
									->select("*")->get();
								
								for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
									$tasktypeinput[]=[
										"name"=>$tasktypeinputrow[$j]->name,
										"type"=>$tasktypeinputrow[$j]->type
									];
								}
							}
							
							for($j=0;$j<count($taskinputrow);$j=$j+1){
								$taskinput[]=[
									"name"=>$taskinputrow[$j]->name,
									"type"=>$taskinputrow[$j]->type,
									"value"=>$taskinputrow[$j]->value
								];
							}
							$workertasktype=DB::table("worker_task_types")
								->where("id","=",$row->worker_id)
								->select("*")->get();
							for($i=0;$i<count($workertasktype);$i=$i+1){
								$tasktypelist[]=$workertasktype[$i]->task_type_id;
							}
							$workertasktypedata=[];
							for($i=0;$i<count($tasktypelist);$i=$i+1){
								$inputdata=[];
								$workertasktyperow=DB::table("task_types")
									->where("id","=",$tasktypelist[$i])
									->where("deleted_at","=",NULL)
									->select("*")->first();
								if($workertasktyperow){
									$tasktypeinputrow=DB::table("task_type_inputs")
										->where("task_type_id","=",$workertasktyperow->id)
										->select("*")->get();
									
									for($j=0;$j<count($tasktypeinputrow);$j=$j+1){
										$inputdata[]=[
											"name"=>$tasktypeinputrow[$j]->name,
											"type"=>$tasktypeinputrow[$j]->type,
										];
									}
			
									$workertasktypedata[]=[
										"id"=>$workertasktyperow->id,
										"name"=>$workertasktyperow->name,
										"inputs"=>$inputdata,
										"created_at"=>formattime($workertasktyperow->created_at)
									];
								}
							}
							$isidledrow=DB::table("tasks")
								->where("worker_id","=",$row->worker_id)
								->where("status","=","processing")
								->select("*")->first();
							if($isidledrow)
								$isidled=true;
							else
								$isidled=false;
							return resp([
								"id"=>$row->id,
								"type"=>[
									"id"=>$tasktyperow->id,
									"name"=>$tasktyperow->name,
									"inputs"=>$tasktypeinput,
									"created_at"=>formattime($tasktyperow->created_at)
								],
								"user"=>[
									"id"=>$userrow->id,
									"email"=>$userrow->email,
									"nickname"=>$userrow->nickname,
									"profile_image"=>url($userrow->profile_image),
									"type"=>$userrow->type,
									"created_at"=>formattime($userrow->created_at)
								],
								"worker"=>[
									"id"=>$workerrow->id,
									"name"=>$workerrow->name,
									"types"=>$workertasktypedata,
									"is_idled"=>$isidled,
									"created_at"=>formattime($workerrow->created_at)
								],
								"taskinput"=>$taskinput,
								"status"=>$row->status,
								"result"=>$row->result,
								"created_at"=>formattime($row->created_at),
								"updated_at"=>formattime($row->updated_at)
							]);
						}else{
							return returnerror("18");
						}
					}else{
						return returnerror("8");
					}
				}else{
					return returnerror("3");
				}
			}else{
				return returnerror("3");
			}
		}else{
			return returnerror($requestdata->errors()->first());
		}
	});

?>