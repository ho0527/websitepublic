# -*- coding: utf-8 -*-
"""A06 Spinning Globe Animation：用 world-map.png 產生 3D 旋轉地球的動畫 GIF"""
import numpy as np
from PIL import Image

BASE = r"C:/nginx/skill/worldskill/2022/modulea/A06"

SIZE = 400          # 輸出畫布邊長
RADIUS = 185.0      # 地球半徑(px)
FRAMES = 48         # 一圈的張數
SUPER = 2           # 超取樣倍率，最後再縮回去做抗鋸齒

world = Image.open(BASE + "/world-map.png").convert("RGBA")
# 把透明底換成海洋藍，陸地維持原色
ocean = Image.new("RGBA", world.size, (28, 92, 158, 255))
ocean.alpha_composite(world)
world_pixels = np.asarray(ocean.convert("RGB")).astype(np.float32)
map_height, map_width = world_pixels.shape[:2]

canvas = SIZE * SUPER
radius = RADIUS * SUPER

# 建立畫布座標，換算成球面上的經緯度
axis = np.arange(canvas, dtype=np.float32) - (canvas - 1) / 2.0
grid_x, grid_y = np.meshgrid(axis, axis)
distance = np.sqrt(grid_x ** 2 + grid_y ** 2)
inside = distance <= radius

# 正射投影：球面點 (x, y, z)，z 為朝向觀察者的深度
normal_x = np.clip(grid_x / radius, -1, 1)
normal_y = np.clip(grid_y / radius, -1, 1)
normal_z = np.sqrt(np.clip(1 - normal_x ** 2 - normal_y ** 2, 0, 1))

latitude = np.arcsin(np.clip(normal_y, -1, 1))          # 由上到下 -90°~90°
longitude = np.arctan2(normal_x, normal_z)              # 中央經線為 0

# 簡單的光照：光源在左上前方，讓球體有立體感
light = np.clip(normal_z * 0.72 - normal_x * 0.42 - normal_y * 0.42, 0, None)
shading = (0.35 + 0.75 * light)[:, :, None]

# 邊緣柔化，避免圓周出現鋸齒
edge = np.clip((radius - distance) / (2.0 * SUPER), 0, 1)[:, :, None]

frames = []
for index in range(FRAMES):
    spin = index / FRAMES * 2 * np.pi          # 本張要旋轉的經度
    sample_lon = (longitude + spin) % (2 * np.pi)

    column = (sample_lon / (2 * np.pi) * map_width).astype(np.int32) % map_width
    row = np.clip(((latitude / np.pi + 0.5) * map_height).astype(np.int32), 0, map_height - 1)

    sphere = world_pixels[row, column] * shading
    frame = np.zeros((canvas, canvas, 3), np.float32)
    frame += sphere * edge                      # 圓外為黑色背景
    frame[~inside] = 0

    image = Image.fromarray(np.clip(frame, 0, 255).astype(np.uint8))
    frames.append(image.resize((SIZE, SIZE), Image.LANCZOS))

frames[0].save(BASE + "/a06.gif", save_all=True, append_images=frames[1:],
               duration=70, loop=0, optimize=True)
print("A06 ok", len(frames), "frames")
