<?php
/**
 * 模組 C - 資料庫安裝程式
 * 用法：瀏覽 api/install.php 建立資料庫與資料表；加上 ?reset=1 會先清空再重新匯入種子資料。
 */

declare(strict_types=1);

require __DIR__ . '/db.php';

header('Content-Type: text/plain; charset=utf-8');

$reset = isset($_GET['reset']);
$log = [];

try {
    // 1. 建立資料庫
    $server = dbServer();
    $server->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $log[] = '資料庫 ' . DB_NAME . ' 已就緒';

    $pdo = db();

    // 2. 建立資料表
    $pdo->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS parking_lots (
            id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
            name            VARCHAR(120)  NOT NULL,
            address         VARCHAR(200)  NOT NULL DEFAULT '',
            total_spaces    INT           NOT NULL DEFAULT 0,
            available_spaces INT          NOT NULL DEFAULT 0,
            latitude        DECIMAL(10,7) NOT NULL DEFAULT 0,
            longitude       DECIMAL(10,7) NOT NULL DEFAULT 0,
            updated_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    SQL);
    $log[] = '資料表 parking_lots 已就緒';

    $pdo->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS events (
            id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
            title       VARCHAR(160) NOT NULL,
            description VARCHAR(255) NOT NULL DEFAULT '',
            start_date  DATE         NOT NULL,
            end_date    DATE         NOT NULL,
            image_color CHAR(7)      NOT NULL DEFAULT '#1c3e60',
            image_url   VARCHAR(255) NOT NULL DEFAULT '',
            PRIMARY KEY (id),
            KEY idx_events_start (start_date),
            KEY idx_events_end (end_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    SQL);
    $log[] = '資料表 events 已就緒';

    $pdo->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS weather (
            id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
            forecast_date DATE        NOT NULL,
            condition_text VARCHAR(40) NOT NULL DEFAULT '',
            icon          VARCHAR(20) NOT NULL DEFAULT 'sunny',
            high_temp     TINYINT     NOT NULL DEFAULT 0,
            low_temp      TINYINT     NOT NULL DEFAULT 0,
            rain_chance   TINYINT UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_forecast_date (forecast_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    SQL);
    $log[] = '資料表 weather 已就緒';

    if ($reset) {
        $pdo->exec('DELETE FROM parking_lots');
        $pdo->exec('DELETE FROM events');
        $pdo->exec('DELETE FROM weather');
        $log[] = '已清空既有資料（reset=1）';
    }

    // 3. 匯入停車場種子資料
    if ((int) $pdo->query('SELECT COUNT(*) FROM parking_lots')->fetchColumn() === 0) {
        $lots = [
            ['南港展覽館一館地下停車場', '台北市南港區經貿二路 1 號', 620, 126, 25.0561000, 121.6178000],
            ['南港展覽館二館停車場', '台北市南港區經貿二路 2 號', 320, 74, 25.0581000, 121.6162000],
            ['南港車站轉乘停車場', '台北市南港區忠孝東路七段 380 號', 540, 212, 25.0526000, 121.6078000],
            ['經貿二路平面停車場', '台北市南港區經貿二路 188 號', 96, 38, 25.0595000, 121.6145000],
            ['中信金融園區停車場', '台北市南港區經貿二路 168 號', 260, 91, 25.0592000, 121.6157000],
            ['南港軟體園區停車場', '台北市南港區三重路 19-2 號', 180, 57, 25.0572000, 121.6127000],
            ['台北流行音樂中心停車場', '台北市南港區市民大道八段 99 號', 210, 145, 25.0512000, 121.5985000],
            ['玉成公園地下停車場', '台北市南港區中坡南路 55 號', 150, 22, 25.0448000, 121.5896000],
        ];

        $statement = $pdo->prepare(
            'INSERT INTO parking_lots (name, address, total_spaces, available_spaces, latitude, longitude)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        foreach ($lots as $lot) {
            $statement->execute($lot);
        }
        $log[] = '已匯入 ' . count($lots) . ' 筆停車場資料';
    }

    // 4. 匯入活動種子資料（跨越數個月，方便測試日期篩選與無限捲動）
    if ((int) $pdo->query('SELECT COUNT(*) FROM events')->fetchColumn() === 0) {
        $topics = [
            ['台北國際電腦展', '科技產業展示與新品發表', '#1c3e60'],
            ['智慧城市應用展', '城市服務、交通與資料應用', '#217453'],
            ['亞洲生技論壇', '生技新創、醫療器材與研討會', '#b3542f'],
            ['設計品牌週', '生活風格、設計選物與講座', '#8c5a2b'],
            ['國際旅遊展', '旅遊產品、航空與城市推廣', '#315f75'],
            ['電動車與能源展', '車用科技、充電與儲能方案', '#7c6c2d'],
            ['動漫創作博覽會', 'IP 展示、舞台活動與簽名會', '#7d4e8a'],
            ['台灣美食嘉年華', '地方料理與餐飲品牌展售', '#a05b08'],
            ['資安技術大會', '攻防演練、資安治理與雲端安全', '#305c86'],
            ['國際教育展', '海外留學、語言學習與學程諮詢', '#675f9b'],
            ['永續材料展', '循環材料、綠建材與 ESG 方案', '#2f7557'],
            ['運動健康產業展', '健身設備、運動科技與健康管理', '#b45f3b'],
            ['寵物用品博覽會', '生活用品、食品與品牌活動', '#7d6848'],
            ['食品加工設備展', '智慧製造、包裝與食品安全', '#596d34'],
            ['創新創業媒合日', '新創展示、投資媒合與講座', '#465f8d'],
            ['文具禮品採購展', '文創商品、商務禮品與通路採購', '#9b536c'],
            ['AI 應用開發日', '企業 AI、模型應用與工作坊', '#245b69'],
            ['國際家具家飾展', '居家設計、家具與照明品牌', '#6a5739'],
        ];

        $statement = $pdo->prepare(
            'INSERT INTO events (title, description, start_date, end_date, image_color)
             VALUES (?, ?, ?, ?, ?)'
        );

        // 從今天往前 20 天開始，每 3 天一檔活動，共 54 檔（足以觸發多次無限捲動）
        $cursor = new DateTimeImmutable('today -20 days');
        for ($index = 0; $index < 54; $index++) {
            [$title, $description, $color] = $topics[$index % count($topics)];
            $round = intdiv($index, count($topics)) + 1;
            $start = $cursor->add(new DateInterval('P' . ($index * 3) . 'D'));
            $end = $start->add(new DateInterval('P2D'));
            $statement->execute([
                sprintf('%s %s（第 %d 場）', $start->format('Y'), $title, $round),
                $description,
                $start->format('Y-m-d'),
                $end->format('Y-m-d'),
                $color,
            ]);
        }
        $log[] = '已匯入 54 筆活動資料';
    }

    // 5. 匯入天氣種子資料（自今日起 30 天，API 只取最近 7 天）
    if ((int) $pdo->query('SELECT COUNT(*) FROM weather')->fetchColumn() === 0) {
        $patterns = [
            ['晴時多雲', 'sunny', 31, 25, 10],
            ['多雲', 'cloudy', 30, 24, 20],
            ['短暫陣雨', 'rainy', 28, 23, 60],
            ['多雲時晴', 'cloudy', 29, 24, 30],
            ['晴朗', 'sunny', 32, 25, 10],
            ['午後雷陣雨', 'rainy', 30, 24, 70],
            ['陰天', 'cloudy', 27, 23, 40],
        ];

        $statement = $pdo->prepare(
            'INSERT INTO weather (forecast_date, condition_text, icon, high_temp, low_temp, rain_chance)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $today = new DateTimeImmutable('today');
        for ($day = 0; $day < 30; $day++) {
            [$text, $icon, $high, $low, $rain] = $patterns[$day % count($patterns)];
            $statement->execute([
                $today->add(new DateInterval('P' . $day . 'D'))->format('Y-m-d'),
                $text,
                $icon,
                $high - ($day % 3),
                $low - ($day % 2),
                $rain,
            ]);
        }
        $log[] = '已匯入 30 天天氣資料';
    }

    echo "安裝完成\n\n" . implode("\n", $log) . "\n";
} catch (Throwable $error) {
    http_response_code(500);
    echo "安裝失敗：" . $error->getMessage() . "\n";
}
