# -*- coding: utf-8 -*-
"""A03 Double Exposure Effect 與 A07 Apply Background：兩題都是白底去背後做雙重曝光合成"""
import cv2
import numpy as np


def build_subject_mask(image, white_threshold=242):
    """從白色背景的照片中取出主體遮罩(0/255)"""
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    mask = (gray < white_threshold).astype(np.uint8) * 255

    # 去雜訊並保留最大的連通區域(主體)
    kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (5, 5))
    mask = cv2.morphologyEx(mask, cv2.MORPH_OPEN, kernel, iterations=1)
    _, labels, stats, _ = cv2.connectedComponentsWithStats(mask, 8)
    largest = 1 + int(np.argmax(stats[1:, cv2.CC_STAT_AREA]))
    mask = np.where(labels == largest, 255, 0).astype(np.uint8)

    # 填滿主體內部因為亮色被誤判成背景的空洞
    flood = mask.copy()
    holes = np.zeros((mask.shape[0] + 2, mask.shape[1] + 2), np.uint8)
    cv2.floodFill(flood, holes, (0, 0), 255)
    mask = mask | cv2.bitwise_not(flood)
    return mask


def cover_resize(image, width, height):
    """等比例縮放並置中裁切，讓貼圖填滿指定尺寸"""
    scale = max(width / image.shape[1], height / image.shape[0])
    resized = cv2.resize(image, (int(np.ceil(image.shape[1] * scale)),
                                 int(np.ceil(image.shape[0] * scale))))
    offset_x = (resized.shape[1] - width) // 2
    offset_y = (resized.shape[0] - height) // 2
    return resized[offset_y:offset_y + height, offset_x:offset_x + width]


def double_exposure(subject_path, texture_path, output_path,
                    screen_weight=0.75, desaturate=0.35, texture_scale=1.0,
                    blend_mode="screen", texture_crop=None):
    """把 texture 疊進 subject 的輪廓內，輪廓外留白

    blend_mode="screen"：濾色，適合亮色紋理(夕陽)
    blend_mode="luma"  ：以主體亮度控制混合比例，暗處保留主體、亮處讓紋理透出來，
                         適合含有深色細節(樹林)的紋理
    """
    subject = cv2.imread(subject_path)
    texture = cv2.imread(texture_path)
    if texture_crop is not None:
        top_ratio, bottom_ratio = texture_crop
        texture = texture[int(texture.shape[0] * top_ratio):int(texture.shape[0] * bottom_ratio)]
    height, width = subject.shape[:2]

    mask = build_subject_mask(subject)
    alpha = (cv2.GaussianBlur(mask, (0, 0), 1.2).astype(np.float32) / 255.0)[:, :, None]

    # 主體先降低彩度，讓疊上去的紋理色調成為主角
    gray = cv2.cvtColor(subject, cv2.COLOR_BGR2GRAY)
    gray_bgr = cv2.cvtColor(gray, cv2.COLOR_GRAY2BGR)
    base = (subject.astype(np.float32) * (1 - desaturate)
            + gray_bgr.astype(np.float32) * desaturate)

    # 紋理只鋪在主體的外接矩形範圍，避免主體外的紋理被浪費
    x, y, w, h = cv2.boundingRect(mask)
    tile = np.full_like(base, 255.0)
    scaled_w = int(w * texture_scale)
    scaled_h = int(h * texture_scale)
    patch = cover_resize(texture, scaled_w, scaled_h).astype(np.float32)
    start_x = max(x - (scaled_w - w) // 2, 0)
    start_y = max(y - (scaled_h - h) // 2, 0)
    end_x = min(start_x + scaled_w, width)
    end_y = min(start_y + scaled_h, height)
    tile[start_y:end_y, start_x:end_x] = patch[:end_y - start_y, :end_x - start_x]

    if blend_mode == "screen":
        # 濾色混合：亮處保留紋理、暗處保留主體輪廓
        screen = 255.0 - (255.0 - base) * (255.0 - tile) / 255.0
        blended = base * (1 - screen_weight) + screen * screen_weight
    else:
        # 以主體亮度當混合權重：主體暗的地方(頭、腳)保留原樣，亮的地方讓紋理透出來
        luma = cv2.cvtColor(base.astype(np.uint8), cv2.COLOR_BGR2GRAY).astype(np.float32) / 255.0
        weight = (screen_weight * luma)[:, :, None]
        blended = base * (1 - weight) + tile * weight

    white = np.full_like(base, 255.0)
    result = blended * alpha + white * (1 - alpha)
    cv2.imwrite(output_path, result.astype(np.uint8), [cv2.IMWRITE_JPEG_QUALITY, 95])
    print(output_path, "mask ratio %.3f" % (mask.mean() / 255))


A03 = r"C:/nginx/skill/worldskill/2022/modulea/A03"
A07 = r"C:/nginx/skill/worldskill/2022/modulea/A07"

double_exposure(A03 + "/Bison.jpg", A03 + "/Snow.jpg", A03 + "/double-exposure.jpg",
                screen_weight=0.95, desaturate=0.12, texture_scale=1.0,
                blend_mode="luma", texture_crop=(0.34, 0.87))
double_exposure(A07 + "/1.jpg", A07 + "/2.jpg", A07 + "/result.jpg",
                screen_weight=0.78, desaturate=0.25, texture_scale=1.0)
