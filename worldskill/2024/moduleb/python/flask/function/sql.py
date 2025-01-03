# import pymysql
import datetime
import MySQLdb
import mysql.connector as mysql
from MySQLdb.cursors import DictCursor
from mysql.connector import Error

# 自創
from function.thing import *

# main START

def createdb(dbname,host="localhost",username="root",password="",port="3306"):
    return MySQLdb.connect(host=host,db=dbname,user=username,passwd=password,port=port)

def query(dbname,query,data=None,setting={"host": "localhost","username": "root","password": "","port": 3306}):
    response=None
    try:
        db=MySQLdb.connect(host=setting["host"],db=dbname,user=setting["username"],passwd=setting["password"],port=setting["port"])
        cursor=db.cursor(DictCursor)
        cursor.execute(query,data)
        response=cursor.fetchall()
        db.commit()
        printcolorhaveline("green","use query function SUCCESS","")
    except Exception as error:
        printcolorhaveline("fail","[ERROR] use query function error "+str(error),"")
        db=MySQLdb.connect(host=setting["host"],db=dbname,user=setting["username"],passwd=setting["password"],port=setting["port"])
    if cursor:
        cursor.close()
    if db:
        db.close()
    return response