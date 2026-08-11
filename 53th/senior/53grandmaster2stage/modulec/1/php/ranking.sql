-- Star Battle 排行榜資料表
-- 使用方式：mysql -u root -p < ranking.sql
-- （register.php 也會在第一次執行時自動建立相同結構）

CREATE DATABASE IF NOT EXISTS `s53g2_starbattle`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `s53g2_starbattle`;

CREATE TABLE IF NOT EXISTS `ranking` (
    `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`  VARCHAR(50) NOT NULL,
    `time`  INT NOT NULL DEFAULT 0 COMMENT '飛行秒數',
    `score` INT NOT NULL DEFAULT 0 COMMENT '得分，可為負數',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
