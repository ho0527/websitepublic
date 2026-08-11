# -*- coding: utf-8 -*-
"""A01 Masking Image：把 flower.jpg 的花朵去背、轉灰階，再合成到 background.jpg 上"""
import cv2
import numpy as np

BASE = r"C:/nginx/skill/worldskill/2022/modulea/A01"

flower = cv2.imread(BASE + "/flower.jpg")
background = cv2.imread(BASE + "/background.jpg")
height, width = flower.shape[:2]

# 花瓣是淡粉色(飽和度低、亮度高)，紫色背景飽和度高，用 HSV 的 S/V 建立 GrabCut 初始標記
hsv = cv2.cvtColor(flower, cv2.COLOR_BGR2HSV).astype(int)
saturation = hsv[:, :, 1]
value = hsv[:, :, 2]

kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (9, 9))

# 確定前景：明亮且低飽和的花瓣核心(取最大連通區域後再侵蝕，確保完全落在花朵內)
core = ((saturation < 110) & (value > 150)).astype(np.uint8) * 255
core = cv2.morphologyEx(core, cv2.MORPH_OPEN, kernel, iterations=2)
_, labels, stats, _ = cv2.connectedComponentsWithStats(core, 8)
largest = 1 + int(np.argmax(stats[1:, cv2.CC_STAT_AREA]))
core = np.where(labels == largest, 255, 0).astype(np.uint8)

grab = np.full((height, width), cv2.GC_PR_FGD, np.uint8)
grab[saturation > 170] = cv2.GC_BGD                            # 高飽和的紫色背景視為確定背景
grab[cv2.erode(core, kernel, iterations=2) > 0] = cv2.GC_FGD   # 花瓣核心視為確定前景

bg_model = np.zeros((1, 65), np.float64)
fg_model = np.zeros((1, 65), np.float64)
cv2.grabCut(flower, grab, None, bg_model, fg_model, 5, cv2.GC_INIT_WITH_MASK)
mask = np.where((grab == cv2.GC_FGD) | (grab == cv2.GC_PR_FGD), 255, 0).astype(np.uint8)

# 去雜訊 → 只留最大一塊(花朵) → 封住花瓣細縫 → 填滿內部空洞
mask = cv2.morphologyEx(mask, cv2.MORPH_OPEN, kernel, iterations=2)
_, labels, stats, _ = cv2.connectedComponentsWithStats(mask, 8)
largest = 1 + int(np.argmax(stats[1:, cv2.CC_STAT_AREA]))
mask = np.where(labels == largest, 255, 0).astype(np.uint8)
mask = cv2.morphologyEx(mask, cv2.MORPH_CLOSE, kernel, iterations=3)
flood = mask.copy()
holes = np.zeros((height + 2, width + 2), np.uint8)
cv2.floodFill(flood, holes, (0, 0), 255)
mask = mask | cv2.bitwise_not(flood)

# 邊緣羽化，讓合成後不會有鋸齒
alpha = (cv2.GaussianBlur(mask, (0, 0), 1.5).astype(np.float32) / 255.0)[:, :, None]

# 花朵轉灰階(仍轉回三通道以便與彩色背景合成)
gray = cv2.cvtColor(flower, cv2.COLOR_BGR2GRAY)
gray_bgr = cv2.cvtColor(gray, cv2.COLOR_GRAY2BGR).astype(np.float32)

# 背景縮放到與花朵同尺寸後合成
bg = cv2.resize(background, (width, height)).astype(np.float32)
result = gray_bgr * alpha + bg * (1 - alpha)

cv2.imwrite(BASE + "/A01.jpg", result.astype(np.uint8), [cv2.IMWRITE_JPEG_QUALITY, 95])
cv2.imwrite(BASE + "/A01_mask.png", mask)
print("mask ratio", mask.mean() / 255)
