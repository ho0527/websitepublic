# import
import json
import requests
from apiflask import APIBlueprint
from apiflask.fields import Integer,String
from flask import Blueprint
from flask import Flask,request,jsonify
from fugle_marketdata import RestClient

from function.sql import *
from function.thing import *
from .initialize import *
from .function import *

CASE00035_INDEX_BLUEPRINT=Blueprint("index",__name__)

try:
	# 獲取股票列表
	@CASE00035_INDEX_BLUEPRINT.post("/newdata")
	def newdata():
		"""
			預計回傳值
			"data": [
				{
					"id": int(程式股票id),
					"name": string(股票名稱),
					"no": string(股票序號),
					"money": number(股票價格),
					"percent": number(漲跌幅度)
				},(...inf)
			]
		"""
		# data=json.loads(request.data)
		# username=data.get("username")
		# password=data.get("password")
		requestcall=requests.post("https://uav.aphia.gov.tw/ws-uavsprayrecordtest-service.html",json={},headers={"Content-Type": "application/json"})
		print(requestcall)
		clientrequestdata=requestcall.text

		# client=RestClient(api_key=APIKEY)
		# clientrequest=client.stock
		# clientrequestdata=clientrequest.snapshot.quotes(market="TSE")

		return jsonify({
			"success": True,
			"data": clientrequestdata
		}),200

		if username:
			if password:
				row=query(SETTING["dbname"],"SELECT*FROM `user` WHERE `username`=%s AND `deletetime` IS NULL",[username],SETTING["dbsetting"])
				if row:
					if checkpassword(password,row[0]["password"]):
						token=randomtext()
						query(SETTING["dbname"],"INSERT INTO `token`(`userid`,`token`,`createtime`)VALUES(%s,%s,%s)",[row[0]["id"],token,time()],SETTING["dbsetting"])
						return jsonify({
							"success": True,
							"data": {
								"userid": row[0]["id"],
								"name": row[0]["name"],
								"nextclass": row[0]["nextclass"],
								"token": token
							}
						}),200
					else:
						return jsonify({
							"success": False,
							"data": "[ERROR]password error"
						}),400
				else:
					return jsonify({
						"success": False,
						"data": "[ERROR]username error"
					}),400
			else:
				return jsonify({
					"success": False,
					"data": "[ERROR]request data not found"
				}),400
		else:
			return jsonify({
				"success": False,
				"data": "[ERROR]request data not found"
			}),400

	# 獲取股票資訊
	@CASE00035_INDEX_BLUEPRINT.get("/getstock/<int:id>")
	def getstock(id):
		chashdata=chash(request.data,"decode")
		try:
			data=json.loads(chashdata)
			username=data.get("username")
			name=data.get("name")
			email=data.get("email")
			password=data.get("password")
			sex=data.get("sex")
		except Exception as error:
			return jsonify({
				"success": False,
				"data": "[ERROR]request data not found"
			}),400

		if name and username and email and password and sex:
			row=query(SETTING["dbname"],"SELECT*FROM `user` WHERE `username`=%s",[username],SETTING["dbsetting"])
			if not row:
				query(SETTING["dbname"],
					"INSERT INTO `user`(`no`,`username`,`name`,`email`,`password`,`sex`,`balance`,`nextclass`,`lastclassdate`,`createtime`)VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
					["",username,name,email,hashpassword(password),sex,"0","1",date(),time()],
					SETTING["dbsetting"]
				)
				return jsonify({
					"success": True,
					"data": ""
				}),200
			else:
				return jsonify({
					"success": False,
					"data": "[ERROR]user exist"
				}),401
		else:
			return jsonify({
				"success": False,
				"data": "[ERROR]requset data not found"
			}),400

	@CASE00035_INDEX_BLUEPRINT.post("/signout")
	def signout():
		header=request.headers.get("Authorization")
		if header:
			token=chash(header.split(" ")[1],"decode")
			if token!=None:
				tokenrow=query(SETTING["dbname"],"SELECT*FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])
				if tokenrow:
					query(SETTING["dbname"],"DELETE FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])
					return jsonify({
						"success": True,
						"data": ""
					}),200
				else:
					return jsonify({
						"success": False,
						"data": "[ERROR]token error"
					}),401
			else:
				return jsonify({
					"success": False,
					"data": "[ERROR]token error"
				}),401
		else:
			return jsonify({
				"success": False,
				"data": "[ERROR]token not found"
			}),401

	@CASE00035_INDEX_BLUEPRINT.put("/editnextclass")
	def editnextclass():
		data=json.loads(request.data)
		nextclass=data.get("nextclass")

		header=request.headers.get("Authorization")
		if header:
			token=header.split(" ")[1]
			tokenrow=query(SETTING["dbname"],"SELECT*FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])
			if tokenrow:
				row=query(SETTING["dbname"],"SELECT*FROM `user` WHERE `id`=%s AND `deletetime` IS NULL",[tokenrow[0]["userid"]],SETTING["dbsetting"])

				if row:
					query(SETTING["dbname"],"UPDATE `user` SET `nextclass`=%s,`lastclassdate`=%s WHERE `id`=%s",[nextclass,date(),tokenrow[0]["userid"]],SETTING["dbsetting"])
					query(SETTING["dbname"],"DELETE FROM `token` WHERE `token`=%s",[token],SETTING["dbsetting"])

					return jsonify({
						"success": True,
						"data": ""
					}),200
				else:
					return jsonify({
						"success": False,
						"data": "[ERROR]token error"
					}),401
			else:
				return jsonify({
					"success": False,
					"data": "[ERROR]token error"
				}),401
		else:
			return jsonify({
				"success": False,
				"data": "[ERROR]token not found"
			}),401
except Exception as error:
	printcolorhaveline("fail",error,"")
	jsonify({
		"success": False,
		"data": "[ERROR] unknow error pls tell the admin error:\n"+str(error)
	}),500