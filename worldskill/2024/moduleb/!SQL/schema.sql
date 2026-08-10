-- =====================================================================
-- WorldSkills 2024 TP17 Web Technologies - Module B Products Management
-- 資料庫結構與測試資料
--
-- 匯入方式（任選一種）：
--   mysql -u root < schema.sql
--   php ../!SQL/import.php          （見同資料夾的 import.php）
--
-- 設計重點：
--   * 第三正規化：公司的 owner / contact 抽成 company_contacts，
--     產品的多語系名稱與描述抽成 product_translations。
--   * 所有關聯都建立外鍵約束（FK constraints）。
--   * GTIN 以 UNIQUE 索引建立，兼具唯一性與查詢索引。
-- =====================================================================

DROP DATABASE IF EXISTS `worldskill2024_moduleb`;
CREATE DATABASE `worldskill2024_moduleb`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;
USE `worldskill2024_moduleb`;


-- ---------------------------------------------------------------------
-- 聯絡人角色（owner / contact）查表
-- ---------------------------------------------------------------------
CREATE TABLE `contact_roles` (
    `code`  VARCHAR(20)  NOT NULL COMMENT '角色代碼：owner=擁有者, contact=聯絡人',
    `label` VARCHAR(50)  NOT NULL COMMENT '角色顯示名稱',
    PRIMARY KEY (`code`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '公司聯絡人角色';

INSERT INTO `contact_roles` (`code`, `label`) VALUES
    ('owner',   'Owner'),
    ('contact', 'Contact person');


-- ---------------------------------------------------------------------
-- 語系查表（產品多語系資訊使用）
-- ---------------------------------------------------------------------
CREATE TABLE `locales` (
    `code`  CHAR(2)     NOT NULL COMMENT 'ISO 639-1 語言代碼',
    `label` VARCHAR(50) NOT NULL COMMENT '語言顯示名稱',
    PRIMARY KEY (`code`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '支援的語系';

INSERT INTO `locales` (`code`, `label`) VALUES
    ('en', 'English'),
    ('fr', 'Français');


-- ---------------------------------------------------------------------
-- 公司
-- ---------------------------------------------------------------------
CREATE TABLE `companies` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(150) NOT NULL COMMENT '公司名稱',
    `address`    VARCHAR(255) NOT NULL DEFAULT '' COMMENT '公司地址',
    `telephone`  VARCHAR(40)  NOT NULL DEFAULT '' COMMENT '公司電話',
    `email`      VARCHAR(190) NOT NULL DEFAULT '' COMMENT '公司電子郵件',
    `is_active`  TINYINT(1)   NOT NULL DEFAULT 1 COMMENT '1=啟用, 0=停用(deactivated)',
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_companies_is_active` (`is_active`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '公司主檔';


-- ---------------------------------------------------------------------
-- 公司聯絡資訊（擁有者與聯絡人共用同一張表，以 role_code 區分）
-- ---------------------------------------------------------------------
CREATE TABLE `company_contacts` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `company_id`    INT UNSIGNED NOT NULL,
    `role_code`     VARCHAR(20)  NOT NULL COMMENT '對應 contact_roles.code',
    `name`          VARCHAR(120) NOT NULL DEFAULT '' COMMENT '姓名',
    `mobile_number` VARCHAR(40)  NOT NULL DEFAULT '' COMMENT '手機號碼',
    `email`         VARCHAR(190) NOT NULL DEFAULT '' COMMENT '電子郵件',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_company_contacts_company_role` (`company_id`, `role_code`),
    KEY `idx_company_contacts_role` (`role_code`),
    CONSTRAINT `fk_company_contacts_company`
        FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_company_contacts_role`
        FOREIGN KEY (`role_code`) REFERENCES `contact_roles` (`code`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '公司擁有者與聯絡人';


-- ---------------------------------------------------------------------
-- 產品（與語言無關的欄位）
-- ---------------------------------------------------------------------
CREATE TABLE `products` (
    `id`                INT UNSIGNED          NOT NULL AUTO_INCREMENT,
    `company_id`        INT UNSIGNED          NOT NULL COMMENT '所屬公司',
    `gtin`              VARCHAR(14)           NOT NULL COMMENT '全球貿易項目編號，13 或 14 位數字',
    `brand`             VARCHAR(120)          NOT NULL DEFAULT '' COMMENT '品牌名稱',
    `country_of_origin` VARCHAR(100)          NOT NULL DEFAULT '' COMMENT '原產國',
    `gross_weight`      DECIMAL(10, 3) UNSIGNED DEFAULT NULL COMMENT '總重（含包裝）',
    `net_weight`        DECIMAL(10, 3) UNSIGNED DEFAULT NULL COMMENT '淨重',
    `weight_unit`       VARCHAR(10)           NOT NULL DEFAULT 'kg' COMMENT '重量單位，例如 kg / g / L',
    `image_path`        VARCHAR(255)          DEFAULT NULL COMMENT '產品圖片檔名，NULL 代表使用預設佔位圖',
    `is_hidden`         TINYINT(1)            NOT NULL DEFAULT 0 COMMENT '1=隱藏, 0=公開',
    `created_at`        DATETIME              NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME              NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    -- GTIN 建立唯一索引：同時滿足「唯一」與「已建立索引」兩項要求
    UNIQUE KEY `uq_products_gtin` (`gtin`),
    KEY `idx_products_company` (`company_id`),
    KEY `idx_products_is_hidden` (`is_hidden`),
    CONSTRAINT `fk_products_company`
        FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '產品主檔';


-- ---------------------------------------------------------------------
-- 產品多語系資訊（英文 / 法文，日後可直接新增語系資料列）
-- ---------------------------------------------------------------------
CREATE TABLE `product_translations` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id`  INT UNSIGNED NOT NULL,
    `locale_code` CHAR(2)      NOT NULL COMMENT '對應 locales.code',
    `name`        VARCHAR(200) NOT NULL DEFAULT '' COMMENT '該語系的產品名稱',
    `description` TEXT         DEFAULT NULL COMMENT '該語系的產品描述，可多行',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_product_translations_product_locale` (`product_id`, `locale_code`),
    KEY `idx_product_translations_locale` (`locale_code`),
    CONSTRAINT `fk_product_translations_product`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_product_translations_locale`
        FOREIGN KEY (`locale_code`) REFERENCES `locales` (`code`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '產品多語系資訊';


-- =====================================================================
-- 測試資料
-- =====================================================================

INSERT INTO `companies` (`id`, `name`, `address`, `telephone`, `email`, `is_active`) VALUES
    (1, 'Euro Expo', 'Boulevard de l''Europe, 69680 Chassieu, France', '+33 1 41 56 78 00', 'mail.customerservice.hdq@example.com', 1),
    (2, 'Maison Lumiere', '12 Rue de la Paix, 75002 Paris, France', '+33 1 42 60 30 30', 'contact@maisonlumiere.example.com', 1),
    (3, 'Vieux Moulin', '5 Chemin du Moulin, 33000 Bordeaux, France', '+33 5 56 44 22 11', 'info@vieuxmoulin.example.com', 0);

INSERT INTO `company_contacts` (`company_id`, `role_code`, `name`, `mobile_number`, `email`) VALUES
    (1, 'owner',   'Benjamin Smith', '+33 6 12 34 56 78', 'b.smith@example.com'),
    (1, 'contact', 'Marie Dubois',   '+33 6 98 76 54 32', 'm.dubois@example.com'),
    (2, 'owner',   'Claire Moreau',  '+33 6 11 22 33 44', 'c.moreau@example.com'),
    (2, 'contact', 'Lucas Bernard',  '+33 6 55 66 77 88', 'l.bernard@example.com'),
    (3, 'owner',   'Henri Lefevre',  '+33 6 21 43 65 87', 'h.lefevre@example.com'),
    (3, 'contact', 'Sophie Girard',  '+33 6 31 41 59 26', 's.girard@example.com');

INSERT INTO `products`
    (`id`, `company_id`, `gtin`, `brand`, `country_of_origin`, `gross_weight`, `net_weight`, `weight_unit`, `image_path`, `is_hidden`) VALUES
    ( 1, 1, '03000123456789', 'Green Orchard',  'France', 1.100, 1.000, 'L',  NULL, 0),
    ( 2, 1, '3000123456790',  'Green Orchard',  'France', 0.750, 0.700, 'L',  NULL, 0),
    ( 3, 1, '3000123456791',  'Le Fromager',    'France', 0.520, 0.500, 'kg', NULL, 0),
    ( 4, 1, '3000123456792',  'Le Fromager',    'France', 0.320, 0.300, 'kg', NULL, 0),
    ( 5, 1, '03000123456793', 'Boulangerie 12', 'France', 0.450, 0.400, 'kg', NULL, 0),
    ( 6, 1, '3000123456794',  'Boulangerie 12', 'France', 1.250, 1.200, 'kg', NULL, 1),
    ( 7, 2, '4000987654321',  'Lumiere Beaute', 'France', 0.260, 0.250, 'g',  NULL, 0),
    ( 8, 2, '04000987654322', 'Lumiere Beaute', 'France', 0.130, 0.120, 'g',  NULL, 0),
    ( 9, 2, '4000987654323',  'Provence Bio',   'France', 0.560, 0.500, 'L',  NULL, 0),
    (10, 2, '4000987654324',  'Provence Bio',   'France', 2.100, 2.000, 'kg', NULL, 0),
    (11, 3, '5000555444333',  'Moulin Ancien',  'France', 1.050, 1.000, 'kg', NULL, 1),
    (12, 3, '05000555444334', 'Moulin Ancien',  'France', 5.200, 5.000, 'kg', NULL, 1),
    (13, 1, '3000123456795',  'Green Orchard',  'France', 0.550, 0.500, 'L',  NULL, 0),
    (14, 1, '3000123456796',  'Le Fromager',    'France', 0.230, 0.200, 'kg', NULL, 0),
    (15, 2, '4000987654325',  'Provence Bio',   'France', 0.360, 0.330, 'L',  NULL, 0),
    (16, 2, '4000987654326',  'Lumiere Beaute', 'France', 0.110, 0.100, 'g',  NULL, 0);

INSERT INTO `product_translations` (`product_id`, `locale_code`, `name`, `description`) VALUES
    ( 1, 'en', 'Organic Apple Juice', 'Our organic apple juice is pressed from 100% fresh organic apples, with no added sugars or preservatives. Rich in vitamin C and antioxidants, it''s an ideal choice for your daily healthy diet.'),
    ( 1, 'fr', 'Jus de pomme biologique', 'Notre jus de pomme biologique est presse a partir de 100% de pommes biologiques fraiches, sans sucre ajoute ni conservateurs. Riche en vitamine C et en antioxydants, c''est le choix ideal pour votre alimentation quotidienne saine.'),
    ( 2, 'en', 'Organic Grape Juice', 'A smooth grape juice made from hand-picked grapes grown in the Rhone valley. No added sugar.'),
    ( 2, 'fr', 'Jus de raisin biologique', 'Un jus de raisin onctueux elabore a partir de raisins cueillis a la main dans la vallee du Rhone. Sans sucre ajoute.'),
    ( 3, 'en', 'Camembert de Normandie', 'A traditional soft cheese with a bloomy rind, matured for at least 21 days in the Normandy region.'),
    ( 3, 'fr', 'Camembert de Normandie', 'Un fromage a pate molle traditionnel a croute fleurie, affine au moins 21 jours en Normandie.'),
    ( 4, 'en', 'Goat Cheese Log', 'Fresh goat cheese log with a mild and creamy taste, perfect for salads and toasts.'),
    ( 4, 'fr', 'Buche de chevre', 'Buche de fromage de chevre frais au gout doux et cremeux, parfaite pour les salades et les toasts.'),
    ( 5, 'en', 'Butter Croissant Pack', 'Six all-butter croissants baked every morning with French AOP butter.'),
    ( 5, 'fr', 'Lot de croissants au beurre', 'Six croissants pur beurre cuits chaque matin avec du beurre AOP francais.'),
    ( 6, 'en', 'Country Sourdough Bread', 'A large sourdough loaf with a crispy crust, fermented for 24 hours.'),
    ( 6, 'fr', 'Pain de campagne au levain', 'Une grande miche au levain a la croute croustillante, fermentee pendant 24 heures.'),
    ( 7, 'en', 'Lavender Hand Cream', 'A nourishing hand cream with lavender essential oil from Provence.'),
    ( 7, 'fr', 'Creme pour les mains a la lavande', 'Une creme nourrissante pour les mains a l''huile essentielle de lavande de Provence.'),
    ( 8, 'en', 'Rose Lip Balm', 'A gentle lip balm made with organic rose wax and shea butter.'),
    ( 8, 'fr', 'Baume a levres a la rose', 'Un baume a levres doux a base de cire de rose biologique et de beurre de karite.'),
    ( 9, 'en', 'Extra Virgin Olive Oil', 'Cold pressed extra virgin olive oil from olives harvested in the south of France.'),
    ( 9, 'fr', 'Huile d''olive vierge extra', 'Huile d''olive vierge extra pressee a froid, issue d''olives recoltees dans le sud de la France.'),
    (10, 'en', 'Provence Herb Salt', 'Sea salt blended with thyme, rosemary and savory from Provence.'),
    (10, 'fr', 'Sel aux herbes de Provence', 'Sel marin melange au thym, au romarin et a la sarriette de Provence.'),
    (11, 'en', 'Stone Ground Wheat Flour', 'Wheat flour ground with a traditional stone mill, ideal for bread making.'),
    (11, 'fr', 'Farine de ble a la meule de pierre', 'Farine de ble moulue a la meule de pierre traditionnelle, ideale pour la panification.'),
    (12, 'en', 'Buckwheat Flour', 'Gluten free buckwheat flour, traditionally used for Breton galettes.'),
    (12, 'fr', 'Farine de sarrasin', 'Farine de sarrasin sans gluten, traditionnellement utilisee pour les galettes bretonnes.'),
    (13, 'en', 'Sparkling Pear Juice', 'A lightly sparkling pear juice, bottled without any added sugar or colouring.'),
    (13, 'fr', 'Jus de poire petillant', 'Un jus de poire legerement petillant, embouteille sans sucre ajoute ni colorant.'),
    (14, 'en', 'Roquefort AOP', 'A blue cheese matured in the natural caves of Roquefort-sur-Soulzon.'),
    (14, 'fr', 'Roquefort AOP', 'Un fromage bleu affine dans les caves naturelles de Roquefort-sur-Soulzon.'),
    (15, 'en', 'Sunflower Cooking Oil', 'Refined sunflower oil suitable for frying and baking.'),
    (15, 'fr', 'Huile de tournesol', 'Huile de tournesol raffinee, adaptee a la friture et a la patisserie.'),
    (16, 'en', 'Shea Hand Soap', 'A gentle hand soap enriched with shea butter and almond oil.'),
    (16, 'fr', 'Savon pour les mains au karite', 'Un savon doux pour les mains enrichi au beurre de karite et a l''huile d''amande.');
