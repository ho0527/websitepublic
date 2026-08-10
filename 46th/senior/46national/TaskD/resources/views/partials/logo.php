<?php
/**
 * 網站 LOGO。
 *
 * 以行內 SVG 呈現，文字部分才能透過 CSS 變數跟著深色／淺色佈景換色。
 */
?>
<svg viewBox="0 0 168 44" width="168" height="44" role="img" aria-label="列車訂票系統">
    <defs>
        <linearGradient id="railMark" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#4d8ae6"/>
            <stop offset="1" stop-color="#1d3f88"/>
        </linearGradient>
    </defs>

    <!-- 標誌：以速度線與車頭側面構成的圓角方塊 -->
    <rect x="0" y="2" width="40" height="40" rx="12" fill="url(#railMark)"/>
    <path d="M13 12h14a6 6 0 0 1 6 6v11a3 3 0 0 1-3 3H16a3 3 0 0 1-3-3V12z" fill="#ffffff"/>
    <path d="M16 16h11a3 3 0 0 1 3 3v4H16z" fill="#4d8ae6"/>
    <circle cx="19" cy="28" r="2.2" fill="#1d3f88"/>
    <circle cx="27" cy="28" r="2.2" fill="#1d3f88"/>
    <path d="M4 34h6M2 38h8" stroke="#ffffff" stroke-width="2" stroke-linecap="round" opacity="0.65"/>

    <!-- 文字：系統名稱，顏色由 CSS 依佈景決定 -->
    <text class="logo-title" x="50" y="22"
          font-family="'Noto Sans TC','Microsoft JhengHei',sans-serif" font-size="16" font-weight="700">列車訂票系統</text>
    <text class="logo-subtitle" x="50" y="36"
          font-family="'Segoe UI',sans-serif" font-size="9" font-weight="600" letter-spacing="2.4">RAIL BOOKING</text>
</svg>
