-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost
-- 產生時間： 2025 年 06 月 14 日 05:56
-- 伺服器版本： 10.5.29-MariaDB-0+deb11u1
-- PHP 版本： 8.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `05_module_d`
--

-- --------------------------------------------------------

--
-- 資料表結構 `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `name` varchar(512) NOT NULL,
  `address` varchar(1024) NOT NULL,
  `phone` varchar(512) NOT NULL,
  `email` varchar(512) NOT NULL,
  `ownername` varchar(512) NOT NULL,
  `ownerphone` varchar(512) NOT NULL,
  `owneremail` varchar(512) NOT NULL,
  `contactname` varchar(512) NOT NULL,
  `contactphone` varchar(512) NOT NULL,
  `contactemail` varchar(512) NOT NULL,
  `deactivatetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `companyid` int(11) NOT NULL,
  `enname` varchar(512) NOT NULL,
  `frname` varchar(512) NOT NULL,
  `gtin` varchar(14) NOT NULL,
  `endescription` varchar(4096) NOT NULL,
  `frdescription` varchar(4096) NOT NULL,
  `brandname` varchar(512) NOT NULL,
  `country` varchar(128) NOT NULL,
  `grossweight` double NOT NULL,
  `contentweight` double NOT NULL,
  `weightunit` varchar(8) NOT NULL,
  `hidetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gtin` (`gtin`),
  ADD KEY `companyid` (`companyid`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`companyid`) REFERENCES `company` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
