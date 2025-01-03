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

YOUNGDIAMOND_ADMIN_BLUEPRINT=Blueprint("admin",__name__)

try:
    @YOUNGDIAMOND_ADMIN_BLUEPRINT.post("/admin/signin")
    def adminsignin():
        data=json.loads(request.data)
        username=data.get("username")
        password=data.get("password")
        if username:
            if password:
                row=query(SETTING["dbname"],"SELECT*FROM `admin` WHERE `username`=%s AND `deletetime` IS NULL",[username],SETTING["dbsetting"])
                if row:
                    if checkpassword(password,row[0]["password"]):
                        token=randomtext()
                        query(SETTING["dbname"],"INSERT INTO `admintoken`(`adminid`,`token`,`createtime`)VALUES(%s,%s,%s)",[row[0]["id"],token,time()],SETTING["dbsetting"])
                        return jsonify({
                            "success": True,
                            "data": {
                                "userid": row[0]["id"],
                                "permissionid": row[0]["permissionid"],
                                "token": token
                            }
                        }),200
                    else:
                        return jsonify({
                            "success": False,
                            "data": "[ERROR]password error"
                        }),401
                else:
                    return jsonify({
                        "success": False,
                        "data": "[ERROR]username error"
                    }),401
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

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.post("/admin/signout")
    def adminsignout():
        header=request.headers.get("Authorization")
        if header:
            token=header.split(" ")[1]
            row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
            if row:
                query(SETTING["dbname"],"DELETE FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
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
                "data": "[ERROR]token not found"
            }),401

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.get("/admin/getuserlist")
    def admingetuserlist():
        header=request.headers.get("Authorization")
        if header:
            token=header.split(" ")[1]
            row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
            if row:
                row=query(SETTING["dbname"],"SELECT*FROM `user` WHERE `deletetime` IS NULL",[],SETTING["dbsetting"])
                return jsonify({
                    "success": True,
                    "data": row
                }),200
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

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.get("/admin/getuser/<int:id>")
    def admingetuser(id):
        header=request.headers.get("Authorization")
        if header:
            token=header.split(" ")[1]
            row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
            if row:
                row=query(SETTING["dbname"],"SELECT*FROM `user` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])

                if row:
                    return jsonify({
                        "success": True,
                        "data": row[0]
                    }),200
                else:
                    return jsonify({
                        "success": False,
                        "data": "[ERROR]user not found"
                    }),400
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

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.post("/admin/newuser")
    def adminnewuser():
        data=json.loads(request.data)
        username=data.get("username")
        name=data.get("name")
        email=data.get("email")
        password=data.get("password")
        sex=data.get("sex")
        balance=data.get("balance")
        lastclass=data.get("lastclass")
        lastclassdate=data.get("lastclassdate")

        if name and username and email and password and sex and balance and lastclass and lastclassdate:
            header=request.headers.get("Authorization")
            if header:
                token=header.split(" ")[1]
                row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
                if row:
                    query(SETTING["dbname"],
                        "INSERT INTO `user`(`username`,`name`,`email`,`password`,`sex`,`balance`,`lastclass`,`lastclassdate`,`createtime`)VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s)",
                        [username,name,email,hashpassword(password),sex,balance,lastclass,lastclassdate,time()],
                        SETTING["dbsetting"]
                    )
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
                    "data": "[ERROR]token not found"
                }),401
        else:
            return jsonify({
                "success": False,
                "data": "[ERROR]requset data not found"
            }),400

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.put("/admin/edituser/<int:id>")
    def adminedituser(id):
        data=json.loads(request.data)
        no=data.get("no")
        balance=data.get("balance")
        nextclass=data.get("nextclass")

        if no and balance and nextclass:
            header=request.headers.get("Authorization")
            if header:
                token=header.split(" ")[1]
                row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
                if row:
                    row=query(SETTING["dbname"],"SELECT*FROM `user` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
                    if row:
                        query(SETTING["dbname"],"UPDATE `user` SET `no`=%s,`balance`=%s,`nextclass`=%s,`updatetime`=%s WHERE `id`=%s",[no,balance,nextclass,time(),id],SETTING["dbsetting"])
                        return jsonify({
                            "success": True,
                            "data": ""
                        }),200
                    else:
                        return jsonify({
                            "success": False,
                            "data": "[ERROR]user not found"
                        }),400
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
        else:
            return jsonify({
                "success": False,
                "data": "[ERROR]requset data not found"
            }),400

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.delete("/admin/deleteuser/<int:id>")
    def admindeleteuser(id):
        header=request.headers.get("Authorization")
        if header:
            token=header.split(" ")[1]
            row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
            if row:
                row=query(SETTING["dbname"],"SELECT*FROM `user` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
                if row:
                    query(SETTING["dbname"],"UPDATE `user` SET `deletetime`=%s WHERE `id`=%s",[time(),id],SETTING["dbsetting"])
                    return jsonify({
                        "success": True,
                        "data": ""
                    }),200
                else:
                    return jsonify({
                        "success": False,
                        "data": "[ERROR]user not found"
                    }),400
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

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.get("/admin/getlessonlist")
    def admingetlessonlist():
        header=request.headers.get("Authorization")
        if header:
            token=header.split(" ")[1]
            row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
            if row:
                row=query(SETTING["dbname"],"SELECT*FROM `lesson` WHERE `deletetime` IS NULL",[],SETTING["dbsetting"])
                return jsonify({
                    "success": True,
                    "data": row
                }),200
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

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.get("/admin/getlesson/<int:id>")
    def admingetlesson(id):
        header=request.headers.get("Authorization")
        if header:
            token=header.split(" ")[1]
            row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
            if row:
                row=query(SETTING["dbname"],"SELECT*FROM `lesson` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])

                if row:
                    return jsonify({
                        "success": True,
                        "data": row[0]
                    }),200
                else:
                    return jsonify({
                        "success": False,
                        "data": "[ERROR]lesson not found"
                    }),400
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

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.post("/admin/newlesson")
    def adminnewlesson():
        data=json.loads(request.data)
        name=data.get("name")
        level=data.get("level")
        pagecount=data.get("pagecount")
        questioncount=data.get("questioncount")

        if name and level and pagecount and questioncount:
            header=request.headers.get("Authorization")
            if header:
                token=header.split(" ")[1]
                row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
                if row:
                    query(SETTING["dbname"],
                        "INSERT INTO `lesson`(`name`,`level`,`pagecount`,`questioncount`,`createtime`)VALUES(%s,%s,%s,%s,%s)",
                        [name,level,pagecount,questioncount,time()],
                        SETTING["dbsetting"]
                    )
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
                    "data": "[ERROR]token not found"
                }),401
        else:
            return jsonify({
                "success": False,
                "data": "[ERROR]requset data not found"
            }),400

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.put("/admin/editlesson/<int:id>")
    def admineditlesson(id):
        data=json.loads(request.data)
        name=data.get("name")
        level=data.get("level")
        pagecount=data.get("pagecount")
        questioncount=data.get("questioncount")

        if name and level and pagecount and questioncount:
            header=request.headers.get("Authorization")
            if header:
                token=header.split(" ")[1]
                row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
                if row:
                    row=query(SETTING["dbname"],"SELECT*FROM `lesson` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
                    if row:
                        query(SETTING["dbname"],"UPDATE `lesson` SET `name`=%s,`level`=%s,`pagecount`=%s,`questioncount`=%s,`updatetime`=%s WHERE `id`=%s",[name,level,pagecount,questioncount,time(),id],SETTING["dbsetting"])
                        return jsonify({
                            "success": True,
                            "data": ""
                        }),200
                    else:
                        return jsonify({
                            "success": False,
                            "data": "[ERROR]lesson not found"
                        }),400
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
        else:
            return jsonify({
                "success": False,
                "data": "[ERROR]requset data not found"
            }),400

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.delete("/admin/deletelesson/<int:id>")
    def admindeletelesson(id):
        header=request.headers.get("Authorization")
        if header:
            token=header.split(" ")[1]
            row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
            if row:
                row=query(SETTING["dbname"],"SELECT*FROM `lesson` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
                if row:
                    query(SETTING["dbname"],"UPDATE `lesson` SET `deletetime`=%s WHERE `id`=%s",[time(),id],SETTING["dbsetting"])
                    return jsonify({
                        "success": True,
                        "data": ""
                    }),200
                else:
                    return jsonify({
                        "success": False,
                        "data": "[ERROR]lesson not found"
                    }),400
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

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.get("/admin/getadminlist")
    def admingetadminlist():
        header=request.headers.get("Authorization")
        if header:
            token=header.split(" ")[1]
            row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
            if row:
                row=query(SETTING["dbname"],"SELECT*FROM `admin` WHERE `deletetime` IS NULL",[],SETTING["dbsetting"])
                data=[]
                for i in range(len(row)):
                    permissionrow=query(SETTING["dbname"],"SELECT*FROM `permission` WHERE `id`=%s",[row[i]["permissionid"]],SETTING["dbsetting"])
                    data.append({
                        "id": row[i]["id"],
                        "username": row[i]["username"],
                        "name": row[i]["name"],
                        "email": row[i]["email"],
                        "permission": permissionrow[0]["name"],
                        "createtime": row[i]["createtime"]
                    })
                return jsonify({
                    "success": True,
                    "data": data
                }),200
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

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.get("/admin/getadmin/<int:id>")
    def admingetadmin(id):
        header=request.headers.get("Authorization")
        if header:
            token=header.split(" ")[1]
            row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
            if row:
                row=query(SETTING["dbname"],"SELECT*FROM `admin` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
                if row:
                    return jsonify({
                        "success": True,
                        "data": {
                            "id": row[0]["id"],
                            "username": row[0]["username"],
                            "name": row[0]["name"],
                            "email": row[0]["email"],
                            "permissionid": row[0]["permissionid"],
                            "createtime": row[0]["createtime"]
                        }
                    }),200
                else:
                    return jsonify({
                        "success": False,
                        "data": "[ERROR]admin not found"
                    }),400
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

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.post("/admin/newadmin")
    def adminnewadmin():
        data=json.loads(request.data)
        permissionid=data.get("permissionid")
        email=data.get("email")
        username=data.get("username")
        name=data.get("name")
        password=data.get("password")

        if permissionid and email and username and name and password:
            header=request.headers.get("Authorization")
            if header:
                token=header.split(" ")[1]
                tokenrow=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
                if tokenrow:
                    row=query(SETTING["dbname"],"SELECT*FROM `admin` WHERE `username`=%s AND `deletetime` IS NULL",[username],SETTING["dbsetting"])
                    if not row:
                        query(SETTING["dbname"],
                            "INSERT INTO `admin`(`permissionid`,`username`,`name`,`email`,`password`,`createtime`)VALUES(%s,%s,%s,%s,%s,%s)",
                            [permissionid,username,name,email,hashpassword(password),time()],
                            SETTING["dbsetting"]
                        )
                        return jsonify({
                            "success": True,
                            "data": ""
                        }),200
                    else:
                        return jsonify({
                            "success": False,
                            "data": "[ERROR]username exist"
                    }),409
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
        else:
            return jsonify({
                "success": False,
                "data": "[ERROR]request data not found"
            }),400

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.put("/admin/editadmin/<int:id>")
    def admineditadmin(id):
        data=json.loads(request.data)
        permissionid=data.get("permissionid")
        email=data.get("email")
        username=data.get("username")
        name=data.get("name")
        # password=data.get("password")

        if permissionid and email and username and name:
            header=request.headers.get("Authorization")
            if header:
                token=header.split(" ")[1]
                tokenrow=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
                if tokenrow:
                    row=query(SETTING["dbname"],"SELECT*FROM `admin` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
                    if row:
                        row=query(SETTING["dbname"],"SELECT*FROM `admin` WHERE `id`!=%s AND `username`=%s AND `deletetime` IS NULL",[id,username],SETTING["dbsetting"])
                        if not row:
                            query(SETTING["dbname"],
                                "UPDATE `admin` SET `permissionid`=%s,`username`=%s,`name`=%s,`email`=%s,`updatetime`=%s WHERE `id`=%s",
                                [permissionid,username,name,email,time(),id],
                                SETTING["dbsetting"]
                            )
                            return jsonify({
                                "success": True,
                                "data": ""
                            }),200
                        else:
                            return jsonify({
                                "success": False,
                                "data": "[ERROR]username exist"
                        }),409
                    else:
                        return jsonify({
                            "success": False,
                            "data": "[ERROR]user not found"
                    }),400
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
        else:
            return jsonify({
                "success": False,
                "data": "[ERROR]request data not found"
            }),400

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.delete("/admin/deleteadmin/<int:id>")
    def admindeleteadmin(id):
        header=request.headers.get("Authorization")
        if header:
            token=header.split(" ")[1]
            row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
            if row:
                row=query(SETTING["dbname"],"SELECT*FROM `admin` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
                if row:
                    query(SETTING["dbname"],"UPDATE `admin` SET `deletetime`=%s WHERE `id`=%s",[time(),id],SETTING["dbsetting"])
                    return jsonify({
                        "success": True,
                        "data": ""
                    }),200
                else:
                    return jsonify({
                        "success": False,
                        "data": "[ERROR]admin not found"
                    }),400
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

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.get("/admin/getpermissionlist")
    def admingetpermissionlist():
        header=request.headers.get("Authorization")
        if header:
            token=header.split(" ")[1]
            row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
            if row:
                row=query(SETTING["dbname"],"SELECT*FROM `permission` WHERE `deletetime` IS NULL",[],SETTING["dbsetting"])
                return jsonify({
                    "success": True,
                    "data": row
                }),200
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

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.get("/admin/getpermission/<int:id>")
    def admingetpermission(id):
        header=request.headers.get("Authorization")
        if header:
            token=header.split(" ")[1]
            row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
            if row:
                row=query(SETTING["dbname"],"SELECT*FROM `permission` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])

                if row:
                    return jsonify({
                        "success": True,
                        "data": row[0]
                    }),200
                else:
                    return jsonify({
                        "success": False,
                        "data": "[ERROR]permission not found"
                    }),400
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

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.post("/admin/newpermission")
    def adminnewpermission():
        data=json.loads(request.data)
        name=data.get("name")

        if name:
            header=request.headers.get("Authorization")
            if header:
                token=header.split(" ")[1]
                row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
                if row:
                    query(SETTING["dbname"],"INSERT INTO `permission`(`name`,`createtime`)VALUES(%s,%s)",[name,time()],SETTING["dbsetting"])
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
                    "data": "[ERROR]token not found"
                }),401
        else:
            return jsonify({
                "success": False,
                "data": "[ERROR]requset data not found"
            }),400

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.put("/admin/editpermission/<int:id>")
    def admineditpermission(id):
        data=json.loads(request.data)
        name=data.get("name")

        if name:
            header=request.headers.get("Authorization")
            if header:
                token=header.split(" ")[1]
                row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
                if row:
                    row=query(SETTING["dbname"],"SELECT*FROM `permission` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
                    if row:
                        query(SETTING["dbname"],"UPDATE `permission` SET `name`=%s,`updatetime`=%s WHERE `id`=%s",[name,time(),id],SETTING["dbsetting"])
                        return jsonify({
                            "success": True,
                            "data": ""
                        }),200
                    else:
                        return jsonify({
                            "success": False,
                            "data": "[ERROR]permission not found"
                        }),400
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
        else:
            return jsonify({
                "success": False,
                "data": "[ERROR]requset data not found"
            }),400

    @YOUNGDIAMOND_ADMIN_BLUEPRINT.delete("/admin/deletepermission/<int:id>")
    def admindeletepermission(id):
        header=request.headers.get("Authorization")
        if header:
            token=header.split(" ")[1]
            row=query(SETTING["dbname"],"SELECT*FROM `admintoken` WHERE `token`=%s",[token],SETTING["dbsetting"])
            if row:
                row=query(SETTING["dbname"],"SELECT*FROM `permission` WHERE `id`=%s AND `deletetime` IS NULL",[id],SETTING["dbsetting"])
                if row:
                    query(SETTING["dbname"],"UPDATE `permission` SET `deletetime`=%s WHERE `id`=%s",[time(),id],SETTING["dbsetting"])
                    return jsonify({
                        "success": True,
                        "data": ""
                    }),200
                else:
                    return jsonify({
                        "success": False,
                        "data": "[ERROR]permission not found"
                    }),400
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