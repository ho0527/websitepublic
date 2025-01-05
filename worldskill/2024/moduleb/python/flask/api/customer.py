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

CASE00035_CUSTOMER_BLUEPRINT=APIBlueprint("customer",__name__)

try:
	@CASE00035_CUSTOMER_BLUEPRINT.get("/getcustomer/<int:id>")
	def getcustomer(id):
		header=request.headers.get("Authorization")
		if header:
			token=chash(header.split(" ")[1],"decode")
			if token!=None:
				tokenrow=query(SETTING["dbname"],"SELECT*FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])
				if tokenrow:
					userid=tokenrow[0]["userid"]
					row=query(SETTING["dbname"],"SELECT*FROM `customer` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
					if row:
						if userid==row[0]["userid"]:
							data=query(SETTING["dbname"],"SELECT*FROM `customer` WHERE `id`=%s AND `deletetime` IS NULL ORDER BY `updatetime` DESC,`id` DESC",[id],SETTING["dbsetting"])

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
							"data": "ERROR_customer_not_found"
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

	@CASE00035_CUSTOMER_BLUEPRINT.get("/getcustomerlist")
	def getcustomerlist():
		header=request.headers.get("Authorization")
		if header:
			token=chash(header.split(" ")[1],"decode")
			if token!=None:
				tokenrow=query(SETTING["dbname"],"SELECT*FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])
				if tokenrow:
					userid=tokenrow[0]["userid"]
					data=query(SETTING["dbname"],"SELECT*FROM `customer` WHERE `userid`=%s AND `deletetime` IS NULL ORDER BY `id` DESC",[userid],SETTING["dbsetting"])

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

	@CASE00035_CUSTOMER_BLUEPRINT.post("/newcustomer")
	def newcustomer():
		header=request.headers.get("Authorization")
		if header:
			token=chash(header.split(" ")[1],"decode")
			if token!=None:
				tokenrow=query(SETTING["dbname"],"SELECT*FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])
				if tokenrow:
					userid=tokenrow[0]["userid"]
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
							query(SETTING["dbname"],"INSERT INTO `customer`(`userid`,`name`,`phone`,`type`,`area`,`areatype`,`crop`,`address`,`createtime`)VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s)",[userid,name,phone,type,area,areatype,crop,address,time()],SETTING["dbsetting"])
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

	@CASE00035_CUSTOMER_BLUEPRINT.post("/editcustomer/<int:id>")
	def editcustomer(id):
		header=request.headers.get("Authorization")
		if header:
			token=chash(header.split(" ")[1],"decode")
			if token!=None:
				tokenrow=query(SETTING["dbname"],"SELECT*FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])
				if tokenrow:
					userid=tokenrow[0]["userid"]
					row=query(SETTING["dbname"],"SELECT*FROM `customer` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
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
									query(SETTING["dbname"],"UPDATE `customer` SET `name`=%s,`phone`=%s,`type`=%s,`area`=%s,`areatype`=%s,`crop`=%s,`address`=%s,`updatetime`=%s WHERE `id`=%s",[name,phone,type,area,areatype,crop,address,time(),id],SETTING["dbsetting"])
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
							"data": "ERROR_customer_not_found"
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

	@CASE00035_CUSTOMER_BLUEPRINT.delete("/deletecustomer/<int:id>")
	def deletecustomer(id):
		header=request.headers.get("Authorization")
		if header:
			token=chash(header.split(" ")[1],"decode")
			if token!=None:
				tokenrow=query(SETTING["dbname"],"SELECT*FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])
				if tokenrow:
					userid=tokenrow[0]["userid"]
					row=query(SETTING["dbname"],"SELECT*FROM `customer` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
					if row:
						if userid==row[0]["userid"]:
							query(SETTING["dbname"],"UPDATE `customer` SET `deletetime`=%s WHERE `id`=%s",[time(),id],SETTING["dbsetting"])
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
							"data": "ERROR_customer_not_found"
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