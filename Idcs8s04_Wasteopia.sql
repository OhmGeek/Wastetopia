-- phpMyAdmin SQL Dump
-- version 4.0.10.17
-- https://www.phpmyadmin.net
--
-- Host: mysql.dur.ac.uk
-- Generation Time: Mar 01, 2017 at 12:54 PM
-- Server version: 5.1.39-community-log
-- PHP Version: 5.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Idcs8s04_Wasteopia`
--

-- --------------------------------------------------------

--
-- Table structure for table `Barcode`
--

CREATE TABLE IF NOT EXISTS `Barcode` (
  `Barcode` varchar(13) NOT NULL COMMENT 'Assumed that only 1D barcodes are being used, it is not clear whether 2D barcodes can be represented by an integer or not.',
  `Barcode_Type` varchar(20) NOT NULL COMMENT 'Assumed that only 1D barcodes are being used, it is not clear whether 2D barcodes can be represented by an integer or not.',
  `FK_Item_ItemID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Barcode`,`FK_Item_ItemID`,`Barcode_Type`),
  KEY `constraint_barcode_item` (`FK_Item_ItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Barcode`
--

INSERT INTO `Barcode` (`Barcode`, `Barcode_Type`, `FK_Item_ItemID`) VALUES
('03011454', 'EAN8', 23),
('01308228', 'EAN8', 37),
('5010549305554', 'EAN13', 39),
('01454468', 'EAN8', 45),
('5000436589341', 'EAN13', 46);

-- --------------------------------------------------------

--
-- Table structure for table `Category`
--

CREATE TABLE IF NOT EXISTS `Category` (
  `CategoryID` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `Category_Name` varchar(30) NOT NULL,
  PRIMARY KEY (`CategoryID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `Category`
--

INSERT INTO `Category` (`CategoryID`, `Category_Name`) VALUES
(1, 'Type'),
(2, 'State'),
(3, 'Dietary Requirement'),
(4, 'Content'),
(5, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `Conversation`
--

CREATE TABLE IF NOT EXISTS `Conversation` (
  `ConversationID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FK_User_ReceiverID` int(10) unsigned NOT NULL,
  `FK_Listing_ListingID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ConversationID`),
  UNIQUE KEY `index_conversation_listing` (`FK_Listing_ListingID`),
  UNIQUE KEY `index_conversation_receiver` (`FK_User_ReceiverID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `Conversation`
--

INSERT INTO `Conversation` (`ConversationID`, `FK_User_ReceiverID`, `FK_Listing_ListingID`) VALUES
(1, 20, 11),
(2, 15, 12),
(3, 14, 51),
(4, 16, 7),
(5, 18, 37);

-- --------------------------------------------------------

--
-- Table structure for table `Image`
--

CREATE TABLE IF NOT EXISTS `Image` (
  `ImageID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Image_URL` varchar(120) NOT NULL,
  PRIMARY KEY (`ImageID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=62 ;

--
-- Dumping data for table `Image`
--

INSERT INTO `Image` (`ImageID`, `Image_URL`) VALUES
(1, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/caterpillarcake1.jpg'),
(4, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/activia1.jpg'),
(5, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/activia2.jpg'),
(6, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/batteredfish1.jpg'),
(7, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/benandjerrys1.jpg'),
(8, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/broadbeans1.jpg'),
(9, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/carrots1.jpg'),
(10, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/crackers1.jpg'),
(11, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/crackers2.jpg'),
(12, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/crumbedham1.jpg'),
(13, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/cucumber1.jpg'),
(14, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/frozenpeas1.jpg'),
(15, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/haribostarmix1.jpg'),
(16, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/milk1.jpg'),
(17, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/oregano1.jpg'),
(18, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/parsnips1.jpg'),
(19, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/porkpies1.jpg'),
(20, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/sausagerolls1.jpg'),
(21, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/scotcheggs1.jpg'),
(22, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/softwhitebread1.jpg'),
(23, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/softwhitebread1.jpg'),
(24, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/whiterice1.jpg'),
(25, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/whiterice2.jpg'),
(26, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/whiterice3.jpg'),
(27, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/butternut_squash1.JPG'),
(28, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/butternut_squash2.JPG'),
(29, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/butternut_squash3.JPG'),
(30, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/butternut_squash4.JPG'),
(31, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/butternut_squash5.JPG'),
(32, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/butternut_squash6.JPG'),
(33, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/old_speckled_hen1.JPG'),
(34, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/old_speckled_hen2.JPG'),
(35, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/butternut_squash1.JPG'),
(36, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/butternut_squash2.JPG'),
(37, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/butternut_squash3.JPG'),
(38, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/butternut_squash4.JPG'),
(39, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/butternut_squash5.JPG'),
(40, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/butternut_squash6.JPG'),
(41, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/eggs1.JPG'),
(42, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/eggs2.JPG'),
(43, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/eggs3.JPG'),
(44, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/eggs4.JPG'),
(45, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/milk1.JPG'),
(46, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/milk2.JPG'),
(47, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/old_speckled_hen1.JPG'),
(48, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/old_speckled_hen2.JPG'),
(49, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/oregano2.JPG'),
(50, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/potatoes1.JPG'),
(51, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/rice1.JPG'),
(52, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/sausage_roll1.JPG'),
(53, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/yoghurt_blueberry1.JPG'),
(54, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/yoghurt_blueberry2.JPG'),
(55, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/yoghurt_blueberry3.JPG'),
(56, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/yoghurt_cereal1.JPG'),
(57, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/yoghurt_cereal2.JPG'),
(58, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/UserImages/yoghurt_rhubarb2.JPG'),
(59, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/butternut_squash1.jpg'),
(60, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/oldspeckledhen1.jpg'),
(61, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ItemImages/eggs1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `Item`
--

CREATE TABLE IF NOT EXISTS `Item` (
  `ItemID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Description` varchar(140) DEFAULT NULL,
  `Use_By` datetime DEFAULT NULL,
  PRIMARY KEY (`ItemID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

--
-- Dumping data for table `Item`
--

INSERT INTO `Item` (`ItemID`, `Description`, `Use_By`) VALUES
(1, 'Potatoes from Tesco', '2016-12-23 00:00:00'),
(2, 'Potatoes from Sainsburys ', '2016-12-17 00:00:00'),
(3, 'From the market', '2016-12-31 00:00:00'),
(4, 'Half a cucumber ', '2016-12-28 00:00:00'),
(5, 'Seems alright', '2017-01-19 00:00:00'),
(6, 'Yes I will look forward to meeting you then', '2017-02-10 00:00:00'),
(7, 'Unopened pack of two scotch eggs from Waitrose', '2017-09-14 00:00:00'),
(8, 'Small pork pie from Tesco', '2016-12-15 00:00:00'),
(9, '1 Sausage roll left from Tesco, frozen.', '2017-03-10 00:00:00'),
(10, 'Beans from my garden', '2017-07-14 00:00:00'),
(11, 'Beans from somewhere, seem alright really', '2017-05-10 00:00:00'),
(12, 'Frozen Peas', '2019-05-17 00:00:00'),
(13, NULL, '2017-10-14 00:00:00'),
(14, 'Orange things', '2017-12-13 00:00:00'),
(15, 'Orange things', '2017-07-13 00:00:00'),
(16, 'White things', '2017-05-18 00:00:00'),
(17, 'Good state, should be decent until Saturday probably', '2017-08-19 00:00:00'),
(18, 'Unopened ham from Asda', '2017-06-16 00:00:00'),
(19, 'unopened ham from Sainsbury''s, 12 slices', '2017-07-04 00:00:00'),
(20, 'Unopened ', '2017-07-01 00:00:00'),
(21, 'Halal rice from the chinese supermarket', '2017-11-03 00:00:00'),
(22, '2kg left of a 4kg bag, has been kept properly in a dry location', '2017-09-02 00:00:00'),
(23, 'unopened', '2017-05-03 00:00:00'),
(24, 'Half a loaf left, got a day or two left before it goes bad', '2018-02-01 00:00:00'),
(25, 'Unopened, kept well', '2017-03-01 00:00:00'),
(26, 'Unopened, slightly malformed however', '2017-04-05 00:00:00'),
(27, 'Unopened, face has fallen off', '2017-01-20 00:00:00'),
(28, 'Unopened, dropped but is perfectly edible', '2017-03-31 00:00:00'),
(29, 'Unopened ', '2017-12-08 00:00:00'),
(30, 'Unopened, but melted', '2017-08-10 00:00:00'),
(31, 'Melted at one point but is perfectly fine', '2018-06-20 00:00:00'),
(32, NULL, '2019-03-07 00:00:00'),
(33, 'Don''t really like it', '2017-10-11 00:00:00'),
(34, 'Need to go on a diet', '2019-03-13 00:00:00'),
(35, 'Perfectly fine, just have too many', '2018-01-18 00:00:00'),
(36, 'Good condition', '2017-02-10 00:00:00'),
(37, '', '2019-11-13 00:00:00'),
(38, 'Kept well, 4 cans left', '2018-04-13 00:00:00'),
(39, '7 cans left, one a bit bent', '2018-07-19 00:00:00'),
(40, 'Butternut Squash, perfect condition', '2017-05-12 00:00:00'),
(41, '2 left', '2017-01-03 00:00:00'),
(42, '2 left', '2017-01-12 00:00:00'),
(43, '2 left', '2017-01-05 00:00:00'),
(44, '4 left', '2017-01-03 00:00:00'),
(45, '6 eggs', '2017-01-04 00:00:00'),
(46, 'Most of the milk is left, kept well', '2016-12-27 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `ItemImage`
--

CREATE TABLE IF NOT EXISTS `ItemImage` (
  `FK_Image_ImageID` int(10) unsigned NOT NULL,
  `FK_Item_ItemID` int(10) unsigned NOT NULL,
  `Is_Default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`FK_Image_ImageID`,`FK_Item_ItemID`),
  KEY `constraint_itemimage_item` (`FK_Item_ItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ItemImage`
--

INSERT INTO `ItemImage` (`FK_Image_ImageID`, `FK_Item_ItemID`, `Is_Default`) VALUES
(1, 25, 0),
(1, 26, 0),
(1, 27, 0),
(1, 28, 0),
(4, 35, 0),
(4, 36, 0),
(6, 14, 0),
(7, 31, 0),
(7, 32, 0),
(7, 33, 0),
(7, 34, 0),
(8, 10, 0),
(8, 11, 0),
(9, 15, 0),
(10, 20, 0),
(11, 19, 0),
(12, 18, 0),
(13, 3, 0),
(13, 4, 0),
(13, 5, 0),
(14, 12, 0),
(14, 13, 0),
(15, 29, 0),
(15, 30, 0),
(16, 46, 0),
(17, 23, 0),
(18, 16, 0),
(18, 17, 0),
(19, 6, 0),
(19, 8, 0),
(20, 9, 0),
(21, 7, 0),
(22, 24, 0),
(23, 24, 0),
(24, 21, 0),
(25, 21, 0),
(26, 22, 0),
(27, 40, 0),
(28, 40, 0),
(29, 40, 0),
(30, 40, 0),
(31, 40, 0),
(32, 40, 0),
(33, 38, 0),
(34, 38, 0),
(35, 40, 0),
(36, 40, 0),
(37, 40, 0),
(38, 40, 0),
(39, 40, 0),
(40, 40, 0),
(41, 45, 0),
(42, 45, 0),
(43, 45, 0),
(44, 45, 0),
(45, 45, 0),
(46, 45, 0),
(47, 38, 0),
(48, 38, 0),
(49, 23, 0),
(50, 2, 0),
(51, 21, 0),
(52, 9, 0),
(53, 44, 0),
(54, 44, 0),
(55, 44, 0),
(56, 42, 0),
(57, 43, 0),
(58, 41, 0),
(59, 40, 0),
(60, 38, 0),
(60, 39, 0),
(61, 45, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ItemTag`
--

CREATE TABLE IF NOT EXISTS `ItemTag` (
  `FK_Item_ItemID` int(10) unsigned NOT NULL,
  `FK_Tag_TagID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Item_ItemID`,`FK_Tag_TagID`),
  KEY `constraint_itemtag_tag` (`FK_Tag_TagID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ItemTag`
--

INSERT INTO `ItemTag` (`FK_Item_ItemID`, `FK_Tag_TagID`) VALUES
(7, 2),
(9, 2),
(14, 2),
(19, 2),
(20, 2),
(22, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(31, 2),
(32, 2),
(33, 2),
(34, 2),
(42, 2),
(43, 2),
(7, 4),
(25, 4),
(26, 4),
(27, 4),
(28, 4),
(31, 4),
(32, 4),
(33, 4),
(34, 4),
(45, 4),
(25, 5),
(26, 5),
(27, 5),
(28, 5),
(31, 6),
(32, 6),
(33, 6),
(34, 6),
(9, 7),
(31, 7),
(32, 7),
(33, 7),
(34, 7),
(35, 7),
(36, 7),
(41, 7),
(42, 7),
(43, 7),
(44, 7),
(46, 7),
(14, 14),
(3, 15),
(4, 15),
(5, 15),
(35, 15),
(36, 15),
(41, 15),
(42, 15),
(43, 15),
(44, 15),
(1, 16),
(2, 16),
(10, 16),
(11, 16),
(12, 16),
(13, 16),
(15, 16),
(17, 16),
(22, 16),
(37, 16),
(23, 17),
(6, 18),
(8, 18),
(9, 18),
(14, 18),
(18, 18),
(25, 20),
(26, 20),
(27, 20),
(28, 20),
(29, 20),
(30, 20),
(31, 20),
(32, 20),
(33, 20),
(34, 20),
(6, 21),
(7, 21),
(8, 21),
(9, 21),
(18, 21),
(35, 21),
(36, 21),
(41, 21),
(42, 21),
(43, 21),
(44, 21),
(46, 21),
(9, 22),
(12, 22),
(13, 22),
(14, 22),
(24, 23),
(22, 24),
(31, 24),
(32, 24),
(33, 24),
(34, 24),
(22, 25),
(1, 26),
(2, 26),
(15, 26),
(17, 26),
(37, 26),
(1, 27),
(2, 27),
(3, 27),
(4, 27),
(5, 27),
(15, 27),
(37, 27),
(7, 28),
(9, 28),
(27, 29),
(28, 29),
(30, 29),
(1, 30),
(3, 30),
(10, 30),
(11, 30),
(37, 30),
(40, 30),
(2, 31),
(37, 33),
(38, 34),
(39, 34);

-- --------------------------------------------------------

--
-- Table structure for table `Listing`
--

CREATE TABLE IF NOT EXISTS `Listing` (
  `ListingID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FK_Location_LocationID` int(10) unsigned NOT NULL,
  `FK_Item_ItemID` int(10) unsigned NOT NULL,
  `FK_User_UserID` int(10) unsigned NOT NULL,
  `Quantity` int(10) unsigned NOT NULL,
  `Time_Of_Creation` datetime NOT NULL,
  PRIMARY KEY (`ListingID`),
  KEY `index_listing_location` (`FK_Location_LocationID`),
  KEY `index_listing_item` (`FK_Item_ItemID`),
  KEY `index_listing_user` (`FK_User_UserID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=57 ;

--
-- Dumping data for table `Listing`
--

INSERT INTO `Listing` (`ListingID`, `FK_Location_LocationID`, `FK_Item_ItemID`, `FK_User_UserID`, `Quantity`, `Time_Of_Creation`) VALUES
(3, 1, 1, 1, 1, '2016-12-23 00:00:00'),
(4, 2, 2, 2, 1, '2016-12-13 00:00:00'),
(5, 4, 3, 4, 1, '2016-12-23 00:00:00'),
(6, 1, 4, 1, 1, '2016-12-24 00:00:00'),
(7, 8, 5, 8, 1, '2016-12-25 00:00:00'),
(8, 9, 6, 9, 1, '2016-12-26 00:00:00'),
(9, 7, 7, 7, 1, '2016-12-27 00:00:00'),
(10, 6, 8, 6, 1, '2016-12-11 00:00:00'),
(11, 6, 9, 6, 1, '2016-12-28 00:00:00'),
(12, 7, 10, 7, 1, '2016-12-28 00:00:00'),
(13, 3, 11, 3, 1, '2016-12-29 00:00:00'),
(14, 4, 12, 4, 1, '2016-12-30 00:00:00'),
(15, 4, 13, 4, 1, '2016-12-29 00:00:00'),
(16, 6, 14, 6, 1, '2016-12-27 00:00:00'),
(17, 2, 15, 2, 1, '2016-12-30 00:00:00'),
(18, 2, 16, 2, 1, '2016-12-30 00:00:00'),
(19, 5, 17, 5, 1, '2016-12-30 00:00:00'),
(20, 2, 18, 2, 1, '2016-12-31 00:00:00'),
(21, 8, 19, 8, 1, '2016-12-31 00:00:00'),
(22, 3, 20, 3, 1, '2016-12-31 00:00:00'),
(23, 2, 21, 2, 1, '2017-01-01 00:00:00'),
(24, 6, 22, 6, 1, '2017-01-02 00:00:00'),
(25, 5, 23, 5, 1, '2017-01-03 00:00:00'),
(26, 8, 24, 8, 1, '2017-01-03 00:00:00'),
(27, 8, 25, 8, 1, '2017-01-04 00:00:00'),
(28, 9, 26, 9, 1, '2017-01-05 00:00:00'),
(29, 6, 27, 6, 1, '2017-01-06 00:00:00'),
(30, 3, 28, 3, 1, '2017-01-06 00:00:00'),
(31, 2, 29, 2, 1, '2017-01-07 00:00:00'),
(32, 7, 30, 7, 1, '2017-01-09 00:00:00'),
(33, 8, 31, 8, 1, '2017-01-08 00:00:00'),
(34, 9, 32, 9, 1, '2017-01-09 00:00:00'),
(35, 9, 33, 9, 1, '2017-01-10 00:00:00'),
(36, 3, 41, 3, 1, '2017-01-02 00:00:00'),
(37, 5, 42, 5, 1, '2017-01-08 00:00:00'),
(38, 8, 43, 8, 1, '2017-01-03 00:00:00'),
(39, 9, 44, 9, 1, '2017-01-01 00:00:00'),
(40, 2, 45, 2, 1, '2017-01-02 00:00:00'),
(41, 7, 46, 7, 1, '2016-12-25 00:00:00'),
(50, 2, 35, 2, 1, '2016-12-23 07:38:30'),
(51, 6, 35, 6, 1, '2016-12-31 05:46:20'),
(52, 8, 36, 8, 1, '2016-12-31 05:40:20'),
(53, 7, 37, 7, 1, '2016-12-23 18:12:59'),
(54, 3, 38, 3, 1, '2017-01-01 16:26:22'),
(55, 4, 39, 4, 1, '2017-01-01 06:48:32'),
(56, 2, 40, 2, 6, '2016-12-26 04:43:28');

-- --------------------------------------------------------

--
-- Table structure for table `ListingTransaction`
--

CREATE TABLE IF NOT EXISTS `ListingTransaction` (
  `FK_Listing_ListingID` int(10) unsigned NOT NULL,
  `FK_Transaction_TransactionID` int(10) unsigned NOT NULL,
  `Quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `Success` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`FK_Listing_ListingID`,`FK_Transaction_TransactionID`),
  KEY `fk_ListingTransaction_Listing1_idx` (`FK_Listing_ListingID`),
  KEY `fk_ListingTransaction_Transaction1_idx` (`FK_Transaction_TransactionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ListingTransaction`
--

INSERT INTO `ListingTransaction` (`FK_Listing_ListingID`, `FK_Transaction_TransactionID`, `Quantity`, `Success`) VALUES
(3, 1, 1, 1),
(4, 2, 1, 1),
(5, 3, 1, 1),
(6, 4, 1, 1),
(7, 5, 1, 1),
(8, 6, 1, 1),
(9, 7, 1, 1),
(10, 8, 1, 1),
(11, 9, 1, 1),
(12, 10, 1, 1),
(13, 11, 1, 1),
(14, 12, 1, 1),
(15, 13, 1, 1),
(16, 14, 1, 1),
(17, 15, 1, 1),
(18, 16, 1, 1),
(19, 17, 1, 1),
(20, 18, 1, 1),
(21, 19, 1, 1),
(22, 20, 1, 1),
(23, 21, 1, 1),
(24, 22, 1, 1),
(25, 23, 1, 1),
(26, 24, 1, 1),
(27, 25, 1, 1),
(28, 26, 1, 1),
(29, 27, 1, 1),
(30, 28, 1, 1),
(31, 29, 1, 1),
(31, 40, 1, 0),
(32, 30, 1, 1),
(33, 31, 1, 1),
(34, 32, 1, 1),
(35, 33, 1, 1),
(36, 34, 1, 1),
(37, 35, 1, 1),
(38, 36, 1, 1),
(39, 37, 1, 1),
(40, 38, 1, 1),
(41, 39, 1, 1),
(50, 40, 1, 0),
(50, 41, 1, 0),
(50, 42, 1, 0),
(51, 43, 1, 1),
(52, 44, 1, 1),
(53, 45, 1, 1),
(54, 46, 1, 1),
(55, 47, 1, 1),
(56, 48, 1, 0),
(56, 49, 1, 0),
(56, 50, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Location`
--

CREATE TABLE IF NOT EXISTS `Location` (
  `LocationID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(40) NOT NULL,
  `Post_Code` varchar(8) NOT NULL,
  PRIMARY KEY (`LocationID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `Location`
--

INSERT INTO `Location` (`LocationID`, `Name`, `Post_Code`) VALUES
(1, 'My House', 'DH1 4FG'),
(2, 'claypath', 'DH1 1QE'),
(3, 'Warsash', 'SO31 9JE'),
(4, 'Faraday Court', 'DH1 4FG'),
(5, 'Place', 'DH99 1NU'),
(6, 'Rubbish House', 'EH2 1LN'),
(7, 'Here', 'LE21 3EP'),
(8, 'Huntstanton', 'PE36 6BA'),
(9, 'Millbrook near enough', 'SO16 9AG');

-- --------------------------------------------------------

--
-- Table structure for table `Message`
--

CREATE TABLE IF NOT EXISTS `Message` (
  `MessageID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Read` tinyint(1) NOT NULL,
  `Content` text NOT NULL,
  `Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `FK_Conversation_ConversationID` int(11) unsigned NOT NULL,
  `Giver_Or_Receiver` tinyint(1) unsigned NOT NULL COMMENT '0 indicates giver, 1 indicates receiver',
  PRIMARY KEY (`MessageID`),
  KEY `index_message_conversation` (`FK_Conversation_ConversationID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

--
-- Dumping data for table `Message`
--

INSERT INTO `Message` (`MessageID`, `Read`, `Content`, `Time`, `FK_Conversation_ConversationID`, `Giver_Or_Receiver`) VALUES
(19, 1, 'Test message from user 2 to user 1\r\n', '2017-01-06 21:01:37', 1, 1),
(20, 1, 'Test response from user 1 to user 2', '2017-01-06 21:02:14', 2, 1),
(21, 1, 'Another respone from user 1 to user 2', '2017-01-06 21:02:17', 3, 0),
(22, 1, 'Third test resonse from user 1 to user 2', '2017-01-06 21:02:20', 3, 1),
(23, 1, 'Fourth test response from user 1 to user 2', '2017-01-06 21:02:22', 1, 0),
(24, 1, '5th response', '2017-01-06 21:02:25', 5, 0),
(25, 0, '6th response', '2017-01-06 21:02:35', 3, 0),
(26, 0, 'Final response', '2017-01-06 20:59:25', 2, 0),
(27, 0, 'This should clear', '2017-01-06 21:00:55', 5, 1),
(28, 0, 'What happens if I write a lot of text, it won\\''t stop will it ? How annoying is this gonna be to see who typed what', '2017-01-06 20:59:33', 4, 0),
(29, 1, 'Testing the polling', '2017-01-06 21:01:11', 2, 1),
(30, 1, 'The polling works well', '2017-01-06 21:01:59', 5, 1),
(31, 1, 'Testing newMessage polling', '2017-01-06 21:01:05', 4, 1),
(32, 1, 'Testing polling again', '2017-01-06 21:00:43', 3, 1),
(33, 1, 'New unseen message', '2017-01-06 21:01:02', 1, 1),
(34, 1, 'Testing from new user', '2017-01-06 21:01:00', 5, 1),
(35, 1, 'Testing new message again', '2017-01-06 21:00:05', 5, 0),
(36, 1, 'Reply to George', '2017-01-06 21:02:02', 2, 0),
(37, 1, 'Reply again', '2017-01-06 21:02:11', 5, 0),
(38, 1, 'test message', '2017-01-06 21:01:27', 1, 0),
(39, 1, 'other message', '2017-01-06 17:52:33', 2, 1),
(40, 1, 'message response', '2017-01-06 21:00:37', 1, 1),
(41, 1, 'message response thing from giver', '2017-01-06 21:02:05', 2, 0),
(42, 1, 'second response from giver', '2017-01-06 20:10:03', 1, 0),
(43, 1, 'response from giver', '2017-01-06 21:02:08', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Tag`
--

CREATE TABLE IF NOT EXISTS `Tag` (
  `TagID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(15) NOT NULL,
  `FK_Category_Category_ID` int(2) unsigned NOT NULL DEFAULT '0',
  `Description` varchar(140) DEFAULT NULL,
  PRIMARY KEY (`TagID`),
  KEY `index_tag_category` (`FK_Category_Category_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `Tag`
--

INSERT INTO `Tag` (`TagID`, `Name`, `FK_Category_Category_ID`, `Description`) VALUES
(1, 'Nuts', 4, 'May contain nuts'),
(2, 'Gluten', 4, 'May contain gluten'),
(3, 'Crustaceans', 4, 'May contain crustaceans e.g. prawns, lobster, crabs or crayfish'),
(4, 'Egg', 4, 'May contain eggs'),
(5, 'Peanuts', 4, 'May contain peanuts'),
(6, 'Soybeans', 4, 'May contain soybeans'),
(7, 'Dairy', 4, 'May contain milk products'),
(8, 'Celery', 4, 'May contain celery'),
(9, 'Mustard', 4, 'May contain mustard'),
(10, 'Sesame_Seeds', 4, 'May contains sesame seeds'),
(11, 'Sulphur_Dioxide', 4, 'May contain sulphur dioxide or other sulphites in significiant quantities'),
(12, 'Lupin', 4, 'May contain lupin'),
(13, 'Molluscs', 4, 'May contain molluscs e.g. mussels, clams, oysters, scallops, snails and squid'),
(14, 'Fish', 4, 'May contain fish'),
(15, 'Fruit', 1, 'This product is fruit'),
(16, 'Vegetable', 1, 'This product is a vegetable'),
(17, 'Other', 5, 'This product is not easily defined'),
(18, 'Meat', 1, 'This product contains meat'),
(20, 'Confectionery', 1, 'This product is confectionery'),
(21, 'Chilled', 2, 'Keep refrigerated'),
(22, 'Frozen', 2, 'Keep frozen, keep from thawing'),
(23, 'Bread', 1, 'A bread item'),
(24, 'Kosher', 3, 'This product conplies with all necessary requirements to be considered kosher'),
(25, 'Halal', 3, 'This product complies with all necessary requirements to be considered halal'),
(26, 'Vegetarian', 3, 'This product complies with all necessary requirements to be considered vegetarian'),
(27, 'Vegan', 3, 'This product complies with all necessary requirements to be considered vegan'),
(28, 'Unopened', 2, 'The item is as new in an unopened, undamaged state'),
(29, 'Damaged', 2, 'The item is damaged, having experienced some impact at some point. It is still in an edible state'),
(30, 'No UseByDate', 2, 'There is no expiration date associated or the use by date is unknown or uncertain'),
(31, 'Large Item', 2, 'The item is abnormally large, such that it could pose problems to anyone collecting the item'),
(32, 'Heavy Item', 2, 'The item is abnormally heavy, such that it could pose problems to an individual collecting it'),
(33, 'Recurring', 2, 'This item is not an individual item for a single sale, it is a type of item that will always be available '),
(34, 'Alcohol', 4, 'Contains alcohol, by UK law you must be at least 18 years old to purchase.'),
(35, 'Gluten-free', 3, 'This is gluten-free'),
(36, 'Lactose-free', 3, 'This does not contain MILK');

-- --------------------------------------------------------

--
-- Table structure for table `Transaction`
--

CREATE TABLE IF NOT EXISTS `Transaction` (
  `TransactionID` int(10) unsigned NOT NULL,
  `FK_User_UserID` int(10) unsigned NOT NULL,
  `Time_Of_Transaction` datetime DEFAULT NULL,
  PRIMARY KEY (`TransactionID`),
  KEY `index_transaction_user` (`FK_User_UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Transaction`
--

INSERT INTO `Transaction` (`TransactionID`, `FK_User_UserID`, `Time_Of_Transaction`) VALUES
(1, 10, '2017-01-04 06:44:20'),
(2, 13, '2016-12-31 08:43:09'),
(3, 11, '2016-12-26 00:00:00'),
(4, 12, '2017-01-06 00:00:00'),
(5, 16, '2017-01-17 00:00:00'),
(6, 18, '2017-01-09 00:00:00'),
(7, 20, '2017-01-07 00:00:00'),
(8, 15, '2017-01-03 00:00:00'),
(9, 14, '2017-01-02 00:00:00'),
(10, 16, '2017-01-01 00:00:00'),
(11, 16, '2017-01-09 00:00:00'),
(12, 14, '2017-01-09 00:00:00'),
(13, 10, '2017-01-01 00:00:00'),
(14, 14, '2017-01-04 00:00:00'),
(15, 15, '2017-01-05 00:00:00'),
(16, 17, '2017-01-01 00:00:00'),
(17, 16, '2017-01-07 00:00:00'),
(18, 14, '2016-12-31 00:00:00'),
(19, 10, '2017-01-11 00:00:00'),
(20, 11, '2017-01-13 00:00:00'),
(21, 12, '2017-01-14 00:00:00'),
(22, 13, '2017-01-08 00:00:00'),
(23, 18, '2017-01-16 00:00:00'),
(24, 17, '2017-01-11 00:00:00'),
(25, 20, '2017-01-20 00:00:00'),
(26, 18, '2017-01-23 00:00:00'),
(27, 16, '2017-01-08 00:00:00'),
(28, 13, '2017-01-08 00:00:00'),
(29, 18, '2017-01-13 09:10:41'),
(30, 11, '2017-01-20 04:41:20'),
(31, 18, '2017-01-16 00:00:00'),
(32, 12, '2017-01-27 06:45:29'),
(33, 13, '2017-01-25 04:52:22'),
(34, 16, '2017-01-04 00:00:00'),
(35, 20, '2017-01-27 05:42:25'),
(36, 13, '2017-01-12 21:51:46'),
(37, 12, '2017-01-12 17:59:34'),
(38, 19, '2017-01-06 00:00:00'),
(39, 20, '2017-01-07 00:00:00'),
(40, 13, '2017-01-08 00:00:00'),
(41, 17, '2017-01-05 00:00:00'),
(42, 12, '2017-01-01 00:00:00'),
(43, 19, '2017-01-14 00:00:00'),
(44, 15, '2017-01-06 00:00:00'),
(45, 10, '2017-01-07 00:00:00'),
(46, 14, '2017-01-02 00:00:00'),
(47, 18, '2017-01-05 00:00:00'),
(48, 13, '2017-01-11 00:00:00'),
(49, 11, '2017-01-19 00:00:00'),
(50, 20, '2017-01-08 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `UserID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Forename` varchar(30) NOT NULL,
  `Surname` varchar(30) NOT NULL,
  `Email_Address` varchar(60) NOT NULL,
  `Password_Hash` varchar(64) NOT NULL,
  `Salt` varchar(45) NOT NULL,
  `Number_Of_Ratings` int(11) unsigned NOT NULL DEFAULT '0',
  `Mean_Rating_Percent` float DEFAULT NULL,
  `Picture_URL` varchar(150) NOT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `User`
--

INSERT INTO `User` (`UserID`, `Forename`, `Surname`, `Email_Address`, `Password_Hash`, `Salt`, `Number_Of_Ratings`, `Mean_Rating_Percent`, `Picture_URL`) VALUES
(1, 'Jimmy', 'Valmer', 'cooper@coopertown.com', '0c8ed7673e8763c655b2e1de6eba83990a920e27bf5e04a0b7f35f1d3682fcd5', 'PPNnX8dVyuthf3JlpgjV', 1, 20, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/JimmyValmer.jpg'),
(2, 'Harold', 'Meep', 'meepthegreat@hotmail.com', '80a46fceac57468afe2dee4236e59f3143b45f9fb745bcc2b16eaf107ed7c745', '6Nr37uqulg8ink04B9K3', 70, 20, 'https://static-resource.np.community.playstation.net/avatar/WWS_J/J0009.png'),
(3, 'Arnie', 'Pye', 'arniepieinthesky@news.com', '2eb58006537ebcdfeafcb351370c00e8e45b0adf520024d5e1dfd43e5a727d9c', 'HrnGDyavATYZcECbeqgm', 20, 54, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/ArniePye.png'),
(4, 'Ike', 'Broflovski', 'ryan@superemail.org', '86f294d7b71ceb90b9b2eb3ded6eb48e390d97b78195ed6b395a49f55ede2dc6', 'QjUBNCc1XO3sgK826RVE', 30, 90, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/IkeBroflovski.jpg'),
(5, 'Scott', 'Malkinson', 'XXsuperEliteXX@gmai.me', 'e939e05ccdb6bb71b366e98afacce752c5c9a083b7a9c63c88d3698aca14a2cd', 'sYsDmwrAaNQzkwD5b3wV', 10, 45.987, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/ScottMalkinson.png'),
(6, 'Barney', 'Gumble', 'barney@gumborides.com', 'ec62672bac015fcb3c315dc179cfa70c9e2a851e517294c4ee8f4133e169b01c', 'uLcm3S2WoaO8BFuGMGeM', 4, 95, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/BarneyGumble.png'),
(7, 'Eleanor', 'Abernathy', 'catlover@tre.ma', '56dcb40da80a26faaa0ad3219d68d6f137aea207aedb14ce9a426fa34e6af3ad', 'vlhphpogLFYCsG6oQyvZ', 6, 90, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/crazy%20cat%20lady.jpg'),
(8, 'Beary', 'Bear', 'penisland@qwerty.co.uk', 'c64f39f92146202ee9c733258c81cd67f65cefdfb259aa2ddaef1c913878d805', 'NERRQedAdQkDJ6RlYHBm', 7, 100, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/BearyBear.png'),
(9, 'Bebe', 'Stevens', 'wall@sea.com', 'd1f532102b51d8e725d13db373432701bb98498bdfe20d1504b9ba439bd42e20', 'RN7oNnYeBvhOLQed99p2', 3, 23, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/BebeStevens.png'),
(10, 'Wendy', 'Testaburger', 'fishing@sea.tr', 'c3b26c16e2c739ac24e38bf18b7d2cd517f1a8073499111782bc6ba456d641b3', 'yeN9FK888dk42mFbuxq1', 9, 76, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/WendyTestaburger.jpg'),
(11, 'Marlon', 'Brando', 'marlin@nambla.org', 'fe299b08275092a99d83b94ea1a2b271952d7c8d4e02ab7a9594f4c5bfa425e9', 'eNnmPO8bgngxS1sGSac4', 3, 87, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/Dr._Mephesto_transparent.png'),
(12, 'Charles', 'Treeman', 'treecharles@place.io', '34f1d53a5acca9dd4e6fc1adbd26dda73b155adb2665702e9c0f92d93ff5eabb', '8ea4yvOLB9gfXpHtLAmj', 21, 93, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/tree.jpg'),
(13, 'Kenny', 'McCormick', 'poor@southpark.edu', '6c6738a8b00a0b7d3d6e55d8067a8c621a72e5d22e183f8a15b70e4f9cc605dc', 'B67xmZJzDydYFLAghvH3', 54, 82.87, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/Princess_Kenny.png'),
(14, 'Eric', 'Cartman', 'fatass@southpark.edu', 'a70b626c0a53a8b49c8aabb626942c24b4deb795b4964f930ab48b4a01565858', 'GLHYBcSBZJGQ7rzTyrbz', 23, 87.2, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/EricCartman.png'),
(15, 'Kyle', 'Broflovski', 'jew@southpark.edu', '925e3024bf680a331f7ce4fecc9d801e26ff57e0e48654923c7fe0c4ccdddda6', 'jRoPjSGLeXmrfeTrXiZo', 48, 99, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/KyleBroflovski.png'),
(16, 'Stan', 'Marsh', 'stan.darsh@southpark.edu', 'f4b1803be22e86b5e10c7a488c93ef8f90c42b17ee5039daaa5cc49d356ec081', '7ZF0QfLoNONIPR6WvENR', 9, 91, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/StanMarsh.PNG'),
(17, 'Butters', 'Stotch', 'grounded@southpark.edu', '5b9981ba86e79572252586497066bd4d0bd09da3f2f791cd52b777e50698b5dd', 'px49okcAS1NIBMqXPqXz', 8, 96, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/Butters_(Facebook).jpg'),
(18, 'Herbert', 'Garrison', 'president@usa.com', '08fa0da9f59d56992633e16fa8ce825653448e620ead0a25627e2d3cd4a7a3c5', 'Lr4Ay0VZfp7d67pwnHh4', 30, 57, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/MrGarrison.jpg'),
(19, 'PC', 'Principal', 'the.headmaster@southpark.edu', '67e319de8b51baec37ed8acf2f9a85b0e23908519764cdbf4d08a98c49197f65', 'tCMqGFANJbwfQ1muhZu6', 20, 43.6, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/PCPrincipal.jpg'),
(20, 'Tweak', 'Tweak', 'tweaked@coffee.sp', 'ac0cca79b3ba59f4c5b1934cd72895bf2d2c311b50377b3ecfea5169c93fb361', 'lSu43D4JTKHKpEUjSKqy', 9, 73.9828, 'http://community.dur.ac.uk/cs.seg04/Wastetopia/Database/Assets/Images/ProfileImages/Tweek_Tweak.png');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Barcode`
--
ALTER TABLE `Barcode`
  ADD CONSTRAINT `constraint_barcode_item` FOREIGN KEY (`FK_Item_ItemID`) REFERENCES `Item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Conversation`
--
ALTER TABLE `Conversation`
  ADD CONSTRAINT `constraint_conversation_listing` FOREIGN KEY (`FK_Listing_ListingID`) REFERENCES `Listing` (`ListingID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `constraint_conversation_receiver` FOREIGN KEY (`FK_User_ReceiverID`) REFERENCES `User` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ItemImage`
--
ALTER TABLE `ItemImage`
  ADD CONSTRAINT `constraint_itemimage_item` FOREIGN KEY (`FK_Item_ItemID`) REFERENCES `Item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `constraint_itemimage_image` FOREIGN KEY (`FK_Image_ImageID`) REFERENCES `Image` (`ImageID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ItemTag`
--
ALTER TABLE `ItemTag`
  ADD CONSTRAINT `constraint_itemtag_item` FOREIGN KEY (`FK_Item_ItemID`) REFERENCES `Item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `constraint_itemtag_tag` FOREIGN KEY (`FK_Tag_TagID`) REFERENCES `Tag` (`TagID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Listing`
--
ALTER TABLE `Listing`
  ADD CONSTRAINT `constraint_listing_user` FOREIGN KEY (`FK_User_UserID`) REFERENCES `User` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `constraint_listing_item` FOREIGN KEY (`FK_Item_ItemID`) REFERENCES `Item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `constraint_listing_location` FOREIGN KEY (`FK_Location_LocationID`) REFERENCES `Location` (`LocationID`) ON UPDATE CASCADE;

--
-- Constraints for table `ListingTransaction`
--
ALTER TABLE `ListingTransaction`
  ADD CONSTRAINT `constraint_listingtransaction_transaction` FOREIGN KEY (`FK_Transaction_TransactionID`) REFERENCES `Transaction` (`TransactionID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `constraint_listingtransaction_listing` FOREIGN KEY (`FK_Listing_ListingID`) REFERENCES `Listing` (`ListingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Message`
--
ALTER TABLE `Message`
  ADD CONSTRAINT `constraint_message_conversation` FOREIGN KEY (`FK_Conversation_ConversationID`) REFERENCES `Conversation` (`ConversationID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Tag`
--
ALTER TABLE `Tag`
  ADD CONSTRAINT `constraint_tag_category` FOREIGN KEY (`FK_Category_Category_ID`) REFERENCES `Category` (`CategoryID`) ON UPDATE CASCADE;

--
-- Constraints for table `Transaction`
--
ALTER TABLE `Transaction`
  ADD CONSTRAINT `constraint_transaction_user` FOREIGN KEY (`FK_User_UserID`) REFERENCES `User` (`UserID`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
