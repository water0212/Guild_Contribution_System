-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-12-05 06:57:45
-- 伺服器版本： 10.4.28-MariaDB
-- PHP 版本： 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `guild_contribution_system`
--

-- --------------------------------------------------------

--
-- 資料表結構 `contribution_record`
--

CREATE TABLE `contribution_record` (
  `Record_Id` int(15) NOT NULL,
  `Member_Id` int(15) NOT NULL,
  `Mission_type` varchar(255) NOT NULL,
  `point` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `contribution_table`
--

CREATE TABLE `contribution_table` (
  `Mission_type` varchar(255) NOT NULL,
  `Text` varchar(255) NOT NULL,
  `point` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `contribution_table`
--

INSERT INTO `contribution_table` (`Mission_type`, `Text`, `point`) VALUES
('routine', 'Daily affairs', 10),
('transportation', 'Transporting goods', 15);

-- --------------------------------------------------------

--
-- 資料表結構 `member`
--

CREATE TABLE `member` (
  `Member_Id` int(15) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Contribution_sum` bigint(15) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `member`
--

INSERT INTO `member` (`Member_Id`, `Name`, `Contribution_sum`) VALUES
(1, 'John', 70),
(2, 'Elisia', 0);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `contribution_record`
--
ALTER TABLE `contribution_record`
  ADD PRIMARY KEY (`Record_Id`),
  ADD KEY `Mission_type` (`Mission_type`),
  ADD KEY `Member_Id` (`Member_Id`);

--
-- 資料表索引 `contribution_table`
--
ALTER TABLE `contribution_table`
  ADD PRIMARY KEY (`Mission_type`);

--
-- 資料表索引 `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`Member_Id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `contribution_record`
--
ALTER TABLE `contribution_record`
  MODIFY `Record_Id` int(15) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `member`
--
ALTER TABLE `member`
  MODIFY `Member_Id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `contribution_record`
--
ALTER TABLE `contribution_record`
  ADD CONSTRAINT `contribution_record_ibfk_1` FOREIGN KEY (`Mission_type`) REFERENCES `contribution_table` (`Mission_type`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contribution_record_ibfk_2` FOREIGN KEY (`Member_Id`) REFERENCES `member` (`Member_Id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
