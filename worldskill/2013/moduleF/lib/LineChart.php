<?php
/**
 * 以 GD 動態繪製折線圖（Performance by trade over the years）。
 *
 * 圖表完全在伺服器端產生，不使用題目提供的樣板圖片，
 * 內容包含：座標軸與刻度標籤、格線、每個國家一條顏色與符號皆不同的折線、
 * 資料點符號、平均值折線，以及標示 ISO 代碼與國名的圖例。
 */
class LineChart
{
    /** 繪圖區左側／上方留白 */
    const PLOT_LEFT = 56;
    const PLOT_TOP  = 30;

    /** 繪圖區寬高 */
    const PLOT_WIDTH  = 470;
    const PLOT_HEIGHT = 520;

    /** 圖例區寬度 */
    const LEGEND_WIDTH = 250;

    /** @var resource|GdImage */
    private $image;

    /** @var array 顏色索引表 */
    private $colors = array();

    /** @var string|null TrueType 字型路徑；找不到時退回 GD 內建點陣字型 */
    private $fontFile;

    /** 每個資料序列使用的顏色（RGB） */
    private static $palette = array(
        array(0x1F, 0x6F, 0xC4), array(0xC0, 0x39, 0x2B), array(0x8E, 0x44, 0xAD),
        array(0xE6, 0x7E, 0x22), array(0x16, 0xA0, 0x85), array(0x2C, 0x3E, 0x50),
        array(0xD3, 0x5D, 0x9B), array(0x7F, 0x8C, 0x8D), array(0x00, 0x8B, 0x8B),
        array(0xB7, 0x95, 0x0B), array(0x27, 0x60, 0x8B), array(0x9B, 0x2D, 0x1F),
        array(0x5D, 0x3F, 0xD3), array(0x1A, 0x7A, 0x3C), array(0xAA, 0x6A, 0x2E),
        array(0x4B, 0x5D, 0x8C), array(0xA0, 0x3E, 0x6B), array(0x0E, 0x6E, 0x8A),
        array(0x6B, 0x8E, 0x23), array(0x8B, 0x00, 0x8B),
    );

    /** 資料點符號的形狀，與顏色搭配使用 */
    private static $shapes = array('diamond', 'square', 'triangle', 'circle', 'cross');

    public function __construct()
    {
        $this->fontFile = $this->locateFont();
    }

    /**
     * 繪製圖表並直接輸出 PNG。
     *
     * @param string  $title   圖表標題
     * @param int[]   $years   X 軸年度
     * @param array   $series  array(ISO => array(year => points))
     * @param array   $average array(year => points)
     * @param array   $names   array(ISO => 國名)
     * @param string  $notice  無資料時顯示的訊息
     */
    public function render($title, array $years, array $series, array $average, array $names, $notice = '')
    {
        $legendRows = count($series) + 1;                       // 各國 + 平均
        $height     = max(self::PLOT_TOP + self::PLOT_HEIGHT + 46, 40 + $legendRows * 20 + 30);
        $width      = self::PLOT_LEFT + self::PLOT_WIDTH + self::LEGEND_WIDTH;

        $this->image = imagecreatetruecolor($width, $height);
        imageantialias($this->image, true);
        $this->allocateColors();
        imagefilledrectangle($this->image, 0, 0, $width, $height, $this->colors['background']);

        $this->drawText($title, self::PLOT_LEFT - 6, 4, 11, $this->colors['title']);

        list($minValue, $maxValue, $step) = $this->valueRange($series, $average);
        $this->drawGrid($years, $minValue, $maxValue, $step);

        if ($notice !== '') {
            $this->drawText($notice, self::PLOT_LEFT + 20, self::PLOT_TOP + self::PLOT_HEIGHT / 2,
                            10, $this->colors['notice']);
        }

        // 各國折線
        $index = 0;
        foreach ($series as $iso => $points) {
            $color = $this->seriesColor($index);
            $shape = $this->seriesShape($index);
            $this->drawSeries($years, $points, $minValue, $maxValue, $color, $shape);
            $this->drawEndLabel($years, $points, $minValue, $maxValue, $color, $iso);
            $index++;
        }

        // 平均值折線（固定為綠色三角形，與各國區隔）
        $this->drawSeries($years, $average, $minValue, $maxValue, $this->colors['average'], 'triangle', 3);

        $this->drawLegend($series, $average, $names);

        header('Content-Type: image/png');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        imagepng($this->image);
        imagedestroy($this->image);
    }

    /**
     * 錯誤情況下輸出一張說明用的圖片。
     */
    public function renderError($message)
    {
        $width  = self::PLOT_LEFT + self::PLOT_WIDTH + self::LEGEND_WIDTH;
        $height = 200;

        $this->image = imagecreatetruecolor($width, $height);
        $this->allocateColors();
        imagefilledrectangle($this->image, 0, 0, $width, $height, $this->colors['background']);
        imagerectangle($this->image, 0, 0, $width - 1, $height - 1, $this->colors['axis']);

        $this->drawText('The diagram could not be generated', 20, 30, 11, $this->colors['error']);
        foreach ($this->wrap($message, 90) as $offset => $line) {
            $this->drawText($line, 20, 60 + $offset * 18, 9, $this->colors['title']);
        }

        header('Content-Type: image/png');
        imagepng($this->image);
        imagedestroy($this->image);
    }

    // ------------------------------------------------------------------
    // 繪圖細節
    // ------------------------------------------------------------------

    /** 配置常用顏色 */
    private function allocateColors()
    {
        $this->colors['background'] = imagecolorallocate($this->image, 0xFF, 0xFF, 0xFF);
        $this->colors['axis']       = imagecolorallocate($this->image, 0x7F, 0x7F, 0x7F);
        $this->colors['grid']       = imagecolorallocate($this->image, 0xD9, 0xD9, 0xD9);
        $this->colors['title']      = imagecolorallocate($this->image, 0x33, 0x33, 0x33);
        $this->colors['label']      = imagecolorallocate($this->image, 0x55, 0x55, 0x55);
        $this->colors['average']    = imagecolorallocate($this->image, 0x77, 0xA6, 0x33);
        $this->colors['notice']     = imagecolorallocate($this->image, 0x99, 0x99, 0x99);
        $this->colors['error']      = imagecolorallocate($this->image, 0xC0, 0x39, 0x2B);
    }

    /** 取得第 n 個序列的顏色 */
    private function seriesColor($index)
    {
        $rgb = self::$palette[$index % count(self::$palette)];
        return imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]);
    }

    /**
     * 取得第 n 個序列的資料點符號。
     * 顏色每 20 個循環一次，符號則每循環一輪才換一種，
     * 因此「顏色 + 符號」的組合最多可提供 100 種不重複的樣式。
     */
    private function seriesShape($index)
    {
        $round = (int)floor($index / count(self::$palette));
        return self::$shapes[$round % count(self::$shapes)];
    }

    /**
     * 依資料決定 Y 軸範圍與刻度間距。
     *
     * @return array array(min, max, step)
     */
    private function valueRange(array $series, array $average)
    {
        $values = array();
        foreach ($series as $points) {
            foreach ($points as $value) {
                if ($value !== null) {
                    $values[] = (float)$value;
                }
            }
        }
        foreach ($average as $value) {
            if ($value !== null) {
                $values[] = (float)$value;
            }
        }

        if (empty($values)) {
            return array(360, 600, 20);   // 沒有資料時沿用樣板的預設範圍
        }

        $min = floor((min($values) - 10) / 10) * 10;
        $max = ceil((max($values) + 10) / 10) * 10;
        if ($max - $min < 60) {
            $max = $min + 60;
        }

        // 刻度數量控制在 10~24 條之間
        $step = 10;
        while (($max - $min) / $step > 24) {
            $step += 10;
        }

        return array($min, $max, $step);
    }

    /** 座標軸、格線與刻度標籤 */
    private function drawGrid(array $years, $min, $max, $step)
    {
        $left   = self::PLOT_LEFT;
        $top    = self::PLOT_TOP;
        $right  = $left + self::PLOT_WIDTH;
        $bottom = $top + self::PLOT_HEIGHT;

        for ($value = $min; $value <= $max; $value += $step) {
            $y = $this->valueToY($value, $min, $max);
            imageline($this->image, $left, $y, $right, $y, $this->colors['grid']);
            $this->drawText((string)$value, $left - 34, $y - 7, 8, $this->colors['label']);
        }

        // X 軸年度刻度
        foreach ($years as $index => $year) {
            $x = $this->yearToX($index, count($years));
            imageline($this->image, $x, $bottom, $x, $bottom + 4, $this->colors['axis']);
            $this->drawText((string)$year, $x - 14, $bottom + 8, 9, $this->colors['label']);
        }

        imageline($this->image, $left, $top, $left, $bottom, $this->colors['axis']);
        imageline($this->image, $left, $bottom, $right, $bottom, $this->colors['axis']);
    }

    /** 畫出一條折線與其資料點符號 */
    private function drawSeries(array $years, array $points, $min, $max, $color, $shape, $thickness = 2)
    {
        imagesetthickness($this->image, $thickness);

        $previous = null;
        foreach ($years as $index => $year) {
            $value = isset($points[$year]) ? $points[$year] : null;
            if ($value === null) {
                $previous = null;
                continue;
            }
            $x = $this->yearToX($index, count($years));
            $y = $this->valueToY($value, $min, $max);

            if ($previous !== null) {
                imageline($this->image, $previous[0], $previous[1], $x, $y, $color);
            }
            $previous = array($x, $y);
        }

        imagesetthickness($this->image, 1);

        // 符號畫在線之後，避免被線覆蓋
        foreach ($years as $index => $year) {
            $value = isset($points[$year]) ? $points[$year] : null;
            if ($value === null) {
                continue;
            }
            $this->drawMarker($this->yearToX($index, count($years)),
                              $this->valueToY($value, $min, $max), $color, $shape);
        }
    }

    /** 在折線末端標註 ISO 代碼，讓每條線更容易辨識 */
    private function drawEndLabel(array $years, array $points, $min, $max, $color, $iso)
    {
        for ($index = count($years) - 1; $index >= 0; $index--) {
            $year  = $years[$index];
            $value = isset($points[$year]) ? $points[$year] : null;
            if ($value === null) {
                continue;
            }
            $x = $this->yearToX($index, count($years));
            $y = $this->valueToY($value, $min, $max);
            $this->drawText($iso, $x + 7, $y - 7, 8, $color);
            return;
        }
    }

    /** 資料點符號 */
    private function drawMarker($x, $y, $color, $shape)
    {
        $size = 4;
        switch ($shape) {
            case 'square':
                imagefilledrectangle($this->image, $x - $size, $y - $size, $x + $size, $y + $size, $color);
                break;
            case 'triangle':
                imagefilledpolygon($this->image,
                    array($x, $y - $size - 1, $x + $size + 1, $y + $size, $x - $size - 1, $y + $size),
                    $color);
                break;
            case 'circle':
                imagefilledellipse($this->image, $x, $y, $size * 2 + 1, $size * 2 + 1, $color);
                break;
            case 'cross':
                imagesetthickness($this->image, 2);
                imageline($this->image, $x - $size, $y - $size, $x + $size, $y + $size, $color);
                imageline($this->image, $x - $size, $y + $size, $x + $size, $y - $size, $color);
                imagesetthickness($this->image, 1);
                break;
            case 'diamond':
            default:
                imagefilledpolygon($this->image,
                    array($x, $y - $size - 1, $x + $size + 1, $y, $x, $y + $size + 1, $x - $size - 1, $y),
                    $color);
                break;
        }
    }

    /** 圖例：色塊 + 符號 + 「ISO - 國名」 */
    private function drawLegend(array $series, array $average, array $names)
    {
        $x = self::PLOT_LEFT + self::PLOT_WIDTH + 24;
        $y = self::PLOT_TOP + 6;

        $index = 0;
        foreach ($series as $iso => $points) {
            $color = $this->seriesColor($index);
            $shape = $this->seriesShape($index);
            $this->drawLegendRow($x, $y, $color, $shape,
                $iso . ' - ' . (isset($names[$iso]) ? $names[$iso] : 'country not listed'));
            $y += 20;
            $index++;
        }

        $hasAverage = false;
        foreach ($average as $value) {
            if ($value !== null) {
                $hasAverage = true;
            }
        }
        if ($hasAverage) {
            $this->drawLegendRow($x, $y, $this->colors['average'], 'triangle', 'Average');
        }
    }

    /** 單一圖例列 */
    private function drawLegendRow($x, $y, $color, $shape, $label)
    {
        imagesetthickness($this->image, 2);
        imageline($this->image, $x, $y + 7, $x + 26, $y + 7, $color);
        imagesetthickness($this->image, 1);
        $this->drawMarker($x + 13, $y + 7, $color, $shape);
        $this->drawText($label, $x + 34, $y, 9, $this->colors['title']);
    }

    /** 年度 -> X 座標 */
    private function yearToX($index, $count)
    {
        if ($count <= 1) {
            return self::PLOT_LEFT + (int)(self::PLOT_WIDTH / 2);
        }
        $usable = self::PLOT_WIDTH - 80;
        return (int)(self::PLOT_LEFT + 40 + $usable * $index / ($count - 1));
    }

    /** 分數 -> Y 座標 */
    private function valueToY($value, $min, $max)
    {
        if ($max <= $min) {
            return self::PLOT_TOP + self::PLOT_HEIGHT;
        }
        $ratio = ($value - $min) / ($max - $min);
        return (int)(self::PLOT_TOP + self::PLOT_HEIGHT - $ratio * self::PLOT_HEIGHT);
    }

    /**
     * 文字輸出：有 TrueType 字型時使用 imagettftext，否則退回內建點陣字型。
     */
    private function drawText($text, $x, $y, $size, $color)
    {
        if ($this->fontFile !== null) {
            imagettftext($this->image, $size, 0, (int)$x, (int)($y + $size + 2), $color, $this->fontFile, $text);
            return;
        }
        $builtIn = $size >= 10 ? 4 : 3;
        imagestring($this->image, $builtIn, (int)$x, (int)$y, $text, $color);
    }

    /**
     * 尋找可用的 TrueType 字型；找不到就回傳 null（改用內建字型）。
     */
    private function locateFont()
    {
        $candidates = array(
            'C:/Windows/Fonts/arial.ttf',
            'C:/Windows/Fonts/tahoma.ttf',
            'C:/Windows/Fonts/segoeui.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        );
        foreach ($candidates as $path) {
            if (is_file($path) && function_exists('imagettftext')) {
                return $path;
            }
        }
        return null;
    }

    /** 將長訊息折成多行 */
    private function wrap($text, $width)
    {
        return explode("\n", wordwrap($text, $width, "\n", true));
    }
}
