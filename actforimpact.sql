-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Nov 19, 2016 at 10:25 PM
-- Server version: 5.5.49-log
-- PHP Version: 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `actforimpact`
--

-- --------------------------------------------------------

--
-- Table structure for table `fsatoridingid`
--

CREATE TABLE IF NOT EXISTS `fsatoridingid` (
  `COL 1` varchar(3) DEFAULT NULL,
  `COL 2` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fsatoridingid`
--

INSERT INTO `fsatoridingid` (`COL 1`, `COL 2`) VALUES
('K1A', 1),
('K1B', 2),
('K1C', 3),
('K1E', 4),
('K1G', 5),
('K1H', 6),
('K1J', 7),
('K1L', 8),
('K1M', 9),
('K1N', 10),
('K1P', 11),
('K1R', 12),
('K1S', 13),
('K1T', 14),
('K1V', 15),
('K1W', 16),
('K1X', 17),
('K1Y', 18),
('K1Z', 19),
('K2A', 20),
('K2A', 21),
('K2B', 22),
('K2C', 23),
('K2E', 24),
('K2G', 25),
('K2H', 26),
('K2H', 27),
('K2J', 28),
('K2K', 29),
('K2K', 30),
('K2L', 31),
('K2M', 32),
('K2P', 33),
('K2R', 34),
('K2R', 35),
('K2S', 36),
('K2T', 37),
('K2T', 38),
('K2V', 39),
('K2W', 40),
('K4A', 41),
('K4B', 42),
('K4C', 43),
('K4M', 44),
('K4P', 45),
('K1T', 46),
('K1V', 47),
('K1X', 48),
('K2L', 49),
('K2M', 50),
('K2S', 51),
('K4M', 52),
('K4P', 53),
('K4B', 54),
('K4C', 55),
('K2K', 56),
('K2T', 57),
('K2V', 58),
('K2W', 59),
('K2E', 60),
('K2G', 61),
('K2H', 62),
('K2J', 63),
('K2L', 64),
('K2M', 65),
('K2R', 66),
('K2V', 67),
('K1B', 68),
('K1C', 69),
('K1E', 70),
('K1J', 71),
('K1W', 72),
('K4A', 73),
('K4B', 74),
('K4C', 75),
('K1A', 76),
('K1P', 77),
('K1R', 78),
('K1S', 79),
('K1Y', 80),
('K1Z', 81),
('K2A', 82),
('K2C', 83),
('K2P', 84),
('K1B', 85),
('K1G', 86),
('K1H', 87),
('K1T', 88),
('K1V', 89),
('K1A', 90),
('K1B', 91),
('K1J', 92),
('K1L', 93),
('K1M', 94),
('K1N', 95),
('K1Z', 96),
('K2A', 97),
('K2B', 98),
('K2C', 99),
('K2E', 100),
('K2G', 101),
('K2H', 102),
('K2K', 103),
('K2L', 104),
('K2M', 105),
('K2M', 106),
('K2S', 107),
('K2T', 108),
('K2V', 109),
('K2W', 110),
('K4B', 111),
('K4C', 112),
('K1T', 113),
('K1V', 114),
('K1X', 115),
('K2E', 116),
('K2G', 117),
('K2H', 118),
('K2J', 119),
('K2L', 120),
('K2L', 121),
('K2M', 122),
('K2M', 123),
('K2R', 124),
('K2V', 125),
('K4M', 126),
('K4P', 127),
('K1B', 128),
('K1A', 129),
('K1P', 130),
('K1R', 131),
('K1S', 132),
('K1Y', 133),
('K1Z', 134),
('K2A', 135),
('K2C', 136),
('K2P', 137),
('K1C', 138),
('K1E', 139),
('K1W', 140),
('K4A', 141),
('K4B', 142),
('K4C', 143),
('K1B', 144),
('K1G', 145),
('K1H', 146),
('K1T', 147),
('K1V', 148),
('K1A', 149),
('K1J', 150),
('K1J', 151),
('K1L', 152),
('K1M', 153),
('K1N', 154),
('K1Z', 155),
('K2B', 156),
('K2C', 157),
('K2E', 158),
('K2H', 159),
('K2K', 160);

-- --------------------------------------------------------

--
-- Table structure for table `grouptypes`
--

CREATE TABLE IF NOT EXISTS `grouptypes` (
  `no` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  `typeid` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `grouptypes`
--

INSERT INTO `grouptypes` (`no`, `type`, `typeid`) VALUES
(1, 'muncipal', 1),
(2, 'provincial', 2),
(3, 'federal', 3);

-- --------------------------------------------------------

--
-- Table structure for table `ridings`
--

CREATE TABLE IF NOT EXISTS `ridings` (
  `ridingid` int(11) NOT NULL,
  `ridingtype` int(1) DEFAULT NULL,
  `ridingname` varchar(30) DEFAULT NULL,
  `contactname` varchar(24) DEFAULT NULL,
  `contactemail` varchar(35) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ridings`
--

INSERT INTO `ridings` (`ridingid`, `ridingtype`, `ridingname`, `contactname`, `contactemail`) VALUES
(1, 1, '14', 'Catherine McKenney', 'catherine.mckenney@ottawa.ca'),
(2, 1, '11', 'Tim Tierney', 'tim.tierney@ottawa.ca'),
(3, 1, '2', 'Jody Mitic', 'jody.mitic@ottawa.ca'),
(4, 1, '1', 'Bob Monette', 'Bob.Monette@ottawa.ca'),
(5, 1, '10', 'Diane Deans', 'diane.deans@ottawa.ca'),
(6, 1, '18', 'Jean Cloutier', 'jean.cloutier@ottawa.ca'),
(7, 1, '11', 'Tim Tierney', 'tim.tierney@ottawa.ca'),
(8, 1, '12', 'Mathieu Fleury', 'mathieu.fleury@ottawa.ca'),
(9, 1, '13', 'Tobi Nussbaum', 'tobi.nussbaum@ottawa.ca'),
(10, 1, '12', 'Mathieu Fleury', 'mathieu.fleury@ottawa.ca'),
(11, 1, '14', 'Catherine McKenney', 'catherine.mckenney@ottawa.ca'),
(12, 1, '14', 'Catherine McKenney', 'catherine.mckenney@ottawa.ca'),
(13, 1, '17', 'David Chernushenko', 'david.chernushenko@ottawa.ca'),
(14, 1, '22', 'Michael Qaqish', 'michael.qaqish@ottawa.ca'),
(15, 1, '16', 'Riley Brockington', 'riley.brockington@ottawa.ca'),
(16, 1, '2', 'Jody Mitic', 'jody.mitic@ottawa.ca'),
(17, 1, '20', 'George Darouze', 'george.darouze@ottawa.ca'),
(18, 1, '15', 'Jeff Leiper', 'jeff.leiper@ottawa.ca'),
(19, 1, '16', 'Riley Brockington', 'riley.brockington@ottawa.ca'),
(20, 1, '7', 'Mark Taylor', 'mark.leiper@ottawa.ca'),
(21, 1, '15', 'Jeff Leiper', 'jeff.leiper@ottawa.ca'),
(22, 1, '7', 'Mark Taylor', 'mark.taylor@ottawa.ca'),
(23, 1, '16', 'Riley Brockington', 'riley.brockington@ottawa.ca'),
(24, 1, '9', 'Keith Egli', 'keith.egli@ottawa.ca'),
(25, 1, '9', 'Keith Egli', 'keith.egli@ottawa.ca'),
(26, 1, '8', 'Rick Chiarelli', 'rick.egli@ottawa.ca'),
(27, 1, '9', 'Keith Egli', 'keith.egli@ottawa.ca'),
(28, 1, '3', 'Jan Harder', 'jan.harder@ottawa.ca'),
(29, 1, '4', 'Marianne Wilkinson', 'marianne.taylor@ottawa.ca'),
(30, 1, '7', 'Mark Taylor', 'mark.taylor@ottawa.ca'),
(31, 1, '8', 'Rick Chiarelli', 'rick.chiarelli@ottawa.ca'),
(32, 1, '23', 'Allan Hubley', 'allan.hubley@ottawa.ca'),
(33, 1, '14', 'Catherine McKenney', 'catherine.mckenney@ottawa.ca'),
(34, 1, '8', 'Rick Chiarelli', 'rick.moffatt@ottawa.ca'),
(35, 1, '21', 'Scott Moffatt', 'Scott.Moffatt@ottawa.ca'),
(36, 1, '6', 'Shad Qadri', 'shad.qadri@ottawa.ca'),
(37, 1, '4', 'Marianne Wilkinson', 'marianne.chantiry@ottawa.ca'),
(38, 1, '5', 'Eli El-Chantiry', 'eli.el-chantiry@ottawa.ca'),
(39, 1, '23', 'Allan Hubley', 'allan.hubley@ottawa.ca'),
(40, 1, '5', 'Eli El-Chantiry', 'eli.el-chantiry@ottawa.ca'),
(41, 1, '1', 'Bob Monette', 'Bob.Monette@ottawa.ca'),
(42, 1, '19', 'Stephen Blais', 'stephen.blais@ottawa.ca'),
(43, 1, '19', 'Stephen Blais', 'stephen.blais@ottawa.ca'),
(44, 1, '21', 'Scott Moffatt', 'scott.moffatt@ottawa.ca'),
(45, 1, '20', 'George Darouze', 'george.darouze@ottawa.ca'),
(46, 3, 'Carleton', 'Pierre POILIEVRE', 'Pierre.Poilievre@parl.gc.ca'),
(47, 3, 'Carleton', 'Pierre POILIEVRE', 'Pierre.Poilievre@parl.gc.ca'),
(48, 3, 'Carleton', 'Pierre POILIEVRE', 'Pierre.Poilievre@parl.gc.ca'),
(49, 3, 'Carleton', 'Pierre POILIEVRE', 'Pierre.Poilievre@parl.gc.ca'),
(50, 3, 'Carleton', 'Pierre POILIEVRE', 'Pierre.Poilievre@parl.gc.ca'),
(51, 3, 'Carleton', 'Pierre POILIEVRE', 'Pierre.Poilievre@parl.gc.ca'),
(52, 3, 'Carleton', 'Pierre POILIEVRE', 'Pierre.Poilievre@parl.gc.ca'),
(53, 3, 'Carleton', 'Pierre POILIEVRE', 'Pierre.Poilievre@parl.gc.ca'),
(54, 3, 'Glengarry - Prescott - Russell', 'Francis Drouin', 'Francis.Drouin@parl.gc.ca'),
(55, 3, 'Glengarry - Prescott - Russell', 'Francis Drouin', 'Francis.Drouin@parl.gc.ca'),
(56, 3, 'Kanata - Carleton', 'Karen McCrimmon', 'Karen.McCrimmon@parl.gc.ca'),
(57, 3, 'Kanata - Carleton', 'Karen McCrimmon', 'Karen.McCrimmon@parl.gc.ca'),
(58, 3, 'Kanata - Carleton', 'Karen McCrimmon', 'Karen.McCrimmon@parl.gc.ca'),
(59, 3, 'Kanata - Carleton', 'Karen McCrimmon', 'Karen.McCrimmon@parl.gc.ca'),
(60, 3, 'Nepean', 'Chandra Arya', 'Chandra.Arya@parl.gc.ca'),
(61, 3, 'Nepean', 'Chandra Arya', 'Chandra.Arya@parl.gc.ca'),
(62, 3, 'Nepean', 'Chandra Arya', 'Chandra.Arya@parl.gc.ca'),
(63, 3, 'Nepean', 'Chandra Arya', 'Chandra.Arya@parl.gc.ca'),
(64, 3, 'Nepean', 'Chandra Arya', 'Chandra.Arya@parl.gc.ca'),
(65, 3, 'Nepean', 'Chandra Arya', 'Chandra.Arya@parl.gc.ca'),
(66, 3, 'Nepean', 'Chandra Arya', 'Chandra.Arya@parl.gc.ca'),
(67, 3, 'Nepean', 'Chandra Arya', 'Chandra.Arya@parl.gc.ca'),
(68, 3, 'Orleans', 'Andrew Leslie', 'Andrew.Leslie@parl.gc.ca'),
(69, 3, 'Orleans', 'Andrew Leslie', 'Andrew.Leslie@parl.gc.ca'),
(70, 3, 'Orleans', 'Andrew Leslie', 'Andrew.Leslie@parl.gc.ca'),
(71, 3, 'Orleans', 'Andrew Leslie', 'Andrew.Leslie@parl.gc.ca'),
(72, 3, 'Orleans', 'Andrew Leslie', 'Andrew.Leslie@parl.gc.ca'),
(73, 3, 'Orleans', 'Andrew Leslie', 'Andrew.Leslie@parl.gc.ca'),
(74, 3, 'Orleans', 'Andrew Leslie', 'Andrew.Leslie@parl.gc.ca'),
(75, 3, 'Orleans', 'Andrew Leslie', 'Andrew.Leslie@parl.gc.ca'),
(76, 3, 'Ottawa Centre', 'Catherine McKenna', 'Catherine.McKenna@parl.gc.ca'),
(77, 3, 'Ottawa Centre', 'Catherine McKenna', 'Catherine.McKenna@parl.gc.ca'),
(78, 3, 'Ottawa Centre', 'Catherine McKenna', 'Catherine.McKenna@parl.gc.ca'),
(79, 3, 'Ottawa Centre', 'Catherine McKenna', 'Catherine.McKenna@parl.gc.ca'),
(80, 3, 'Ottawa Centre', 'Catherine McKenna', 'Catherine.McKenna@parl.gc.ca'),
(81, 3, 'Ottawa Centre', 'Catherine McKenna', 'Catherine.McKenna@parl.gc.ca'),
(82, 3, 'Ottawa Centre', 'Catherine McKenna', 'Catherine.McKenna@parl.gc.ca'),
(83, 3, 'Ottawa Centre', 'Catherine McKenna', 'Catherine.McKenna@parl.gc.ca'),
(84, 3, 'Ottawa Centre', 'Catherine McKenna', 'Catherine.McKenna@parl.gc.ca'),
(85, 3, 'Ottawa South', 'David McGuinty', 'David.McGuinty@parl.gc.ca'),
(86, 3, 'Ottawa South', 'David McGuinty', 'David.McGuinty@parl.gc.ca'),
(87, 3, 'Ottawa South', 'David McGuinty', 'David.McGuinty@parl.gc.ca'),
(88, 3, 'Ottawa South', 'David McGuinty', 'David.McGuinty@parl.gc.ca'),
(89, 3, 'Ottawa South', 'David McGuinty', 'David.McGuinty@parl.gc.ca'),
(90, 3, 'Ottawa Vanier', '', ''),
(91, 3, 'Ottawa Vanier', '', ''),
(92, 3, 'Ottawa Vanier', '', ''),
(93, 3, 'Ottawa Vanier', '', ''),
(94, 3, 'Ottawa Vanier', '', ''),
(95, 3, 'Ottawa Vanier', '', ''),
(96, 3, 'Ottawa West - Nepean', 'Anita Vandenbeld', 'Anita.Vandenbeld@parl.gc.ca'),
(97, 3, 'Ottawa West - Nepean', 'Anita Vandenbeld', 'Anita.Vandenbeld@parl.gc.ca'),
(98, 3, 'Ottawa West - Nepean', 'Anita Vandenbeld', 'Anita.Vandenbeld@parl.gc.ca'),
(99, 3, 'Ottawa West - Nepean', 'Anita Vandenbeld', 'Anita.Vandenbeld@parl.gc.ca'),
(100, 3, 'Ottawa West - Nepean', 'Anita Vandenbeld', 'Anita.Vandenbeld@parl.gc.ca'),
(101, 3, 'Ottawa West - Nepean', 'Anita Vandenbeld', 'Anita.Vandenbeld@parl.gc.ca'),
(102, 3, 'Ottawa West - Nepean', 'Anita Vandenbeld', 'Anita.Vandenbeld@parl.gc.ca'),
(103, 2, 'Carleton - Mississippi Mills', 'Jack MacLaren', 'jack.maclaren@pc.ola.org'),
(104, 2, 'Carleton - Mississippi Mills', 'Jack MacLaren', 'jack.maclaren@pc.ola.org'),
(105, 2, 'Carleton - Mississippi Mills', 'Jack MacLaren', 'jack.maclaren@pc.ola.org'),
(106, 2, 'Carleton - Mississippi Mills', 'Jack MacLaren', 'jack.maclaren@pc.ola.org'),
(107, 2, 'Carleton - Mississippi Mills', 'Jack MacLaren', 'jack.maclaren@pc.ola.org'),
(108, 2, 'Carleton - Mississippi Mills', 'Jack MacLaren', 'jack.maclaren@pc.ola.org'),
(109, 2, 'Carleton - Mississippi Mills', 'Jack MacLaren', 'jack.maclaren@pc.ola.org'),
(110, 2, 'Carleton - Mississippi Mills', 'Jack MacLaren', 'jack.maclaren@pc.ola.org'),
(111, 2, 'Glengarry - Prescott - Russell', 'Grant Crack', 'gcrack.mpp@liberal.ola.org'),
(112, 2, 'Glengarry - Prescott - Russell', 'Grant Crack', 'gcrack.mpp@liberal.ola.org'),
(113, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(114, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(115, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(116, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(117, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(118, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(119, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(120, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(121, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(122, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(123, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(124, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(125, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(126, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(127, 2, 'Nepean - Carleton', 'Lisa MacLeod', 'lisa.macleod@pc.ola.org'),
(128, 2, 'Ottawa Orleans', 'Hon Marie-France Lalonde', 'mflalonde.mpp.co@liberal.ola.org'),
(129, 2, 'Ottawa Centre', 'Hon Yasir Naqvi', 'ynaqvi.mpp@liberal.ola.org'),
(130, 2, 'Ottawa Centre', 'Hon Yasir Naqvi', 'ynaqvi.mpp@liberal.ola.org'),
(131, 2, 'Ottawa Centre', 'Hon Yasir Naqvi', 'ynaqvi.mpp@liberal.ola.org'),
(132, 2, 'Ottawa Centre', 'Hon Yasir Naqvi', 'ynaqvi.mpp@liberal.ola.org'),
(133, 2, 'Ottawa Centre', 'Hon Yasir Naqvi', 'ynaqvi.mpp@liberal.ola.org'),
(134, 2, 'Ottawa Centre', 'Hon Yasir Naqvi', 'ynaqvi.mpp@liberal.ola.org'),
(135, 2, 'Ottawa Centre', 'Hon Yasir Naqvi', 'ynaqvi.mpp@liberal.ola.org'),
(136, 2, 'Ottawa Centre', 'Hon Yasir Naqvi', 'ynaqvi.mpp@liberal.ola.org'),
(137, 2, 'Ottawa Centre', 'Hon Yasir Naqvi', 'ynaqvi.mpp@liberal.ola.org'),
(138, 2, 'Ottawa Orleans', 'Hon Marie-France Lalonde', 'mflalonde.mpp.co@liberal.ola.org'),
(139, 2, 'Ottawa Orleans', 'Hon Marie-France Lalonde', 'mflalonde.mpp.co@liberal.ola.org'),
(140, 2, 'Ottawa Orleans', 'Hon Marie-France Lalonde', 'mflalonde.mpp.co@liberal.ola.org'),
(141, 2, 'Ottawa Orleans', 'Hon Marie-France Lalonde', 'mflalonde.mpp.co@liberal.ola.org'),
(142, 2, 'Ottawa Orleans', 'Hon Marie-France Lalonde', 'mflalonde.mpp.co@liberal.ola.org'),
(143, 2, 'Ottawa Orleans', 'Hon Marie-France Lalonde', 'mflalonde.mpp.co@liberal.ola.org'),
(144, 2, 'Ottawa South', 'John Fraser', 'Jfraser.mpp.co@liberal.ola.org'),
(145, 2, 'Ottawa South', 'John Fraser', 'Jfraser.mpp.co@liberal.ola.org'),
(146, 2, 'Ottawa South', 'John Fraser', 'Jfraser.mpp.co@liberal.ola.org'),
(147, 2, 'Ottawa South', 'John Fraser', 'Jfraser.mpp.co@liberal.ola.org'),
(148, 2, 'Ottawa South', 'John Fraser', 'Jfraser.mpp.co@liberal.ola.org'),
(149, 2, 'Ottawa Vanier', 'Nathalie Des Rosiers', 'ndesrosiers.mpp.com@liberal.ola.org'),
(150, 2, 'Ottawa Vanier', 'Nathalie Des Rosiers', 'ndesrosiers.mpp.com@liberal.ola.org'),
(151, 2, 'Ottawa Vanier', 'Nathalie Des Rosiers', 'ndesrosiers.mpp.com@liberal.ola.org'),
(152, 2, 'Ottawa Vanier', 'Nathalie Des Rosiers', 'ndesrosiers.mpp.com@liberal.ola.org'),
(153, 2, 'Ottawa Vanier', 'Nathalie Des Rosiers', 'ndesrosiers.mpp.com@liberal.ola.org'),
(154, 2, 'Ottawa Vanier', 'Nathalie Des Rosiers', 'ndesrosiers.mpp.com@liberal.ola.org'),
(155, 2, 'Ottawa West - Nepean', 'Hon Bob Chiarelli', 'bchiarelli.mpp.co@liberal.ola.org'),
(156, 2, 'Ottawa West - Nepean', 'Hon Bob Chiarelli', 'bchiarelli.mpp.co@liberal.ola.org'),
(157, 2, 'Ottawa West - Nepean', 'Hon Bob Chiarelli', 'bchiarelli.mpp.co@liberal.ola.org'),
(158, 2, 'Ottawa West - Nepean', 'Hon Bob Chiarelli', 'bchiarelli.mpp.co@liberal.ola.org'),
(159, 2, 'Ottawa West - Nepean', 'Hon Bob Chiarelli', 'bchiarelli.mpp.co@liberal.ola.org'),
(160, 2, 'Ottawa West - Nepean', 'Hon Bob Chiarelli', 'bchiarelli.mpp.co@liberal.ola.org');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `grouptypes`
--
ALTER TABLE `grouptypes`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `ridings`
--
ALTER TABLE `ridings`
  ADD PRIMARY KEY (`ridingid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `grouptypes`
--
ALTER TABLE `grouptypes`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `ridings`
--
ALTER TABLE `ridings`
  MODIFY `ridingid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=161;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
