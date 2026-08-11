# -*- coding: utf-8 -*-
"""A10 Mosaic 與 A11 Banner creation"""
from PIL import Image, ImageDraw, ImageFont

# ---------------- A10：馬賽克效果 ----------------
A10 = r"C:/nginx/skill/worldskill/2022/modulea/A10"
source = Image.open(A10 + "/mosaic.png").convert("RGB")
BLOCK = 16  # 每個馬賽克方塊的邊長(px)
# 先縮小再用最近鄰放大回原尺寸，就是標準的馬賽克(像素化)作法
small = source.resize((source.width // BLOCK, source.height // BLOCK), Image.BOX)
mosaic = small.resize(source.size, Image.NEAREST)
mosaic.save(A10 + "/a10.jpg", quality=95)
print("A10 ok", mosaic.size)


# ---------------- A11：300x250 廣告 banner ----------------
A11 = r"C:/nginx/skill/worldskill/2022/modulea/A11"
WIDTH, HEIGHT = 300, 250
banner = Image.new("RGB", (WIDTH, HEIGHT), (250, 246, 240))
draw = ImageDraw.Draw(banner)


def cover(path, width, height):
    """等比例縮放並置中裁切成指定尺寸"""
    image = Image.open(path).convert("RGB")
    scale = max(width / image.width, height / image.height)
    image = image.resize((max(int(image.width * scale), width),
                          max(int(image.height * scale), height)), Image.LANCZOS)
    left = (image.width - width) // 2
    top = (image.height - height) // 2
    return image.crop((left, top, left + width, top + height))


# 上半部：三張商品照拼成一列，三張素材全部都用到
PHOTO_HEIGHT = 120
photo_width = WIDTH // 3
for index, name in enumerate(["photo1.jpg", "photo2.jpg", "photo3.jpg"]):
    # 最後一格補足除不盡的餘數，避免右側留白
    box_width = WIDTH - photo_width * 2 if index == 2 else photo_width
    banner.paste(cover(A11 + "/" + name, box_width, PHOTO_HEIGHT), (photo_width * index, 0))

# 照片下緣加一條深色分隔線
draw.rectangle([0, PHOTO_HEIGHT, WIDTH, PHOTO_HEIGHT + 3], fill=(32, 32, 40))


def load_font(file_name, size):
    return ImageFont.truetype("C:/Windows/Fonts/" + file_name, size)


font_title = load_font("arialbd.ttf", 22)
font_sub = load_font("arial.ttf", 13)
font_percent = load_font("arialbd.ttf", 40)
font_button = load_font("arialbd.ttf", 14)


def draw_centered(text, top, font, fill):
    left, upper, right, lower = draw.textbbox((0, 0), text, font=font)
    draw.text(((WIDTH - (right - left)) / 2 - left, top), text, font=font, fill=fill)
    return lower - upper


# 文案來自素材 text.txt：Weekend / Special Sale / save up to / 30% / SHOP NOW
draw_centered("WEEKEND", 132, font_title, (32, 32, 40))
draw_centered("SPECIAL SALE", 158, font_title, (198, 40, 40))
draw_centered("save up to", 186, font_sub, (90, 90, 96))

# 「30%」與 SHOP NOW 按鈕並排放在最下面
percent_left, percent_top, percent_right, percent_bottom = draw.textbbox((0, 0), "30%", font=font_percent)
draw.text((22 - percent_left, 200 - percent_top), "30%", font=font_percent, fill=(198, 40, 40))

BUTTON = (150, 206, 282, 238)
draw.rounded_rectangle(BUTTON, radius=16, fill=(32, 32, 40))
button_left, button_top, button_right, button_bottom = draw.textbbox((0, 0), "SHOP NOW", font=font_button)
draw.text(((BUTTON[0] + BUTTON[2] - (button_right - button_left)) / 2 - button_left,
           (BUTTON[1] + BUTTON[3] - (button_bottom - button_top)) / 2 - button_top),
          "SHOP NOW", font=font_button, fill=(255, 255, 255))

banner.save(A11 + "/a11.jpg", quality=95)
print("A11 ok", banner.size)
