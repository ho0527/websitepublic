-- ---------------------------------------------------------------------------
-- WorldSkills 2019 TP17 Web Technologies / PHP and JS 模組（Task C - 後端）
--
-- 本檔為「額外新增」的資料表與初始資料。
-- 完整建置步驟（見 README.md）：
--   1. 先匯入主辦單位提供的 db-dump.sql（同資料夾內，未做任何修改）
--   2. 再匯入本檔 schema.sql
--
-- 規格要求：
--   * 不得修改主辦單位提供的既有資料表（organizers 的密碼欄位除外，
--     題目明確要求更新前兩位主辦者的登入密碼）
--   * 新增資料表必須符合第三正規化（3NF）
-- ---------------------------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------------
-- 活動評分（Event ratings）
--   參加者可對「參加過的活動」留言並評分
--   rating 為 1~5 的整數，1 最低、5 最高；同時記錄評分時間
--   （attendee_id, event_id）設為唯一鍵，避免同一人對同一活動重複評分，
--   其餘欄位皆完全相依於主鍵，符合 3NF
-- ---------------------------------------------------------------------------
DROP TABLE IF EXISTS `event_ratings`;
CREATE TABLE `event_ratings` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `attendee_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` mediumtext,
  `rated_at` datetime NOT NULL,
  UNIQUE KEY `uq_event_ratings_attendee_event` (`attendee_id`, `event_id`),
  KEY `fk_event_ratings_events` (`event_id`),
  CONSTRAINT `fk_event_ratings_attendees` FOREIGN KEY (`attendee_id`) REFERENCES `attendees` (`id`),
  CONSTRAINT `fk_event_ratings_events` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  CONSTRAINT `chk_event_ratings_rating` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------------------------
-- 議程評分（Session ratings）
--   除了活動評分之外，參加者也可對「參加過的議程」評分
--   規則與 event_ratings 相同
-- ---------------------------------------------------------------------------
DROP TABLE IF EXISTS `session_ratings`;
CREATE TABLE `session_ratings` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `attendee_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` mediumtext,
  `rated_at` datetime NOT NULL,
  UNIQUE KEY `uq_session_ratings_attendee_session` (`attendee_id`, `session_id`),
  KEY `fk_session_ratings_sessions` (`session_id`),
  CONSTRAINT `fk_session_ratings_attendees` FOREIGN KEY (`attendee_id`) REFERENCES `attendees` (`id`),
  CONSTRAINT `fk_session_ratings_sessions` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`),
  CONSTRAINT `chk_session_ratings_rating` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------------
-- 題目要求：更新前兩位主辦者的密碼，使其可用以下帳密登入
--   demo1@worldskills.org / demopass1
--   demo2@worldskills.org / demopass2
-- 密碼以 PHP password_hash()（bcrypt）產生，此處直接寫入對應的雜湊值。
-- ---------------------------------------------------------------------------
UPDATE `organizers`
   SET `password_hash` = '$2y$10$0xd5RkHFd9nyANoIv21t6eYG9lk2/G4mqg5XSLLXaJKkwgeEk1r8K'
 WHERE `email` = 'demo1@worldskills.org';

UPDATE `organizers`
   SET `password_hash` = '$2y$10$ycBjedxMuW6uVt7Gw//7PeIVSR53hYRa3.4p9d7hB2nO3zaaOoO..'
 WHERE `email` = 'demo2@worldskills.org';

-- ---------------------------------------------------------------------------
-- 範例評分資料（方便展示；不影響自動化測試使用的既有資料表）
-- ---------------------------------------------------------------------------
INSERT INTO `event_ratings` (`attendee_id`, `event_id`, `rating`, `comment`, `rated_at`) VALUES
(1, 1, 5, 'Great conference, very inspiring talks.', '2019-09-24 09:12:00'),
(2, 1, 4, 'Well organized, but the rooms were a bit crowded.', '2019-09-24 10:30:00'),
(3, 2, 3, 'Average content this year.', '2018-06-13 18:05:00');

INSERT INTO `session_ratings` (`attendee_id`, `session_id`, `rating`, `comment`, `rated_at`) VALUES
(1, 1, 5, 'Excellent speaker.', '2019-09-23 10:00:00'),
(1, 6, 4, 'Hands-on and practical.', '2019-09-23 17:30:00'),
(2, 5, 5, 'Loved the short format.', '2019-09-23 14:00:00');
