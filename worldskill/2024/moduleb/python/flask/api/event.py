# import
import json
from apiflask import APIBlueprint
from apiflask.fields import Integer,String
from flask import Blueprint
from flask import Flask,request,jsonify

from function.sql import *
from function.thing import *
from .initialize import *
from .function import *

CASE00035_EVENT_BLUEPRINT=APIBlueprint("event",__name__)

try:
	@CASE00035_EVENT_BLUEPRINT.get("/geteventlist")
	def geteventlist():
		header=request.headers.get("Authorization")
		if header:
			token=chash(header.split(" ")[1],"decode")
			if token!=None:
				tokenrow=query(SETTING["dbname"],"SELECT*FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])
				if tokenrow:
					userid=tokenrow[0]["userid"]
					data=query(SETTING["dbname"],"SELECT*FROM `event` WHERE `userid`=%s AND `deletetime` IS NULL ORDER BY `id` DESC",[userid],SETTING["dbsetting"])

					return jsonify({
						"success": True,
						"data": data
					}),200
				else:
					return jsonify({
						"success": False,
						"data": "ERROR_token_error"
					}),401
			else:
				return jsonify({
					"success": False,
					"data": "ERROR_token_decode_error"
				}),400
		else:
			return jsonify({
				"success": False,
				"data": "ERROR_token_not_found"
			}),401

	@CASE00035_EVENT_BLUEPRINT.get("/getevent/<int:id>")
	def getevent(id):
		header=request.headers.get("Authorization")
		if header:
			token=chash(header.split(" ")[1],"decode")
			if token!=None:
				tokenrow=query(SETTING["dbname"],"SELECT*FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])
				if tokenrow:
					userid=tokenrow[0]["userid"]
					row=query(SETTING["dbname"],"SELECT*FROM `event` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
					if row:
						if userid==row[0]["userid"]:
							data=query(SETTING["dbname"],"SELECT*FROM `event` WHERE `id`=%s AND `deletetime` IS NULL ORDER BY `updatetime` DESC,`id` DESC",[id],SETTING["dbsetting"])

							return jsonify({
								"success": True,
								"data": data[0]
							}),200
						else:
							return jsonify({
								"success": False,
								"data": "ERROR_no_permission"
							}),403
					else:
						return jsonify({
							"success": False,
							"data": "ERROR_event_not_found"
						}),404
				else:
					return jsonify({
						"success": False,
						"data": "ERROR_token_error"
					}),401
			else:
				return jsonify({
					"success": False,
					"data": "ERROR_token_decode_error"
				}),400
		else:
			return jsonify({
				"success": False,
				"data": "ERROR_token_not_found"
			}),401

	@CASE00035_EVENT_BLUEPRINT.post("/newevent")
	def newevent():
		header=request.headers.get("Authorization")
		if header:
			token=chash(header.split(" ")[1],"decode")
			if token!=None:
				tokenrow=query(SETTING["dbname"],"SELECT*FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])
				if tokenrow:
					userid=tokenrow[0]["userid"]
					# userrow=query(SETTING["dbname"],"SELECT*FROM `user` WHERE `id`=%s",[userid],SETTING["dbsetting"])[0]
					chashdata=chash(str(request.data)[2:-1],"decode")
					if chashdata!=None:
						data=json.loads(chashdata)
						print(data)
						customerid=data["customerid"]
						cropname=data["cropname"]
						droneno=data["droneno"]
						sprayarea=data["sprayarea"]
						oemed=data["oemed"]
						usebindingmaterial=data["usebindingmaterial"]
						barcode=data["barcode"]
						cert=data["cert"]
						kml=data["kml"]
						# phone=userrow["phone"]
						# planno=userrow["planno"]
						# groupno=userrow["groupno"]
						# groupname=userrow["groupname"]
						# idcard=userrow["idcard"]
						# checkcode=userrow["checkcode"]

						if customerid and droneno and cropname and sprayarea and oemed and cert and kml and (usebindingmaterial in [True,False]) and barcode:
							query(SETTING["dbname"],"INSERT INTO `event`(`userid`,`customerid`,`droneno`,`implementdate`,`cropname`,`sprayarea`,`oemed`,`cert`,`kml`,`usebindingmaterial`,`barcode`,`createtime`)VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",[userid,customerid,droneno,date(),cropname,sprayarea,oemed,cert,kml,usebindingmaterial,barcode,time()],SETTING["dbsetting"])
							return jsonify({
								"success": True,
								"data": ""
							}),200
						else:
							return jsonify({
								"success": False,
								"data": "ERROR_request_data_not_found"
							}),400
					else:
						return jsonify({
							"success": False,
							"data": "ERROR_requset_data_decode_error"
						}),400
				else:
					return jsonify({
						"success": False,
						"data": "ERROR_token_error"
					}),401
			else:
				return jsonify({
					"success": False,
					"data": "ERROR_token_decode_error"
				}),400
		else:
			return jsonify({
				"success": False,
				"data": "ERROR_token_not_found"
			}),401

	@CASE00035_EVENT_BLUEPRINT.post("/editevent/<int:id>")
	def editevent(id):
		header=request.headers.get("Authorization")
		if header:
			token=chash(header.split(" ")[1],"decode")
			if token!=None:
				tokenrow=query(SETTING["dbname"],"SELECT*FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])
				if tokenrow:
					userid=tokenrow[0]["userid"]
					row=query(SETTING["dbname"],"SELECT*FROM `event` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
					if row:
						if userid==row[0]["userid"]:
							chashdata=chash(str(request.data)[2:-1],"decode")
							if chashdata!=None:
								data=json.loads(chashdata)
								name=data.get("name")
								phone=data.get("phone")
								address=data.get("address")
								type=data.get("type")
								area=data.get("area")
								areatype=data.get("areatype")
								crop=data.get("crop")
								if name and phone and address and type:
									query(SETTING["dbname"],"UPDATE `event` SET `name`=%s,`phone`=%s,`type`=%s,`area`=%s,`areatype`=%s,`crop`=%s,`address`=%s,`updatetime`=%s WHERE `id`=%s",[name,phone,type,area,areatype,crop,address,time(),id],SETTING["dbsetting"])
									return jsonify({
										"success": True,
										"data": ""
									}),200
								else:
									return jsonify({
										"success": False,
										"data": "ERROR_request_data_not_found"
									}),400
							else:
								return jsonify({
									"success": False,
									"data": "ERROR_requset_data_decode_error"
								}),400
						else:
							return jsonify({
								"success": False,
								"data": "ERROR_no_permission"
							}),403
					else:
						return jsonify({
							"success": False,
							"data": "ERROR_event_not_found"
						}),404
				else:
					return jsonify({
						"success": False,
						"data": "ERROR_token_error"
					}),401
			else:
				return jsonify({
					"success": False,
					"data": "ERROR_token_decode_error"
				}),400
		else:
			return jsonify({
				"success": False,
				"data": "ERROR_token_not_found"
			}),401

	@CASE00035_EVENT_BLUEPRINT.delete("/deleteevent/<int:id>")
	def deleteevent(id):
		header=request.headers.get("Authorization")
		if header:
			token=chash(header.split(" ")[1],"decode")
			if token!=None:
				tokenrow=query(SETTING["dbname"],"SELECT*FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])
				if tokenrow:
					userid=tokenrow[0]["userid"]
					row=query(SETTING["dbname"],"SELECT*FROM `event` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
					if row:
						if userid==row[0]["userid"]:
							query(SETTING["dbname"],"UPDATE `event` SET `deletetime`=%s WHERE `id`=%s",[time(),id],SETTING["dbsetting"])
							return jsonify({
								"success": True,
								"data": ""
							}),200
						else:
							return jsonify({
								"success": False,
								"data": "ERROR_no_permission"
							}),403
					else:
						return jsonify({
							"success": False,
							"data": "ERROR_event_not_found"
						}),404
				else:
					return jsonify({
						"success": False,
						"data": "ERROR_token_error"
					}),401
			else:
				return jsonify({
					"success": False,
					"data": "ERROR_token_decode_error"
				}),400
		else:
			return jsonify({
				"success": False,
				"data": "ERROR_token_not_found"
			}),401
except Exception as error:
	printcolorhaveline("fail",error,"")
	jsonify({
		"success": False,
		"data": "[ERROR] unknow error pls tell the admin error:\n"+str(error)
	}),500