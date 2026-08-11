-- =====================================================================
-- WorldSkills 2019 技能競賽 17 網頁技術 模組 B（CMS and Layout）
-- 資料庫結構與測試資料 — Kazan MuseumTour CMS
--
-- 匯入方式：
--   mysql -u root -h 127.0.0.1 < schema.sql
-- =====================================================================

DROP DATABASE IF EXISTS `worldskill2019_taskb`;
CREATE DATABASE `worldskill2019_taskb`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE `worldskill2019_taskb`;

-- ---------------------------------------------------------------------
-- 使用者：僅有 admin（管理員）與 editor（編輯）兩種角色
-- ---------------------------------------------------------------------
CREATE TABLE `users` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(60)  NOT NULL COMMENT '登入帳號',
    `password_hash` VARCHAR(255) NOT NULL COMMENT 'password_hash() 產生的雜湊',
    `display_name`  VARCHAR(120) NOT NULL COMMENT '顯示名稱',
    `email`         VARCHAR(190) NOT NULL DEFAULT '',
    `role`          ENUM('admin','editor') NOT NULL DEFAULT 'editor' COMMENT '角色權限',
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 網站設定：以 key/value 形式儲存（站名、標語、社群連結、聯絡表單訊息…）
-- ---------------------------------------------------------------------
CREATE TABLE `settings` (
    `setting_key`   VARCHAR(80)  NOT NULL COMMENT '設定名稱',
    `setting_value` TEXT         NOT NULL COMMENT '設定內容',
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 外掛（模擬 WordPress 外掛機制）：可於後台啟用／停用並設定選項
-- ---------------------------------------------------------------------
CREATE TABLE `plugins` (
    `slug`        VARCHAR(80)  NOT NULL COMMENT '外掛目錄名稱',
    `name`        VARCHAR(160) NOT NULL COMMENT '外掛顯示名稱',
    `description` VARCHAR(255) NOT NULL DEFAULT '',
    `is_active`   TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '1=已啟用',
    PRIMARY KEY (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 文章分類：Site Updates、Seasonal Events 以及每一個精選博物館
-- ---------------------------------------------------------------------
CREATE TABLE `categories` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(160) NOT NULL COMMENT '分類名稱',
    `slug`        VARCHAR(160) NOT NULL COMMENT '網址代稱',
    `description` VARCHAR(255) NOT NULL DEFAULT '',
    `sort_order`  SMALLINT     NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 博物館頁面：is_selected = 1 代表精選博物館（整頁背景版型）
-- category_id 對應該館專屬的新聞分類
-- ---------------------------------------------------------------------
CREATE TABLE `museums` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`          VARCHAR(190) NOT NULL COMMENT '博物館名稱',
    `slug`           VARCHAR(190) NOT NULL COMMENT '網址代稱（永久連結）',
    `excerpt`        VARCHAR(400) NOT NULL DEFAULT '' COMMENT '摘要',
    `content`        MEDIUMTEXT   NOT NULL COMMENT '內文（段落以空白行分隔）',
    `featured_image` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '精選圖片（版型背景／橫幅來源）',
    `gallery`        TEXT         NOT NULL COMMENT '相簿圖片，一行一張',
    `address`        VARCHAR(255) NOT NULL DEFAULT '',
    `opening_hours`  VARCHAR(255) NOT NULL DEFAULT '',
    `is_selected`    TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '1=精選博物館',
    `status`         ENUM('published','draft') NOT NULL DEFAULT 'published',
    `sort_order`     SMALLINT     NOT NULL DEFAULT 0,
    `category_id`    INT UNSIGNED NULL COMMENT '該館新聞所屬分類',
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_museums_slug` (`slug`),
    KEY `idx_museums_category` (`category_id`),
    CONSTRAINT `fk_museums_category`
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 新聞文章
-- ---------------------------------------------------------------------
CREATE TABLE `posts` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`          VARCHAR(190) NOT NULL,
    `slug`           VARCHAR(190) NOT NULL COMMENT '網址代稱',
    `excerpt`        VARCHAR(400) NOT NULL DEFAULT '',
    `content`        MEDIUMTEXT   NOT NULL,
    `featured_image` VARCHAR(255) NOT NULL DEFAULT '',
    `category_id`    INT UNSIGNED NOT NULL,
    `author_id`      INT UNSIGNED NULL,
    `status`         ENUM('published','draft') NOT NULL DEFAULT 'published',
    `published_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_posts_slug` (`slug`),
    KEY `idx_posts_category` (`category_id`),
    KEY `idx_posts_author` (`author_id`),
    CONSTRAINT `fk_posts_category`
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_posts_author`
        FOREIGN KEY (`author_id`) REFERENCES `users` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 安全性外掛：登入嘗試紀錄（成功與失敗都寫入，供封鎖與稽核使用）
-- ---------------------------------------------------------------------
CREATE TABLE `login_attempts` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`   VARCHAR(60)  NOT NULL,
    `ip_address` VARCHAR(45)  NOT NULL,
    `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
    `is_success` TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_attempts_ip_time` (`ip_address`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 測試資料
-- =====================================================================

-- 密碼皆以 password_hash(..., PASSWORD_DEFAULT) 產生：admin / admin、editor / editor
INSERT INTO `users` (`id`, `username`, `password_hash`, `display_name`, `email`, `role`) VALUES
(1, 'admin',  '$2y$10$3jzUQyrMVZYfh3YwWiRzV./U6VoDng1JXJVBCKGtYtDR3MQfRrbTC', 'Site Administrator', 'admin@example.com', 'admin'),
(2, 'editor', '$2y$10$DstliLBg3jJMsGmbC4ZCGOeNE3XpFsCH/QWlR6jWsTzPdds6qGHhy', 'Content Editor',     'editor@example.com', 'editor');

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `sort_order`) VALUES
(1, 'Site Updates',                              'site-updates',                              'Announcements about the Kazan MuseumTour website itself.', 1),
(2, 'Seasonal Events',                            'seasonal-events',                           'Exhibitions and events happening in a given season.',      2),
(3, 'Soviet Lifestyle Museum',                    'soviet-lifestyle-museum',                   'News from the Soviet Lifestyle Museum.',                   3),
(4, 'National Museum of the Republic of Tatarstan','national-museum-of-the-republic-of-tatarstan','News from the National Museum of the Republic of Tatarstan.', 4),
(5, 'Museum of National Culture',                 'museum-of-national-culture',                'News from the Museum of National Culture.',                5),
(6, 'Chak-Chak Museum',                           'chak-chak-museum',                          'News from the Chak-Chak Museum.',                          6);

INSERT INTO `museums`
(`id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `gallery`, `address`, `opening_hours`, `is_selected`, `status`, `sort_order`, `category_id`) VALUES
(1, 'Soviet Lifestyle Museum', 'soviet-lifestyle-museum',
 'A unique place where every visitor can take a trip into the forgotten Soviet past, inside an authentic Kazan communal apartment.',
 'A unique place where every visitor can take a trip into the forgotten Soviet past. The Soviet Lifestyle Museum is located in an authentic Soviet "kommunalka" or in English, "communal apartment" with brick walls, old wiring and cast-iron wall heaters.\n\nExhibitions here carry titles relevant to the museum: "USSR in Space", "Toys: Made in the USSR", "Bad Habits in the USSR", "The Rock and Roll Hall of Fame" and others. The museum''s main goal is to evoke a feeling of pleasant nostalgia and positive emotions among its visitors as they look through items of a bygone era.\n\nExhibits are not chosen based on any particular special value (although among them, some are quite valuable and even rare); the most important thing here is the history and the emotions that each item brings to visitors who may or may not have experienced the heyday of the not-so-distant past.',
 'uploads/museums/soviet-lifestyle-museum-1.jpg',
 'uploads/museums/soviet-lifestyle-museum-2.jpg\nuploads/museums/soviet-lifestyle-museum-3.jpg\nuploads/museums/soviet-lifestyle-museum-4.jpg',
 'Universitetskaya St 9, Kazan, Republic of Tatarstan', 'Daily 10:00 - 20:00', 1, 'published', 1, 3),
(2, 'National Museum of the Republic of Tatarstan', 'national-museum-of-the-republic-of-tatarstan',
 'The largest museum in Tatarstan, founded in 1894 and home to more than 800,000 items.',
 'The National Museum of the Republic of Tatarstan (NMRT) is the largest museum in Tatarstan. It was founded as a Kazan Town Scientific and Industrial Museum in 1894 and opened on April 5, 1895. The basis of the museum is a private collection of 40 thousand items of Andrei Fedorovich Likhachev (1832-90), a well-known regional archaeologist, numismatist and collector.\n\nWell-known scientists of Kazan University stood at the roots of the establishment of the museum and of the museum''s collections, such as A.A. Stuckenberg, N.P. Zagoskin, P.I. Krotov, N.F. Vysotsky and others. The museum occupies the former building of Gostiny Dvor (guest house), a monument of architecture and history of the Russian Federation and the Republic of Tatarstan.\n\nThere are over 800 thousand units in the museum''s collection. Now the museum has the following exhibitions: "Ancient History of Tatarstan", "Money, trade and trade routes in the Middle Ages", "Tatar Golden Treasures" and "Kazan Province in the XVIII century", plus regular temporary exhibitions.',
 'uploads/museums/national-museum-of-the-republic-of-tatarstan-1.jpg',
 'uploads/museums/national-museum-of-the-republic-of-tatarstan-2.jpg\nuploads/museums/national-museum-of-the-republic-of-tatarstan-3.jpg\nuploads/museums/national-museum-of-the-republic-of-tatarstan-4.png',
 'Kremlyovskaya St 2, Kazan, Republic of Tatarstan', 'Tue - Sun 10:00 - 18:00', 1, 'published', 2, 4),
(3, 'Museum of National Culture', 'museum-of-national-culture',
 'Sixteen permanent rooms dedicated to the past and present cultures of the world - the only museum of its kind in the region.',
 'The museum is housed in a colonial-era building that was named a national monument in 1931. After renovation it opened on 5 December 1965 as the Cultural Museum, with rooms dedicated to demonstrating cultural artifacts from around the world.\n\nThe museum has sixteen permanent display rooms and three rooms for temporary exhibits. Some of the rooms are dedicated to prehistoric cultures - cave paintings and implements associated with the origins of sedentary, agricultural societies. Other rooms are devoted to ancient Mesopotamia as well as ancient Greece and Rome. In the Age of Exploration room, items from the time of initial European contact with the Americas are on display.\n\nSince its founding, the museum has received over 12,000 pieces from around the world: textiles, glass objects, porcelain, photographs, arms, kimonos, masks, jewelry and sculptures. Many of these objects are originals and some are quite old.',
 'uploads/museums/museum-of-national-culture-1.jpg',
 'uploads/museums/museum-of-national-culture-2.jpg\nuploads/museums/museum-of-national-culture-3.jpg\nuploads/museums/museum-of-national-culture-4.jpg',
 'Pushkina St 86, Kazan, Republic of Tatarstan', 'Wed - Mon 10:00 - 18:00', 1, 'published', 3, 5),
(4, 'Chak-Chak Museum', 'chak-chak-museum',
 'Tea from a samovar, traditional Tatar sweets and the stories behind them, inside the historical Old Tatar Quarter.',
 'The Chak-Chak Museum is located within the historical Old Tatar Quarter complex. Inside the museum, in a homely atmosphere, guests can drink fragrant tea from a samovar and taste Tatar desserts such as the traditional chak-chak, baursak and kak-tosh made from almonds, prepared using the recipe of the Tatar enlightener Kayum Nasyri.\n\nWhile guests drink tea and sample desserts, the guides share stories and examples about typical ancient Tatar peoples'' everyday life, traditions, customs, and of course the secret to making the perfect chak-chak and other Tatar dishes.',
 'uploads/museums/chak-chak-museum-1.jpeg',
 'uploads/museums/chak-chak-museum-2.png\nuploads/museums/chak-chak-museum-3.jpg\nuploads/museums/chak-chak-museum-4.jpg',
 'Parizhskoy Kommuny St 18/1, Kazan, Republic of Tatarstan', 'Daily 10:00 - 18:00, by appointment', 1, 'published', 4, 6),
(5, 'Hermitage-Kazan', 'hermitage-kazan',
 'The first representative branch of the State Hermitage Museum in Russia, on the territory of the Kazan Kremlin.',
 'This exhibition centre on the territory of the Kazan Kremlin is the first representative branch of the State Hermitage Museum in Russia, outside of its original location in St Petersburg.\n\nThemed exhibitions with exhibits from the collection of the famous St Petersburg museum are regularly held here. The exposition changes approximately twice every year.\n\nIn addition to its exhibitions, the centre has a rich educational program filled with interactive classes, quests, lectures and master classes.',
 'uploads/museums/hermitage-1.png',
 'uploads/museums/hermitage-2.jpg\nuploads/museums/hermitage-3.jpg\nuploads/museums/hermitage-4.jpg\nuploads/museums/hermitage-5.jpg',
 'Kazan Kremlin, Kazan, Republic of Tatarstan', 'Tue - Sun 10:00 - 18:00', 0, 'published', 5, NULL),
(6, 'Museum of Islamic Culture', 'museum-of-islamic-culture',
 'Housed in the basement of the Kul Sharif Mosque, telling the story of Islam among the peoples of the Volga region.',
 'The Museum of Islamic Culture occupies the ground floor of the Kul Sharif Mosque inside the Kazan Kremlin. Its permanent exposition introduces visitors to the history of Islam among the Volga peoples, from the adoption of the faith by Volga Bulgaria to the religious life of Tatarstan today.\n\nThe collection includes rare manuscripts and printed Qurans, calligraphy, shamail paintings on glass, prayer rugs and everyday objects from Tatar Muslim households. Interactive displays explain the five pillars of Islam and the architecture of the mosque above.\n\nGuided tours are offered in Tatar, Russian and English and can be combined with a visit to the mosque prayer hall balcony.',
 'uploads/museums/museum-of-islamic-culture-1.jpg',
 'uploads/museums/museum-of-islamic-culture-2.jpg\nuploads/museums/museum-of-islamic-culture-3.jpg\nuploads/museums/museum-of-islamic-culture-4.jpg',
 'Kul Sharif Mosque, Kazan Kremlin, Kazan', 'Daily 09:00 - 19:30', 0, 'published', 6, NULL),
(7, 'State Museum of Fine Arts of the Republic of Tatarstan', 'state-museum-of-fine-arts-of-the-republic-of-tatarstan',
 'A mansion gallery holding the republic''s richest collection of Russian and Tatar painting, graphics and decorative art.',
 'The State Museum of Fine Arts of the Republic of Tatarstan keeps the largest art collection in the republic - more than 25,000 works of painting, graphics, sculpture and decorative art.\n\nThe main building is the former mansion of general Sandetsky, an elegant example of early twentieth century architecture surrounded by a small park. Halls are devoted to Russian art of the eighteenth and nineteenth centuries, to the avant-garde, and to the national school of Tatar painting.\n\nThe museum also runs a busy program of temporary exhibitions, concerts in the mansion hall and studio classes for children and families.',
 'uploads/museums/state-museum-of-fine-arts-of-the-republic-of-tatarstan-1.jpg',
 'uploads/museums/state-museum-of-fine-arts-of-the-republic-of-tatarstan-2.jpg\nuploads/museums/state-museum-of-fine-arts-of-the-republic-of-tatarstan-3.jpg\nuploads/museums/state-museum-of-fine-arts-of-the-republic-of-tatarstan-4.jpg',
 'Karla Marksa St 64, Kazan, Republic of Tatarstan', 'Tue - Sun 10:00 - 18:00', 0, 'published', 7, NULL),
(8, 'Russian Museum Exhibition Centre', 'russian-museum-exhibition-centre',
 'A Kazan showcase of the State Russian Museum, the world''s largest depository of Russian fine art.',
 'The State Russian Museum, formerly the Russian Museum of His Imperial Majesty Alexander III, is the world''s largest depository of Russian fine art. Its Kazan exhibition centre brings a rotating selection of that collection to the banks of the Volga.\n\nThe museum was established on April 13, 1895, upon the enthronement of Nicholas II to commemorate his father, Alexander III. Its original collection was composed of artworks taken from the Hermitage Museum, the Alexander Palace and the Imperial Academy of Arts.\n\nAfter the Russian Revolution of 1917, many private collections were nationalized and relocated to the Russian Museum; these included Kazimir Malevich''s Black Square.',
 'uploads/others/kzn-1.jpg',
 'uploads/others/kzn-2.jpg\nuploads/others/kzn-3.jpg\nuploads/others/kzn-4.jpg',
 'Kremlyovskaya St 35, Kazan, Republic of Tatarstan', 'Tue - Sun 10:00 - 18:00', 0, 'published', 8, NULL);

INSERT INTO `posts`
(`id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `category_id`, `author_id`, `status`, `published_at`) VALUES
(1, 'Kazan MuseumTour is online', 'kazan-museumtour-is-online',
 'Eight museums, one map and a single ticket desk - the new city museum portal is live.',
 'Kazan MuseumTour collects every museum worth a detour in the city into a single, mobile friendly guide. Opening hours, addresses and the current exhibition of each museum are now kept up to date by the museums themselves.\n\nStart from the museum list, pick the ones that fit your afternoon, and follow the news feed for exhibitions that open while you are in town.',
 'uploads/others/kazan_final_cover.jpg', 1, 1, 'published', '2019-08-01 09:00:00'),
(2, 'Audio guides now available in three languages', 'audio-guides-in-three-languages',
 'Tatar, Russian and English audio guides can be booked online before your visit.',
 'From this month every partner museum offers an audio guide in Tatar, Russian and English. Devices can be reserved together with your ticket and picked up at the entrance desk.\n\nVisitors travelling with children can also request the shorter family route, which covers the highlights of each museum in about forty minutes.',
 'uploads/others/kazan_final_s_1.jpg', 1, 2, 'published', '2019-08-04 10:30:00'),
(3, 'Winter Nights of Museums', 'winter-nights-of-museums',
 'One evening, eight museums, late openings and candle-lit courtyards across Kazan.',
 'During the Winter Nights of Museums all participating venues stay open until midnight. A single evening pass is valid in every museum on the list and includes the shuttle bus between the Kremlin and the Old Tatar Quarter.\n\nExpect live music in the courtyards, guided torch-lit tours and a winter market with hot tea and chak-chak.',
 'uploads/others/winter-in-kazan-russia-5-small.jpg', 2, 1, 'published', '2019-08-06 12:00:00'),
(4, 'Summer festival of Tatar crafts', 'summer-festival-of-tatar-crafts',
 'Leather mosaic, calligraphy and felt making workshops in the museum courtyards.',
 'For two weeks in summer, master craftsmen move their workshops into the museum courtyards. Visitors can try leather mosaic, Tatar calligraphy, felt making and traditional embroidery under supervision.\n\nAll workshops are free with a museum ticket; materials are provided and finished pieces can be taken home.',
 'uploads/others/red-bull-air-race-kazan-russia.jpg', 2, 2, 'published', '2019-08-07 15:00:00'),
(5, 'USSR in Space: a new permanent room', 'ussr-in-space-new-permanent-room',
 'Space toys, posters and a full size Sputnik model join the communal apartment.',
 'The Soviet Lifestyle Museum has turned its smallest room into a permanent exhibition about the space race as it was lived at home: newspaper cuttings, space themed toys, a rocket shaped vacuum flask and a full size Sputnik model hanging from the ceiling.\n\nThe room completes the tour of the communal apartment and is included in the standard ticket.',
 'uploads/museums/soviet-lifestyle-museum-2.jpg', 3, 1, 'published', '2019-08-05 11:00:00'),
(6, 'Evening tours of the communal apartment', 'evening-tours-of-the-communal-apartment',
 'Thursday evening tours told from the point of view of the apartment neighbours.',
 'Every Thursday the Soviet Lifestyle Museum runs an evening tour in which each room is presented by the story of the family that would have lived in it. The tour lasts one hour and ends with tea in the shared kitchen.\n\nGroups are limited to twelve people, so booking ahead is recommended.',
 'uploads/museums/soviet-lifestyle-museum-3.jpg', 3, 2, 'published', '2019-08-08 18:00:00'),
(7, 'Tatar Golden Treasures reopens after restoration', 'tatar-golden-treasures-reopens',
 'The gold room of the National Museum returns with new lighting and showcases.',
 'After six months of restoration work, the Tatar Golden Treasures hall of the National Museum of the Republic of Tatarstan is open again. New fibre optic lighting and low reflection showcases make the filigree work of the medieval jewellers visible in detail.\n\nThe hall is included in the general admission ticket to the museum.',
 'uploads/museums/national-museum-of-the-republic-of-tatarstan-2.jpg', 4, 1, 'published', '2019-08-03 09:30:00'),
(8, 'Free entry on the first Wednesday of the month', 'free-entry-first-wednesday',
 'The National Museum opens its permanent exhibitions free of charge once a month.',
 'On the first Wednesday of every month the permanent exhibitions of the National Museum of the Republic of Tatarstan can be visited free of charge. Temporary exhibitions keep their normal ticket price.\n\nThe offer applies to visitors of any nationality; no registration is required.',
 'uploads/museums/national-museum-of-the-republic-of-tatarstan-3.jpg', 4, 2, 'published', '2019-08-09 09:00:00'),
(9, 'New room dedicated to Oceania', 'new-room-dedicated-to-oceania',
 'Masks, tapa cloth and navigation charts from Samoa and New Ireland go on display.',
 'The Museum of National Culture opens its seventeenth display room with a collection dedicated to the cultures of Oceania. Masks, tapa cloth, shell jewellery and stick navigation charts from Samoa and New Ireland form the core of the display.\n\nMost pieces were donated by private collectors and are shown to the public for the first time.',
 'uploads/museums/museum-of-national-culture-2.jpg', 5, 1, 'published', '2019-08-02 14:00:00'),
(10, 'World cultures weekend for families', 'world-cultures-weekend-for-families',
 'Two days of hands-on activities for visitors aged six and above.',
 'The Museum of National Culture invites families to a weekend of hands-on activities: writing with a reed pen, printing textile patterns, grinding grain the way it was done before agriculture, and a treasure hunt through the sixteen permanent rooms.\n\nActivities run continuously from 11:00 to 17:00 on both days and are suitable for visitors aged six and above.',
 'uploads/museums/museum-of-national-culture-3.jpg', 5, 2, 'published', '2019-08-10 11:00:00'),
(11, 'Chak-chak master class every Saturday', 'chak-chak-master-class-every-saturday',
 'Learn the recipe of Kayum Nasyri and take your own chak-chak home.',
 'Every Saturday morning the Chak-Chak Museum runs a master class in which visitors prepare the traditional sweet themselves, following the recipe of the Tatar enlightener Kayum Nasyri.\n\nThe class lasts ninety minutes, ends with tea from the samovar, and each participant leaves with a box of their own chak-chak.',
 'uploads/museums/chak-chak-museum-2.png', 6, 1, 'published', '2019-08-06 10:00:00'),
(12, 'Tea ceremony room extended', 'tea-ceremony-room-extended',
 'The samovar room now seats twenty four guests for the storytelling tour.',
 'The Chak-Chak Museum has extended its tea room so that a full tour group can sit around the same table. The new benches and the tiled stove were built by craftsmen from the Old Tatar Quarter using traditional techniques.\n\nThe storytelling tour keeps its usual length of about an hour and now runs five times a day.',
 'uploads/museums/chak-chak-museum-3.jpg', 6, 2, 'published', '2019-08-09 16:00:00');

INSERT INTO `plugins` (`slug`, `name`, `description`, `is_active`) VALUES
('social-links',  'Footer Social Links',   '於頁尾顯示 Twitter / Facebook / Instagram 社群連結，連結網址可於後台修改。', 1),
('seo-toolkit',   'SEO Toolkit',           '產生 title / description / canonical / Open Graph 標籤、robots.txt 與 sitemap.xml。', 1),
('site-guardian', 'Site Guardian Security','記錄登入嘗試、封鎖暴力破解來源、加上安全性標頭。', 1),
('contact-form',  'Static Contact Form',   '產生指向 Formspree 的靜態聯絡表單，欄位與提示文字可於後台設定。', 1);

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_title',            'Kazan MuseumTour'),
('site_tagline',          'Discover the museums of Kazan, one story at a time'),
('site_description',      'Kazan MuseumTour is the visitor guide to the museums of Kazan: opening hours, exhibitions, seasonal events and news for tourists exploring the capital of Tatarstan.'),
('target_audience',       'Independent leisure travellers aged 25-55 visiting Kazan for two to four days. They speak English, plan on a phone while walking, and want to know quickly which museums are worth their limited time, when they open and what is on this week.'),
('copyright_owner',       'Kazan MuseumTour'),
('social_twitter',        'https://twitter.com/kazan'),
('social_facebook',       'https://facebook.com/kazan'),
('social_instagram',      'https://instagram.com/kazan'),
('contact_form_action',   'https://formspree.io/admin@example.com'),
('contact_email',         'admin@example.com'),
('contact_success_text',  'Thank you! Your message has been sent - we usually reply within one working day.'),
('contact_error_text',    'Sorry, your message could not be sent right now. Please check the fields and try again, or write to admin@example.com.'),
('contact_intro_text',    'Questions about tickets, opening hours or group visits? Send us a message.'),
('dashboard_widgets',     'at_a_glance,activity,quick_draft'),
('login_background',      'uploads/museums/national-museum-of-the-republic-of-tatarstan-1.jpg'),
('home_cover_image',      'uploads/others/kazan_final_cover.jpg'),
('home_gallery',          'uploads/others/kazan_final_s_1.jpg\nuploads/others/kazan_final_s_2.jpg\nuploads/others/kazan_final_s_3.jpg\nuploads/others/Destination-Kazan.jpg'),
('active_theme',          'Kazan_MuseumTour'),
('security_max_attempts', '5'),
('security_lockout_min',  '15'),
('posts_per_page',        '6');
