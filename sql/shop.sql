-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: 2016 年 11 月 19 日 17:41
-- サーバのバージョン： 5.6.28
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `happytea`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `shop`
--

CREATE TABLE `shop` (
  `shop_id` int(11) NOT NULL,
  `shop_name` varchar(100) NOT NULL,
  `shop_address` text NOT NULL,
  `shop_tel` varchar(50) NOT NULL,
  `shop_owner` varchar(50) NOT NULL,
  `shop_account` int(11) DEFAULT NULL,
  `shop_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `shop`
--

INSERT INTO `shop` (`shop_id`, `shop_name`, `shop_address`, `shop_tel`, `shop_owner`, `shop_account`, `shop_type`) VALUES
(-1, '總店', '台北市天龍路888號', '0288888888', '1', 2, 1),
(1, '泰馬里店', '泰馬里隔壁', '0359878787', '老王', 2, 1),
(2, 'happyteaの家', '舟山河上', '3345678', '1', 1, 1),
(3, '麥當當', '肯德基隔壁', '3939889', '1', 1, 1),
(4, '悠悠堡', '悠悠大樓', '161716', '悠悠', 36, 1),
(6, '早安店', '早安大樓', '12345', '刻刻', 39, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shop`
--
ALTER TABLE `shop`
  ADD PRIMARY KEY (`shop_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shop`
--
ALTER TABLE `shop`
  MODIFY `shop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
