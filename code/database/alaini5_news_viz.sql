-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 28, 2014 at 02:22 AM
-- Server version: 5.6.17-log
-- PHP Version: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `alaini5_news_viz`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `article_url` varchar(150) NOT NULL,
  `title` varchar(150) CHARACTER SET utf8 NOT NULL,
  `sample_text` text CHARACTER SET utf8 NOT NULL,
  `sample_pic` varchar(150) NOT NULL,
  PRIMARY KEY (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hit`
--

CREATE TABLE IF NOT EXISTS `hit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `time_visited` int(15) NOT NULL,
  `article_id` int(11) NOT NULL,
  `timezone` varchar(30) NOT NULL,
  `country` varchar(80) NOT NULL,
  `region` varchar(80) NOT NULL,
  `read_time` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `hit`
--

INSERT INTO `hit` (`id`, `ip`, `time_visited`, `article_id`, `timezone`, `country`, `region`, `read_time`) VALUES
(1, '96.255.182.39', 1406525806, 1, 'America/New_York', 'United States', 'Virginia', 0),
(2, '96.255.182.39', 1406525958, 1, 'America/New_York', 'United States', 'Virginia', 0),
(3, '96.255.182.39', 1406526025, 1, 'America/New_York', 'United States', 'Virginia', 0),
(4, '96.255.182.39', 1406526072, 1, 'America/New_York', 'United States', 'Virginia', 0),
(7, '76.119.208.108', 1406526563, 1, 'America/New_York', 'United States', 'Massachusetts', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
