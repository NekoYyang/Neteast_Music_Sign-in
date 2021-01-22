-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 
-- 服务器版本: 5.5.53
-- PHP 版本: 7.1.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `music`
--

-- --------------------------------------------------------

--
-- 表的结构 `music_km`
--

CREATE TABLE IF NOT EXISTS `music_km` (
  `kid` int(11) NOT NULL AUTO_INCREMENT,
  `uin` bigint(12) DEFAULT NULL,
  `km` varchar(255) NOT NULL,
  `days` bigint(12) NOT NULL,
  `addDate` datetime NOT NULL,
  PRIMARY KEY (`kid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `music_root`
--

CREATE TABLE IF NOT EXISTS `music_root` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `webName` varchar(32) NOT NULL,
  `key` text NOT NULL,
  `notice` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `music_root`
--

INSERT INTO `music_root` (`id`, `username`, `password`, `webName`, `key`, `notice`) VALUES
(1, 'admin', '666666', '网易云任务', '123456', '测试公告');

-- --------------------------------------------------------

--
-- 表的结构 `music_user`
--

CREATE TABLE IF NOT EXISTS `music_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `uin` varchar(128) NOT NULL,
  `pwd` varchar(128) NOT NULL,
  `userId` varchar(64) NOT NULL,
  `musicu` varchar(521) NOT NULL,
  `csrf` varchar(521) NOT NULL,
  `cookieStatus` int(1) NOT NULL DEFAULT '1',
  `vipDate` date DEFAULT NULL,
  `executeDate` date DEFAULT NULL,
  `addDate` datetime NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
