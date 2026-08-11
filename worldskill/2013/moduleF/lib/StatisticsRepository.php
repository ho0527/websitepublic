<?php
/**
 * 統計聚合：把閘道取回的原始資料轉換成畫面需要的形狀。
 */
class StatisticsRepository
{
    /** @var StatisticsGateway */
    private $gateway;

    public function __construct(StatisticsGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * 國家下拉選單資料（ISO => "ISO - Name"），依 ISO 排序。
     *
     * @return string[]
     */
    public function countryOptions()
    {
        $options = array();
        foreach ($this->gateway->countrys() as $country) {
            $options[$country['iso']] = $country['iso'] . ' - ' . $country['name'];
        }
        return $options;
    }

    /**
     * 職類下拉選單資料（number => "number - Name"），依職類編號排序。
     *
     * @return string[]
     */
    public function skillOptions()
    {
        $options = array();
        foreach ($this->gateway->skills() as $skill) {
            $options[$skill['number']] = $skill['number'] . ' - ' . $skill['name'];
        }
        return $options;
    }

    /**
     * 表格資料：指定國家在各職類、各年度取得的獎項。
     *
     * @param string $countryIso 國家 ISO 代碼
     * @return array[] 每筆 array('number','name','awards' => array(year => label))
     */
    public function medalsByCountry($countryIso)
    {
        $skillNames = $this->skillOptions();
        $years      = Config::years();
        $rows       = array();

        foreach ($this->gateway->results() as $result) {
            if ($result['country_iso'] !== $countryIso) {
                continue;
            }
            if (!in_array((int)$result['year'], $years, true)) {
                continue;
            }
            $award = self::awardLabel($result['award']);
            if ($award === '') {
                continue;   // 沒有獎項的成績不列入獎牌表
            }

            $number = $result['skill_number'];
            if (!isset($rows[$number])) {
                $rows[$number] = array(
                    'number' => $number,
                    'name'   => isset($skillNames[$number])
                        ? substr($skillNames[$number], strlen($number) + 3)
                        : '(unknown trade)',
                    'awards' => array_fill_keys($years, ''),
                );
            }
            $rows[$number]['awards'][(int)$result['year']] = $award;
        }

        uasort($rows, function ($a, $b) {
            return self::compareNumber($a['number'], $b['number']);
        });

        return array_values($rows);
    }

    /**
     * 圖表資料：指定職類（可選指定國家）在各年度的分數。
     *
     * @param string $skillNumber 職類編號
     * @param string $countryIso  國家 ISO；'all' 代表全部國家
     * @return array array('series' => array(iso => array(year => points)), 'average' => array(year => points))
     */
    public function performanceByTrade($skillNumber, $countryIso)
    {
        $years  = Config::years();
        $series = array();
        $sums   = array_fill_keys($years, 0.0);
        $counts = array_fill_keys($years, 0);

        foreach ($this->gateway->results() as $result) {
            if ($result['skill_number'] !== $skillNumber) {
                continue;
            }
            $year = (int)$result['year'];
            if (!in_array($year, $years, true)) {
                continue;
            }
            if ($result['points'] === '' || !is_numeric($result['points'])) {
                continue;
            }
            $points = (float)$result['points'];

            // 平均值一律以該職類的所有國家計算
            $sums[$year]   += $points;
            $counts[$year] += 1;

            if ($countryIso !== 'all' && $result['country_iso'] !== $countryIso) {
                continue;
            }
            $iso = $result['country_iso'];
            if (!isset($series[$iso])) {
                $series[$iso] = array_fill_keys($years, null);
            }
            $series[$iso][$year] = $points;
        }

        $average = array();
        foreach ($years as $year) {
            $average[$year] = $counts[$year] > 0 ? round($sums[$year] / $counts[$year], 1) : null;
        }

        ksort($series);

        return array('series' => $series, 'average' => $average);
    }

    /**
     * 獎項名稱標準化（原始資料大小寫不一致）。
     */
    public static function awardLabel($award)
    {
        $award = trim((string)$award);
        if ($award === '') {
            return '';
        }
        $labels = Config::awardLabels();
        $key    = strtoupper($award);
        return isset($labels[$key]) ? $labels[$key] : $award;
    }

    /**
     * 職類編號比較：純數字依數值，其餘排在後面。
     */
    private static function compareNumber($a, $b)
    {
        $a = (string)$a;
        $b = (string)$b;
        $aNumeric = ctype_digit($a);
        $bNumeric = ctype_digit($b);

        if ($aNumeric && $bNumeric) {
            return (int)$a - (int)$b;
        }
        if ($aNumeric !== $bNumeric) {
            return $aNumeric ? -1 : 1;
        }
        return strcmp($a, $b);
    }
}
