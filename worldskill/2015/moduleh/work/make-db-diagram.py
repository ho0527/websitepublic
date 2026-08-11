# -*- coding: utf-8 -*-
"""
產生 db-diagram.svg（資料庫關聯圖）。
此檔案屬於「工作檔」，不列入評分，僅用於重新產生圖檔：
    python work/make-db-diagram.py
"""

import io
import os

# 每個資料表：標題、欄位（名稱, 型別, 主鍵?, 外鍵?）、左上角座標
TABLES = [
    ("dining_module", 40, 40, [
        ("id", "INT UNSIGNED", "PK", ""),
        ("name", "VARCHAR(60) UQ", "", ""),
        ("description", "TEXT", "", ""),
        ("sort_order", "SMALLINT", "", ""),
    ]),
    ("seating", 40, 200, [
        ("id", "INT UNSIGNED", "PK", ""),
        ("dining_module_id", "INT UNSIGNED", "", "FK"),
        ("name", "VARCHAR(40)", "", ""),
        ("configuration", "VARCHAR(120)", "", ""),
        ("start_time / end_time", "TIME", "", ""),
        ("seats_per_competitor", "SMALLINT UNSIGNED", "", ""),
        ("competitor_count", "SMALLINT UNSIGNED", "", ""),
        ("sort_order", "SMALLINT", "", ""),
    ]),
    ("competition_day", 40, 470, [
        ("id", "INT UNSIGNED", "PK", ""),
        ("code", "VARCHAR(10) UQ", "", ""),
        ("day_date", "DATE", "", ""),
        ("sort_order", "SMALLINT", "", ""),
    ]),
    ("reservation", 400, 250, [
        ("id", "INT UNSIGNED", "PK", ""),
        ("booking_id", "INT UNSIGNED", "", "FK"),
        ("competition_day_id", "INT UNSIGNED", "", "FK"),
        ("seating_id", "INT UNSIGNED", "", "FK"),
        ("guest_name", "VARCHAR(120) NULL", "", ""),
        ("guest_country", "CHAR(3)", "", ""),
        ("status", "ENUM(requested,", "", ""),
        ("", "confirmed, waitlisted,", "", ""),
        ("", "declined)", "", ""),
        ("needs_reschedule", "TINYINT(1)", "", ""),
        ("created_at / updated_at", "DATETIME", "", ""),
    ]),
    ("booking", 780, 250, [
        ("id", "INT UNSIGNED", "PK", ""),
        ("booking_no", "VARCHAR(20) UQ", "", ""),
        ("booking_contact_id", "INT UNSIGNED", "", "FK"),
        ("booking_type", "ENUM(individual,", "", ""),
        ("", "group)", "", ""),
        ("created_at", "DATETIME", "", ""),
    ]),
    ("booking_contact", 780, 470, [
        ("id", "INT UNSIGNED", "PK", ""),
        ("name", "VARCHAR(120)", "", ""),
        ("organization", "VARCHAR(120) NULL", "", ""),
        ("email", "VARCHAR(160)", "", ""),
        ("phone", "VARCHAR(60) NULL", "", ""),
        ("country", "CHAR(3)", "", ""),
        ("created_at", "DATETIME", "", ""),
        ("notified_at", "DATETIME NULL", "", ""),
    ]),
    ("email_log", 780, 40, [
        ("id", "INT UNSIGNED", "PK", ""),
        ("booking_contact_id", "INT UNSIGNED", "", "FK"),
        ("file_name", "VARCHAR(190)", "", ""),
        ("created_at", "DATETIME", "", ""),
    ]),
]

# 關聯線：(起點表, 終點表, 說明) —— 以「一對多」表示
RELATIONS = [
    ("dining_module", "seating", "1 : n"),
    ("seating", "reservation", "1 : n"),
    ("competition_day", "reservation", "1 : n"),
    ("booking", "reservation", "1 : n"),
    ("booking_contact", "booking", "1 : n"),
    ("booking_contact", "email_log", "1 : n"),
]

WIDTH = 320
HEADER = 30
ROW = 20
PADDING = 8


def box_height(fields):
    return HEADER + len(fields) * ROW + PADDING


def build():
    positions = {}
    parts = []

    parts.append(
        '<svg xmlns="http://www.w3.org/2000/svg" width="1160" height="760" '
        'viewBox="0 0 1160 760" font-family="Segoe UI, Arial, sans-serif">'
    )
    parts.append('<rect width="1160" height="760" fill="#ffffff"/>')
    parts.append(
        '<text x="40" y="26" font-size="17" font-weight="bold" fill="#20394a">'
        'WSC2015 TP17 Server Side B - Restaurant Service (worldskill2015_moduleh)</text>'
    )
    parts.append(
        '<defs><marker id="arrow" markerWidth="10" markerHeight="10" refX="9" refY="3" '
        'orient="auto"><path d="M0,0 L0,6 L9,3 z" fill="#5b7c8d"/></marker></defs>'
    )

    for name, x, y, fields in TABLES:
        height = box_height(fields)
        positions[name] = (x, y, WIDTH, height)

        parts.append(
            f'<rect x="{x}" y="{y}" width="{WIDTH}" height="{height}" rx="4" '
            'fill="#ffffff" stroke="#5b7c8d" stroke-width="1.5"/>'
        )
        parts.append(
            f'<rect x="{x}" y="{y}" width="{WIDTH}" height="{HEADER}" rx="4" fill="#20394a"/>'
        )
        parts.append(
            f'<text x="{x + 10}" y="{y + 20}" font-size="13" font-weight="bold" '
            f'fill="#ffffff">{name}</text>'
        )

        for index, (column, datatype, pk, fk) in enumerate(fields):
            text_y = y + HEADER + ROW * index + 14
            marker = pk or fk
            colour = "#b8442d" if pk else ("#1c6ea4" if fk else "#333333")
            weight = "bold" if marker else "normal"

            parts.append(
                f'<text x="{x + 10}" y="{text_y}" font-size="11" fill="{colour}" '
                f'font-weight="{weight}">{column}</text>'
            )
            parts.append(
                f'<text x="{x + WIDTH - 10}" y="{text_y}" font-size="10" fill="#7d8b93" '
                f'text-anchor="end">{datatype}{" " + marker if marker else ""}</text>'
            )

    for source, target, label in RELATIONS:
        sx, sy, sw, sh = positions[source]
        tx, ty, tw, th = positions[target]

        if sx == tx:  # 上下排列：由下緣連到上緣
            x1, y1 = sx + sw / 2, sy + sh
            x2, y2 = tx + tw / 2, ty
        elif sx < tx:  # 左邊連到右邊
            x1, y1 = sx + sw, sy + sh / 2
            x2, y2 = tx, ty + th / 2
        else:          # 右邊連到左邊
            x1, y1 = sx, sy + sh / 2
            x2, y2 = tx + tw, ty + th / 2

        parts.append(
            f'<path d="M{x1},{y1} C{(x1 + x2) / 2},{y1} {(x1 + x2) / 2},{y2} {x2},{y2}" '
            'fill="none" stroke="#5b7c8d" stroke-width="1.4" marker-end="url(#arrow)"/>'
        )
        parts.append(
            f'<text x="{(x1 + x2) / 2}" y="{(y1 + y2) / 2 - 5}" font-size="10" '
            f'fill="#5b7c8d" text-anchor="middle">{label}</text>'
        )

    parts.append(
        '<text x="40" y="740" font-size="11" fill="#7d8b93">'
        'PK = primary key, FK = foreign key, UQ = unique key. '
        'Countries are a static list in the application code (see app/Core/Countries.php).</text>'
    )
    parts.append('</svg>')

    return "\n".join(parts)


if __name__ == "__main__":
    target = os.path.join(os.path.dirname(__file__), "..", "db-diagram.svg")

    with io.open(target, "w", encoding="utf-8") as handle:
        handle.write(build())

    print("db-diagram.svg written")
