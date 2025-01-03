import bcrypt

def hashpassword(password):
    hashed = bcrypt.hashpw(password.encode("utf-8"), bcrypt.gensalt()).decode()
    print(f"Hashed password: {hashed}")
    return hashed

def checkpassword(password, dbpassword):
    print(f"Input password: {password}")
    print(f"Stored hashed password: {dbpassword}")
    result = bcrypt.checkpw(password.encode("utf-8"), dbpassword.encode("utf-8"))
    print(f"Password match: {result}")
    return result

# 測試數據
stored_hash = "$2b$12$FPPKXOMY8bUjft/SaRcdJehgbhV753VZAzRZ43apH.WPv00qLAgeu"
input_password = "1234"

# 測試密碼驗證
print("Testing password check:")
print(checkpassword(input_password, stored_hash))  # 應該返回 True
