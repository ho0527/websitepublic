# -*- coding: utf-8 -*-
"""A04 Instagram Grid：把大頭貼、帳號與九宮格照片放進 Instagram 版型，再貼進 iPhone 模擬圖"""
import cv2
import numpy as np
from PIL import Image, ImageDraw, ImageFont

BASE = r"C:/nginx/skill/worldskill/2022/modulea/A04"

# ---------- 第一步：完成 Instagram 個人頁版型 ----------
screen = Image.open(BASE + "/Instagram-Profile-2022.psd").convert("RGB")
draw = ImageDraw.Draw(screen)

# psd 內的深色佔位區塊座標(以程式偵測後寫死)：大頭貼為圓形，其餘九格為貼文
AVATAR_BOX = (25, 153, 152, 152)          # x, y, w, h
GRID_BOXES = [(0, 565), (251, 565), (502, 565),
              (0, 816), (251, 816), (502, 816),
              (0, 1066), (251, 1066), (502, 1066)]
CELL = 248
LAST_ROW_HEIGHT = 180                      # 最後一列被底部導覽列切掉，只貼可見的部分


def cover(path, width, height):
    """等比例縮放並置中裁切成指定尺寸"""
    image = Image.open(path).convert("RGB")
    scale = max(width / image.width, height / image.height)
    image = image.resize((max(int(image.width * scale), width),
                          max(int(image.height * scale), height)), Image.LANCZOS)
    left = (image.width - width) // 2
    top = (image.height - height) // 2
    return image.crop((left, top, left + width, top + height))


# 保留右下角的按讚小氣泡，貼完照片後再貼回來
badge = screen.crop((420, 1140, 630, 1235))

# 九宮格照片
for index, (x, y) in enumerate(GRID_BOXES):
    height = LAST_ROW_HEIGHT if index >= 6 else CELL
    photo = cover(BASE + "/picture (%d).jpg" % (index + 1), CELL, CELL)
    screen.paste(photo.crop((0, 0, CELL, height)), (x, y))

screen.paste(badge, (420, 1140))

# 圓形大頭貼(用遮罩去角)
avatar = cover(BASE + "/profile-pic.jpg", AVATAR_BOX[2], AVATAR_BOX[3])
avatar_mask = Image.new("L", (AVATAR_BOX[2], AVATAR_BOX[3]), 0)
ImageDraw.Draw(avatar_mask).ellipse((0, 0, AVATAR_BOX[2] - 1, AVATAR_BOX[3] - 1), fill=255)
screen.paste(avatar, (AVATAR_BOX[0], AVATAR_BOX[1]), avatar_mask)

# 把版型上的 "username" 換成題目指定的帳號 pedro.souza
draw.rectangle((180, 58, 570, 108), fill=(250, 250, 250))
title_font = ImageFont.truetype("C:/Windows/Fonts/segoeuib.ttf", 30)
left, top, right, bottom = draw.textbbox((0, 0), "pedro.souza", font=title_font)
draw.text(((750 - (right - left)) / 2 - left, 62 - top), "pedro.souza",
          font=title_font, fill=(38, 38, 38))

screen.save(BASE + "/instagram-profile.jpg", quality=95)

# ---------- 第二步：把畫面透視變形貼進 iPhone 模擬圖 ----------
mock = cv2.imread(BASE + "/iphone-mock.jpg")
screen_bgr = np.asarray(screen)[:, :, ::-1].copy()
height, width = screen_bgr.shape[:2]

# iPhone 螢幕四角(以深色區域偵測後寫死)：左上、右上、右下、左下
SCREEN_CORNERS = np.float32([[2956, 1586], [3670, 1774], [3082, 3089], [2353, 2842]])
source_corners = np.float32([[0, 0], [width, 0], [width, height], [0, height]])

matrix = cv2.getPerspectiveTransform(source_corners, SCREEN_CORNERS)
warped = cv2.warpPerspective(screen_bgr, matrix, (mock.shape[1], mock.shape[0]))

# 用同樣的四邊形做遮罩，邊緣稍微羽化避免鋸齒
mask = np.zeros(mock.shape[:2], np.uint8)
cv2.fillConvexPoly(mask, SCREEN_CORNERS.astype(np.int32), 255)
mask = cv2.erode(mask, np.ones((3, 3), np.uint8))
alpha = (cv2.GaussianBlur(mask, (0, 0), 1.5).astype(np.float32) / 255.0)[:, :, None]

result = warped.astype(np.float32) * alpha + mock.astype(np.float32) * (1 - alpha)
cv2.imwrite(BASE + "/instagram-grid.jpg", result.astype(np.uint8),
            [cv2.IMWRITE_JPEG_QUALITY, 92])
print("A04 ok")
