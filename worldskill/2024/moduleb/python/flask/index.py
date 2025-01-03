from flask import Flask
# from flask_restplus import Api,Resource
from flask_cors import CORS
from apiflask import APIFlask, Schema, abort
from apiflask.fields import Integer,String
from apiflask.validators import Length,OneOf

# 藍圖 START
# from api.index import CASE00035_INDEX_BLUEPRINT
from api.company import WORLDSKILL2024MODULEB_COMPANY_BLUEPRINT
from api.customer import CASE00035_CUSTOMER_BLUEPRINT
from api.event import CASE00035_EVENT_BLUEPRINT
# from api.user import YOUNGDIAMOND_FOOD_BLURPRINT
# from api.user import YOUNGDIAMOND_FOODERORDER_BLUEPRINT
# from api.user import YOUNGDIAMOND_ROOM_BLUEPRINT
# 藍圖 END

app=APIFlask(__name__)

# 將藍圖連結到 Flask 應用程式實例

# 藍圖 START
# app.register_blueprint(CASE00035_INDEX_BLUEPRINT)
app.register_blueprint(WORLDSKILL2024MODULEB_COMPANY_BLUEPRINT)
app.register_blueprint(CASE00035_CUSTOMER_BLUEPRINT)
app.register_blueprint(CASE00035_EVENT_BLUEPRINT)
# 藍圖 END

CORS(app)

if __name__=="__main__":
    app.run(debug=True,port=8942)