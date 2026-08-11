-- =====================================================================
-- WorldSkills 2015 TP17 Web Design / Server Side B（Module H）
-- Restaurant Service 訂位系統 資料庫結構與測試資料
-- 資料庫：worldskill2015_moduleh
-- 執行方式：mysql -u root < "!SQL/schema.sql"
-- =====================================================================

-- 確保用戶端以 utf8mb4 傳送資料（避免以 mysql CLI 匯入時中文/重音字元變成亂碼）
SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `worldskill2015_moduleh`;
CREATE DATABASE `worldskill2015_moduleh`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE `worldskill2015_moduleh`;

-- ---------------------------------------------------------------------
-- competition_day：競賽日（C1 - 04.08.2015 …），永遠四天，可由資料庫設定
-- ---------------------------------------------------------------------
CREATE TABLE `competition_day` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code`       VARCHAR(10)  NOT NULL COMMENT '競賽日代碼，例如 C1',
    `day_date`   DATE         NOT NULL COMMENT '實際日期',
    `sort_order` SMALLINT     NOT NULL DEFAULT 0 COMMENT '顯示排序',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_competition_day_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- dining_module：餐飲模組（Casual / Bar / Fine / Banquet），含說明文字
-- ---------------------------------------------------------------------
CREATE TABLE `dining_module` (
    `name`        VARCHAR(60)  NOT NULL COMMENT '模組名稱',
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `description` TEXT         NOT NULL COMMENT '首頁顯示的用餐體驗說明',
    `sort_order`  SMALLINT     NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_dining_module_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- seating：每個模組的場次（Casual Dining 一天兩場，其餘一天一場）
-- 總座位數 = seats_per_competitor * competitor_count
-- 單一國家上限 = 總座位數 - seats_per_competitor
-- ---------------------------------------------------------------------
CREATE TABLE `seating` (
    `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `dining_module_id`     INT UNSIGNED NOT NULL,
    `name`                 VARCHAR(40)  NOT NULL COMMENT '場次名稱，例如 Seating 1',
    `configuration`        VARCHAR(120) NOT NULL COMMENT '桌型設定，例如 1 table of 4 and 1 table of 2',
    `start_time`           TIME         NOT NULL,
    `end_time`             TIME         NOT NULL,
    `seats_per_competitor` SMALLINT UNSIGNED NOT NULL COMMENT '每位餐飲服務選手服務的座位數',
    `competitor_count`     SMALLINT UNSIGNED NOT NULL COMMENT '餐飲服務選手人數',
    `sort_order`           SMALLINT     NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_seating_module` (`dining_module_id`),
    CONSTRAINT `fk_seating_module` FOREIGN KEY (`dining_module_id`)
        REFERENCES `dining_module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- booking_contact：訂位聯絡人（個人訂位時同時也是賓客）
-- notified_at 用於「Send emails」避免對已全部定案的聯絡人重複寄送
-- ---------------------------------------------------------------------
CREATE TABLE `booking_contact` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`         VARCHAR(120) NOT NULL,
    `organization` VARCHAR(120) NULL,
    `email`        VARCHAR(160) NOT NULL,
    `phone`        VARCHAR(60)  NULL,
    `country`      CHAR(3)      NOT NULL COMMENT '國家代碼',
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `notified_at`  DATETIME     NULL COMMENT '最後一次產生通知信的時間',
    PRIMARY KEY (`id`),
    KEY `idx_booking_contact_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- booking：一次送出的訂位申請（產生一組 Booking No，例如 201500021）
-- ---------------------------------------------------------------------
CREATE TABLE `booking` (
    `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `booking_no`         VARCHAR(20)  NOT NULL COMMENT '對外顯示的訂位編號',
    `booking_contact_id` INT UNSIGNED NOT NULL,
    `booking_type`       ENUM('individual','group') NOT NULL DEFAULT 'individual',
    `created_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_booking_no` (`booking_no`),
    KEY `idx_booking_contact` (`booking_contact_id`),
    CONSTRAINT `fk_booking_contact` FOREIGN KEY (`booking_contact_id`)
        REFERENCES `booking_contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- reservation：每位賓客、每個場次一筆訂位
-- 狀態流程：requested -> confirmed / waitlisted / declined
-- needs_reschedule = 1 時，管理頁會顯示改期用的下拉選單
-- ---------------------------------------------------------------------
CREATE TABLE `reservation` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `booking_id`        INT UNSIGNED NOT NULL,
    `competition_day_id` INT UNSIGNED NOT NULL,
    `seating_id`        INT UNSIGNED NOT NULL,
    `guest_name`        VARCHAR(120) NULL COMMENT '賓客姓名，可留空（僅知國家）',
    `guest_country`     CHAR(3)      NOT NULL COMMENT '賓客國家，必填',
    `status`            ENUM('requested','confirmed','waitlisted','declined')
                        NOT NULL DEFAULT 'requested',
    `needs_reschedule`  TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_reservation_booking` (`booking_id`),
    KEY `idx_reservation_slot` (`competition_day_id`, `seating_id`, `status`),
    KEY `idx_reservation_country` (`competition_day_id`, `seating_id`, `guest_country`),
    KEY `idx_reservation_seating` (`seating_id`),
    CONSTRAINT `fk_reservation_booking` FOREIGN KEY (`booking_id`)
        REFERENCES `booking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_reservation_day` FOREIGN KEY (`competition_day_id`)
        REFERENCES `competition_day` (`id`) ON UPDATE CASCADE,
    CONSTRAINT `fk_reservation_seating` FOREIGN KEY (`seating_id`)
        REFERENCES `seating` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- email_log：Send emails 產生的通知檔紀錄
-- ---------------------------------------------------------------------
CREATE TABLE `email_log` (
    `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `booking_contact_id` INT UNSIGNED NOT NULL,
    `file_name`          VARCHAR(190) NOT NULL,
    `created_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_email_log_contact` (`booking_contact_id`),
    CONSTRAINT `fk_email_log_contact` FOREIGN KEY (`booking_contact_id`)
        REFERENCES `booking_contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 基礎設定資料（H9：競賽日與場次皆可於資料庫設定）
-- =====================================================================
INSERT INTO `competition_day` (`code`, `day_date`, `sort_order`) VALUES
    ('C1', '2015-08-04', 1),
    ('C2', '2015-08-05', 2),
    ('C3', '2015-08-06', 3),
    ('C4', '2015-08-07', 4);

INSERT INTO `dining_module` (`id`, `name`, `description`, `sort_order`) VALUES
    (1, 'Casual Dining',  'This dining is like a bistro/café. Casual service for sandwiches, cakes, cheese plates, salads, alcoholic and non-alcoholic beverages. Guests can choose from a limited menu.', 1),
    (2, 'Bar Service',    'Competitors will prepare international cocktails and serve with light snacks.', 2),
    (3, 'Fine Dining',    'This is formal dining with a four course set menu with alcoholic beverages. The service includes the waiter preparing all dishes at the table by flambé, carving or assembling. Appropriate for VIPs.', 3),
    (4, 'Banquet Dining', 'This is a three course set menu with coffee and alcoholic beverages in a banquet format.', 4);

INSERT INTO `seating`
    (`id`, `dining_module_id`, `name`, `configuration`, `start_time`, `end_time`,
     `seats_per_competitor`, `competitor_count`, `sort_order`) VALUES
    (1, 1, 'Seating 1', '1 table of 4 and 1 table of 2', '10:50:00', '12:30:00', 6, 6, 1),
    (2, 1, 'Seating 2', '1 table of 4 and 1 table of 2', '13:30:00', '14:40:00', 6, 6, 2),
    (3, 2, 'Seating',   '1 table of 6',                  '13:15:00', '14:45:00', 6, 6, 3),
    (4, 3, 'Seating',   '1 table of 4',                  '13:00:00', '15:15:00', 4, 6, 4),
    (5, 4, 'Seating',   '1 table of 6',                  '12:45:00', '15:00:00', 6, 6, 5);

-- =====================================================================
-- 測試資料（示範四種狀態、群組訂位與個人訂位）
-- =====================================================================
INSERT INTO `booking_contact` (`id`, `name`, `organization`, `email`, `phone`, `country`) VALUES
    (1, 'Sarah Rogers',  'WSI',              'sarah.rogers@worldskills.org', '+51 342 31 95 31', 'US'),
    (2, 'Jimmy Hendrix', NULL,               'jimmy.hendrix@example.com',    '+1 555 0100',      'US'),
    (3, 'Pierre Dupont', 'WorldSkills France','pierre.dupont@example.fr',    '+33 1 23 45 67 89','FR');

INSERT INTO `booking` (`id`, `booking_no`, `booking_contact_id`, `booking_type`) VALUES
    (1, '201500001', 2, 'individual'),
    (2, '201500002', 1, 'group'),
    (3, '201500003', 3, 'group');

-- Jimmy Hendrix（個人）：C2 Casual Dining Seating 1
INSERT INTO `reservation`
    (`booking_id`, `competition_day_id`, `seating_id`, `guest_name`, `guest_country`, `status`) VALUES
    (1, 2, 1, 'Jimmy Hendrix', 'US', 'requested');

-- Sarah Rogers（群組）：C2 Casual Dining Seating 1，四種狀態各一
INSERT INTO `reservation`
    (`booking_id`, `competition_day_id`, `seating_id`, `guest_name`, `guest_country`, `status`) VALUES
    (2, 2, 1, 'Simon Bartley',      'UK', 'confirmed'),
    (2, 2, 1, 'David Hoey',         'AU', 'confirmed'),
    (2, 2, 1, 'Jane Stokie',        'AU', 'requested'),
    (2, 2, 1, 'Brigitte Collins',   'AU', 'declined'),
    (2, 2, 1, 'Nieman Anders',      'AU', 'waitlisted'),
    (2, 4, 3, 'Jane Stokie',        'AU', 'confirmed'),
    (2, 4, 3, 'Brigitte Collins',   'AU', 'confirmed'),
    (2, 4, 3, 'Skills Emirates',    'AE', 'confirmed');

-- Pierre Dupont（群組）：C2 Bar Service，法國贊助商
INSERT INTO `reservation`
    (`booking_id`, `competition_day_id`, `seating_id`, `guest_name`, `guest_country`, `status`) VALUES
    (3, 2, 3, 'WS france sponsor', 'FR', 'confirmed'),
    (3, 2, 3, 'WS france sponsor', 'FR', 'confirmed'),
    (3, 2, 3, 'WS france sponsor', 'FR', 'confirmed'),
    (3, 2, 3, 'WS france sponsor', 'FR', 'requested'),
    (3, 2, 3, 'WS france sponsor', 'CA', 'requested'),
    (3, 2, 3, 'WS france sponsor', 'BE', 'waitlisted');
