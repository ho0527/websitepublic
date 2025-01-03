import base64
import bcrypt
import datetime
import hashlib
import json
import os
import random
import re
import string

# printcolor 函式：在終端機中以不同顏色打印文字
def printcolor(color,text):
	# 根据传入的颜色选择相应的 ANSI 转义码
	if color=="header": # 目前無用
		colorcode="\033[95m"
	elif color=="blue": # 目前無用
		colorcode="\033[94m"
	elif color=="green": # 用於通過 完成 成功 等等
		colorcode="\033[92m"
	elif color=="warning": # 用於用戶驗證失敗 用戶導致的錯誤 等等
		colorcode="\033[93m"
	elif color=="fail": # 用於程式錯誤 重大錯誤 驗證錯誤 等等
		colorcode="\033[91m"
	else:
		printcolor("fail","color error")
		colorcode="\033[95m"
		# raise ValueError("Unsupported color.")

	# 打印带有颜色的文本
	print(str(colorcode)+str(text)+str("\033[0m"))

# printcolorhaveline 函式：在終端機中打印分隔線並打印文字
def printcolorhaveline(color="green",text="",linestyle="-"):
	print(linestyle*30)
	printcolor(color,text)

# switch_key 函式：根據鍵的格式返回對應的鍵值
def switch_key(tkey):
	if tkey.startswith("#"):
		key=tkey[1:]
	else:
		key=tkey.split("@")[0]
	return key

def nowtime():
	return datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

def nowdate():
	return datetime.datetime.now().strftime("%Y-%m-%d")

def hashpassword(password):
	return bcrypt.hashpw(password.encode("utf-8"),bcrypt.gensalt()).decode()

def checkpassword(password,dbpassword):
	return bcrypt.checkpw(password.encode("utf-8"),dbpassword.encode("utf-8"))

def hash(text,hashname):
	text=text.encode("utf-8")
	if hashname=="md5":
		return hashlib.md5(text).hexdigest()
	elif hashname=="sha1":
		return hashlib.sha1(text).hexdigest()
	elif hashname=="sha224":
		return hashlib.sha224(text).hexdigest()
	elif hashname=="sha256":
		return hashlib.sha256(text).hexdigest()
	elif hashname=="sha384":
		return hashlib.sha384(text).hexdigest()
	elif hashname=="sha512":
		return hashlib.sha512(text).hexdigest()
	elif hashname=="sha3_224":
		return hashlib.sha3_224(text).hexdigest()
	elif hashname=="sha3_256":
		return hashlib.sha3_256(text).hexdigest()
	elif hashname=="sha3_384":
		return hashlib.sha3_384(text).hexdigest()
	elif hashname=="sha3_512":
		return hashlib.sha3_512(text).hexdigest()
	else:
		return "error no this hashname"

def uploadfile(path,file,name):
	try:
		with open(os.path.join(path,name),"wb") as f:
			for chunk in file.chunks():
				f.write(chunk)
	except Exception as error:
		printcolorhaveline("fail","[ERROR] function uploadfile error: "+str(error),"")

def randomtext(length=35):
	name=""
	for i in range(length):
		name=name+str("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"[random.randint(0,61)])
	return name

def deletefile(path):
	if os.path.exists(path):
		os.remove(path)

def chash(x,type="encode",encoding="utf-8"):
	def generaterandomstring(length):
		chars=string.ascii_letters+string.digits
		return "".join(random.choice(chars) for _ in range(length))

	def shufflestring(s):
		chars=list(s)
		random.shuffle(chars)
		return "".join(chars)

	def xorbufs(buf1,buf2):
		return bytes([b1^b2 for b1,b2 in zip(buf1,buf2*(len(buf1)//len(buf2)+1))])

	if type=="encode":
		t=str(x).encode(encoding)
		k=generaterandomstring(40).encode(encoding)

		shuffledt=xorbufs(t,k)
		l=base64.b64encode(shuffledt).decode("ascii")

		a=shufflestring(f"chash{encoding}".ljust(20,"a"))
		y=generaterandomstring(10)
		kencoded=base64.b64encode(k).decode("ascii")

		result=f"{a}|{l}|{y}|{kencoded}"

		return result
	elif type=="decode":
		try:
			a,l,y,k=x.split("|")

			shuffledt=base64.b64decode(l)
			kbuf=base64.b64decode(k)

			t=xorbufs(shuffledt,kbuf)

			originalstring=t.decode(encoding)

			return originalstring
		except Exception as error:
			print("[CHECKCODE_ERROR]function chash error: check code error")
			return None
	else:
		print("[KEYTYPEIN_ERROR]function chash error: type must be \"encode\" or \"decode\"")
		return None