# -*- coding: utf-8 -*-
"""A02 Vectored artwork：把 A02.svg 的向量圖示以相同幾何再繪製成 A02.jpg（預覽用點陣圖）"""
import numpy as np
from PIL import Image, ImageDraw

BASE = r"C:/nginx/skill/worldskill/2022/modulea/A02"
CELL = 200
SCALE = 4                       # 超取樣倍率，畫完再縮小做抗鋸齒
WIDTH, HEIGHT = 600, 800

WHITE = (255, 255, 255)
PURPLE = (155, 47, 214)
GREEN = (18, 194, 90)
RED = (242, 13, 26)
BLUE = (31, 189, 240)

canvas = Image.new("RGB", (WIDTH * SCALE, HEIGHT * SCALE), WHITE)
draw = ImageDraw.Draw(canvas)


def unit(points, origin):
    """把 0~100 的圖示座標換算成畫布實際像素座標"""
    return [((origin[0] + x) * SCALE, (origin[1] + y) * SCALE) for x, y in points]


def cubic(p0, p1, p2, p3, steps=40):
    """把三次貝茲曲線取樣成折線，用來畫水滴形的定位標記"""
    result = []
    for i in range(steps + 1):
        t = i / steps
        s = 1 - t
        x = s ** 3 * p0[0] + 3 * s * s * t * p1[0] + 3 * s * t * t * p2[0] + t ** 3 * p3[0]
        y = s ** 3 * p0[1] + 3 * s * s * t * p1[1] + 3 * s * t * t * p2[1] + t ** 3 * p3[1]
        result.append((x, y))
    return result


def pin_outline(cx=50.0, top=18.0, left=20.0, right=80.0, bottom=90.0, waist=47.0):
    """定位標記的外框折線（左右各一段貝茲曲線）"""
    points = cubic((cx, top), (left + 13, top), (left, top + 13), (left, waist))
    points += cubic((left, waist), (left, waist + 19), (cx - 8, bottom - 10), (cx, bottom))
    points += cubic((cx, bottom), (cx + 8, bottom - 10), (right, waist + 19), (right, waist))
    points += cubic((right, waist), (right, top + 13), (right - 13, top), (cx, top))
    return points


def rounded_rect(x, y, w, h, radius, origin, outline=None, fill=None, width=6):
    box = [(origin[0] + x) * SCALE, (origin[1] + y) * SCALE,
           (origin[0] + x + w) * SCALE, (origin[1] + y + h) * SCALE]
    draw.rounded_rectangle(box, radius=radius * SCALE, outline=outline, fill=fill,
                           width=int(width * SCALE))


def circle(cx, cy, r, origin, outline=None, fill=None, width=6):
    box = [(origin[0] + cx - r) * SCALE, (origin[1] + cy - r) * SCALE,
           (origin[0] + cx + r) * SCALE, (origin[1] + cy + r) * SCALE]
    draw.ellipse(box, outline=outline, fill=fill, width=int(width * SCALE))


def ellipse(cx, cy, rx, ry, origin, outline=None, fill=None, width=6):
    box = [(origin[0] + cx - rx) * SCALE, (origin[1] + cy - ry) * SCALE,
           (origin[0] + cx + rx) * SCALE, (origin[1] + cy + ry) * SCALE]
    draw.ellipse(box, outline=outline, fill=fill, width=int(width * SCALE))


def polyline(points, origin, color, width=6):
    draw.line(unit(points, origin), fill=color, width=int(width * SCALE), joint="curve")


def polygon(points, origin, fill=None, outline=None, width=6):
    pixels = unit(points, origin)
    if fill is not None:
        draw.polygon(pixels, fill=fill)
    if outline is not None:
        draw.line(pixels + [pixels[0]], fill=outline, width=int(width * SCALE), joint="curve")


CURSOR = [(28, 20), (74, 60), (52, 62), (64, 84), (52, 90), (40, 68), (28, 82)]
CURSOR_SMALL = [(34, 24), (72, 56), (54, 58), (64, 76), (54, 81), (44, 63), (34, 74)]
ENVELOPE_FLAP = [(18, 36), (50, 58), (82, 36)]

# ---- 第一列：信封(紫) ----
origin = (50, 10)
rounded_rect(18, 30, 64, 42, 6, origin, outline=PURPLE)
polyline(ENVELOPE_FLAP, origin, PURPLE)

origin = (250, 10)
rounded_rect(18, 30, 64, 42, 6, origin, fill=PURPLE)
polyline(ENVELOPE_FLAP, origin, WHITE)

origin = (450, 10)
circle(50, 50, 48, origin, fill=PURPLE)
rounded_rect(24, 34, 52, 34, 5, origin, fill=WHITE)
polyline([(24, 39), (50, 57), (76, 39)], origin, PURPLE, width=5)

# ---- 第二列：游標(綠) ----
origin = (50, 210)
polygon(CURSOR, origin, outline=GREEN)

origin = (250, 210)
polygon(CURSOR, origin, fill=GREEN)

origin = (450, 210)
circle(50, 50, 48, origin, fill=GREEN)
polygon(CURSOR_SMALL, origin, fill=WHITE)

# ---- 第三列：定位標記(紅) ----
origin = (50, 410)
polygon(pin_outline(), origin, outline=RED)
circle(50, 46, 12, origin, outline=RED)

origin = (250, 410)
polygon(pin_outline(), origin, fill=RED)

origin = (450, 410)
circle(50, 50, 48, origin, fill=RED)
polygon(pin_outline(50, 22, 30, 70, 78, 42), origin, fill=WHITE)

# ---- 第四列：地球(藍) ----
origin = (50, 610)
circle(50, 54, 34, origin, outline=BLUE)
ellipse(50, 54, 15, 34, origin, outline=BLUE)
polyline([(16, 54), (84, 54)], origin, BLUE)

origin = (250, 610)
circle(50, 54, 34, origin, fill=BLUE)
ellipse(50, 54, 15, 34, origin, outline=WHITE)
polyline([(16, 54), (84, 54)], origin, WHITE)

origin = (450, 610)
circle(50, 50, 48, origin, fill=BLUE)
ellipse(50, 50, 17, 38, origin, outline=WHITE)
polyline([(12, 50), (88, 50)], origin, WHITE)

canvas.resize((WIDTH, HEIGHT), Image.LANCZOS).save(BASE + "/A02.jpg", quality=95)
print("A02 ok")
