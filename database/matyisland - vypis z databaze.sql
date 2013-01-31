-- phpMyAdmin SQL Dump
-- version 3.5.2.1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Čtv 31. led 2013, 11:50
-- Verze MySQL: 5.5.27
-- Verze PHP: 5.3.15

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `matyisland`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `basket`
--

CREATE TABLE IF NOT EXISTS `basket` (
  `basket_id` int(11) NOT NULL AUTO_INCREMENT,
  `basket_session_id` varchar(50) NOT NULL,
  `basket_quantity` int(11) NOT NULL DEFAULT '0',
  `basket_ip_address` varchar(50) NOT NULL,
  `basket_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT '0',
  PRIMARY KEY (`basket_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Vypisuji data pro tabulku `basket`
--

INSERT INTO `basket` (`basket_id`, `basket_session_id`, `basket_quantity`, `basket_ip_address`, `basket_timestamp`, `user_id`) VALUES
(12, 'trmc302h29l1fa0tst3nj5eit3', 5, '127.0.0.1', '2013-01-24 16:17:11', 3),
(13, 'nm875vo192pbheahogsb399hn3', 1, '127.0.0.1', '2013-01-31 09:45:54', 8);

-- --------------------------------------------------------

--
-- Struktura tabulky `basket_has_product`
--

CREATE TABLE IF NOT EXISTS `basket_has_product` (
  `basket_basket_id` int(11) NOT NULL,
  `product_prod_id` int(11) NOT NULL,
  PRIMARY KEY (`basket_basket_id`,`product_prod_id`),
  KEY `fk_basket_has_product_basket` (`basket_basket_id`),
  KEY `fk_basket_has_product_product` (`product_prod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `basket_has_product`
--

INSERT INTO `basket_has_product` (`basket_basket_id`, `product_prod_id`) VALUES
(12, 11),
(13, 7);

-- --------------------------------------------------------

--
-- Struktura tabulky `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(45) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Vypisuji data pro tabulku `category`
--

INSERT INTO `category` (`cat_id`, `cat_name`) VALUES
(1, 'Hračky pro holčičky'),
(2, 'Hračky pro kluky'),
(3, 'Dřevěné hračky'),
(4, 'Hračky pro nejmenší'),
(5, 'Hry a hlavolamy');

-- --------------------------------------------------------

--
-- Struktura tabulky `category_has_product`
--

CREATE TABLE IF NOT EXISTS `category_has_product` (
  `category_cat_id` int(11) NOT NULL,
  `product_prod_id` int(11) NOT NULL,
  PRIMARY KEY (`category_cat_id`,`product_prod_id`),
  KEY `fk_category_has_product_category` (`category_cat_id`),
  KEY `fk_category_has_product_product` (`product_prod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `category_has_product`
--

INSERT INTO `category_has_product` (`category_cat_id`, `product_prod_id`) VALUES
(1, 1),
(1, 2),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 11),
(2, 2),
(2, 3),
(2, 11),
(3, 9),
(4, 7),
(5, 4);

-- --------------------------------------------------------

--
-- Struktura tabulky `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `com_id` int(11) NOT NULL AUTO_INCREMENT,
  `com_subject` varchar(255) NOT NULL,
  `com_text` longtext NOT NULL,
  `com_date` datetime NOT NULL,
  `product_prod_id` int(11) DEFAULT NULL,
  `user_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`com_id`),
  KEY `fk_comments_product` (`product_prod_id`),
  KEY `fk_comments_user` (`user_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Vypisuji data pro tabulku `comments`
--

INSERT INTO `comments` (`com_id`, `com_subject`, `com_text`, `com_date`, `product_prod_id`, `user_user_id`) VALUES
(11, 'Tučňáci POP N'' DROP', 'Dobrý den, opravdu se jedná o hru s tučňáky?', '2013-01-24 17:17:30', 4, 3);

-- --------------------------------------------------------

--
-- Struktura tabulky `delivery`
--

CREATE TABLE IF NOT EXISTS `delivery` (
  `delivery_id` int(11) NOT NULL AUTO_INCREMENT,
  `delivery_name` varchar(255) NOT NULL,
  `delivery_describe` varchar(255) DEFAULT NULL,
  `delivery_price` decimal(19,0) NOT NULL,
  `delivery_enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`delivery_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Vypisuji data pro tabulku `delivery`
--

INSERT INTO `delivery` (`delivery_id`, `delivery_name`, `delivery_describe`, `delivery_price`, `delivery_enabled`) VALUES
(1, 'Česká pošta', 'balík do ruky, doručení do 2-3 pracovní dnů', '89', 1),
(2, 'Kurýr DPD', 'dodání do 1-2 pracovních dnů', '89', 1),
(3, 'Osobní převzetí', NULL, '0', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `image`
--

CREATE TABLE IF NOT EXISTS `image` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_prod_id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `image_is_main` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`image_id`,`product_prod_id`),
  KEY `fk_image_product` (`product_prod_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=107 ;

--
-- Vypisuji data pro tabulku `image`
--

INSERT INTO `image` (`image_id`, `product_prod_id`, `image_name`, `image_is_main`) VALUES
(75, 2, 'pizza1.jpg', 1),
(76, 2, 'pizza2.jpg', 0),
(84, 11, 'lodicky.jpg', 1),
(88, 9, 'drevene-puzzle-9-dilku-falko.jpg', 1),
(90, 8, 'mikrovlnka.jpg', 1),
(92, 7, 'rybka.jpg', 1),
(94, 6, 'panenka-38-cm.jpg', 1),
(96, 5, 'panenka-hadrova.jpg', 1),
(98, 4, 'tuc.jpg', 1),
(99, 4, 'tuc2.jpg', 0),
(100, 4, 'tuc3.jpg', 0),
(102, 3, 'robot.jpg', 1),
(104, 1, 'vodni.jpg', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `ord_id` int(11) NOT NULL AUTO_INCREMENT,
  `ord_date` datetime DEFAULT NULL,
  `user_user_id` int(11) DEFAULT NULL,
  `cust_name` varchar(45) DEFAULT NULL,
  `cust_surname` varchar(45) DEFAULT NULL,
  `cust_email` varchar(45) DEFAULT NULL,
  `cust_telefon` varchar(9) DEFAULT NULL,
  `cust_street` varchar(45) DEFAULT NULL,
  `cust_city` varchar(45) DEFAULT NULL,
  `cust_psc` varchar(5) DEFAULT NULL,
  `cust_firmName` varchar(45) DEFAULT NULL,
  `cust_ico` varchar(8) DEFAULT NULL,
  `cust_dic` varchar(12) DEFAULT NULL,
  `cust_bname` varchar(45) DEFAULT NULL,
  `cust_bsurname` varchar(45) DEFAULT NULL,
  `cust_bemail` varchar(45) DEFAULT NULL,
  `cust_btelefon` varchar(9) DEFAULT NULL,
  `cust_bstreet` varchar(45) DEFAULT NULL,
  `cust_bcity` varchar(45) DEFAULT NULL,
  `cust_bpsc` varchar(5) DEFAULT NULL,
  `cust_bfirmName` varchar(45) DEFAULT NULL,
  `cust_note` text,
  `seller_note` text,
  `isGift` tinyint(1) NOT NULL DEFAULT '0',
  `deliveryPrice` int(11) DEFAULT NULL,
  `ord_status` varchar(255) NOT NULL DEFAULT 'Nevyřízeno',
  `delivery_delivery_id` int(11) NOT NULL,
  `payment_payment_id` int(11) NOT NULL,
  PRIMARY KEY (`ord_id`),
  KEY `fk_order_user` (`user_user_id`),
  KEY `fk_orders_delivery` (`delivery_delivery_id`),
  KEY `fk_orders_payment` (`payment_payment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;

--
-- Vypisuji data pro tabulku `orders`
--

INSERT INTO `orders` (`ord_id`, `ord_date`, `user_user_id`, `cust_name`, `cust_surname`, `cust_email`, `cust_telefon`, `cust_street`, `cust_city`, `cust_psc`, `cust_firmName`, `cust_ico`, `cust_dic`, `cust_bname`, `cust_bsurname`, `cust_bemail`, `cust_btelefon`, `cust_bstreet`, `cust_bcity`, `cust_bpsc`, `cust_bfirmName`, `cust_note`, `seller_note`, `isGift`, `deliveryPrice`, `ord_status`, `delivery_delivery_id`, `payment_payment_id`) VALUES
(38, '2012-12-12 21:08:28', NULL, 'asd', 'asf', '', '', '', '', '', '', '', '', 'asd', 'asf', NULL, NULL, '', '', '', NULL, '', NULL, 0, 89, 'Expedováno', 1, 1),
(39, '2012-12-15 19:38:28', 3, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, '', NULL, 0, 89, 'Nevyřízeno', 1, 1),
(40, '2012-12-26 15:28:08', 3, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, '', NULL, 0, 89, 'Stornováno', 1, 1),
(41, '2012-12-26 15:28:19', 3, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, '', NULL, 0, 89, 'Nevyřízeno', 1, 1),
(42, '2012-12-26 15:28:33', 3, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, '', NULL, 0, 89, 'Vráceno', 1, 1),
(43, '2012-12-26 15:30:08', 3, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, '', NULL, 0, 89, 'Nevyřízeno', 1, 1),
(44, '2012-12-26 16:33:40', 3, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, '', NULL, 0, 89, 'Expedováno', 1, 1),
(45, '2013-01-03 14:42:38', NULL, 'František', 'Škála', 'franta@seznam.cz', '734230047', 'Čečenská 5', 'Olomouc', '54320', '', '', '', 'František', 'Škála', '', '', 'Čečenská 5', 'Olomouc', '54320', '', '', '', 0, 89, 'Vyřízeno', 1, 1),
(47, '2013-01-03 21:14:07', 3, 'Jarda', 'Klimčík', 'adsfdsf@sad', '455445', 'Čečenská 5', 'Olomouc', '43323', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, 'Čečenská 5', 'Olomouc', '43323', NULL, 'Nejsem hloupá kuchařka!', NULL, 0, 89, 'Nevyřízeno', 1, 1),
(48, '2013-01-04 15:36:27', 3, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, '', NULL, 0, 89, 'Vyřízeno', 1, 1),
(49, '2013-01-13 05:16:20', NULL, 'Miloš', 'Zeman', 'sfds@sdf.cz', '763548', 'Patova 6', 'Praha', '76567', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 89, 'Nevyřízeno', 1, 1),
(50, '2013-01-13 06:18:34', NULL, 'Karel', 'Švancenberk', 'sajfiskdf@asdladm.cz', '876384', 'Miločná 8', 'Sýkořice', '99864', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 89, 'Vyřízeno', 1, 1),
(53, '2013-01-25 13:47:38', 3, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 0, 89, 'Nevyřízeno', 1, 1),
(57, '2013-01-28 00:00:00', NULL, 'Štěpánka', 'Klimčíková', 'klimcik.m@seznam.cz', '765456786', 'Švédská 46', 'Ostrava', '71200', '', '', '', 'Štěpánka', 'Klimčíková', '', '', 'Švédská 46', 'Ostrava', '71200', '', NULL, NULL, 0, NULL, 'Vyřízeno', 3, 2),
(58, '2013-01-29 11:17:29', NULL, 'Jaroslav', 'Klimčík', 'jerry.klimcik@gmail.com', '736670038', 'Švédská 46', 'Ostrava', '71200', 'JOKO', '88499938', '', 'Jaroslav', 'Klimčík', NULL, NULL, 'Švédská 46', 'Ostrava', '71200', NULL, '', NULL, 0, 139, 'Nevyřízeno', 1, 3),
(59, '2013-01-29 11:20:34', NULL, 'František', 'Pískal', 'franta@seznam.cz', '736679930', 'Čečenská 5', 'Olomouc', '54320', '', '', '', 'František', 'Pískal', NULL, NULL, 'Čečenská 5', 'Olomouc', '54320', NULL, '', NULL, 0, 89, 'Nevyřízeno', 2, 1),
(60, '2013-01-30 17:09:47', NULL, 'Jarda', 'Klimčík', 'adsfdsf@sad', '887738849', 'Čečenská 5', 'Olomouc', '54320', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, 'Čečenská 5', 'Olomouc', '54320', NULL, '', NULL, 0, 139, 'Nevyřízeno', 1, 3),
(61, '2013-01-30 17:27:25', NULL, 'František', 'Škála', 'franta@seznam.cz', '765567784', 'Švédská 46', 'Olomouc', '54320', '', '', '', 'František', 'Škála', NULL, NULL, 'Švédská 46', 'Olomouc', '54320', NULL, '', NULL, 0, 89, 'Nevyřízeno', 1, 1),
(62, '2013-01-30 17:28:26', NULL, 'František', 'Škála', 'franta@seznam.cz', '765567784', 'Švédská 46', 'Olomouc', '54320', '', '', '', 'František', 'Škála', NULL, NULL, 'Švédská 46', 'Olomouc', '54320', NULL, '', NULL, 0, 89, 'Nevyřízeno', 1, 1),
(63, '2013-01-30 17:34:30', NULL, 'Jaroslav', 'Klimčík', 'jerry.klimcik@gmail.com', '748857749', 'Švédská 46', 'Ostrava', '71200', '', '', '', 'Jaroslav', 'Klimčík', NULL, NULL, 'Švédská 46', 'Ostrava', '71200', NULL, 'Dobrý obchod!', NULL, 0, 139, 'Nevyřízeno', 1, 3),
(64, '2013-01-30 17:36:36', NULL, 'Jaroslav', 'Klimčík', 'jerry.klimcik@gmail.com', '748857749', 'Švédská 46', 'Ostrava', '71200', '', '', '', 'Jaroslav', 'Klimčík', NULL, NULL, 'Švédská 46', 'Ostrava', '71200', NULL, 'Dobrý obchod!', NULL, 0, 139, 'Nevyřízeno', 1, 3),
(65, '2013-01-30 17:43:48', NULL, 'Jaroslav', 'Klimčík', 'jerry.klimcik@gmail.com', '748857749', 'Švédská 46', 'Ostrava', '71200', '', '', '', 'Jaroslav', 'Klimčík', NULL, NULL, 'Švédská 46', 'Ostrava', '71200', NULL, '', NULL, 0, 139, 'Nevyřízeno', 1, 3);

-- --------------------------------------------------------

--
-- Struktura tabulky `order_has_product`
--

CREATE TABLE IF NOT EXISTS `order_has_product` (
  `order_ord_id` int(11) NOT NULL,
  `product_prod_id` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL,
  `actual_price_of_product` int(11) NOT NULL,
  `totalPrice` int(11) NOT NULL,
  PRIMARY KEY (`order_ord_id`,`product_prod_id`),
  KEY `fk_order_has_product_order` (`order_ord_id`),
  KEY `fk_order_has_product_product` (`product_prod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `order_has_product`
--

INSERT INTO `order_has_product` (`order_ord_id`, `product_prod_id`, `quantity`, `actual_price_of_product`, `totalPrice`) VALUES
(38, 1, 10, 125, 1250),
(38, 5, 1, 27, 27),
(39, 6, 2, 40, 80),
(41, 6, 1, 419, 419),
(42, 6, 1, 419, 419),
(43, 6, 1, 419, 419),
(44, 4, 11, 129, 1419),
(45, 2, 4, 279, 1116),
(45, 3, 5, 55, 275),
(47, 3, 3, 55, 165),
(48, 1, 1, 179, 179),
(48, 2, 1, 279, 279),
(49, 2, 1, 30, 60),
(50, 1, 2, 179, 358),
(50, 11, 2, 49, 49),
(53, 3, 1, 55, 55),
(53, 11, 1, 49, 49),
(57, 4, 1, 129, 129),
(57, 11, 3, 49, 147),
(58, 11, 3, 49, 147),
(59, 11, 1, 49, 49),
(60, 4, 1, 129, 129),
(60, 11, 2, 49, 98),
(61, 6, 2, 419, 838),
(61, 11, 1, 49, 49),
(62, 6, 2, 419, 838),
(62, 11, 1, 49, 49),
(63, 3, 1, 55, 55),
(63, 11, 3, 49, 147),
(64, 3, 1, 55, 55),
(64, 11, 3, 49, 147),
(65, 11, 3, 49, 147);

-- --------------------------------------------------------

--
-- Struktura tabulky `payment`
--

CREATE TABLE IF NOT EXISTS `payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_name` varchar(255) NOT NULL,
  `payment_describe` varchar(255) DEFAULT NULL,
  `payment_price` decimal(19,0) NOT NULL,
  `payment_enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Vypisuji data pro tabulku `payment`
--

INSERT INTO `payment` (`payment_id`, `payment_name`, `payment_describe`, `payment_price`, `payment_enabled`) VALUES
(1, 'Převodem na účet', NULL, '0', 1),
(2, 'Hotově', NULL, '0', 1),
(3, 'Dobírkou přes Českou poštu', NULL, '50', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `prod_id` int(11) NOT NULL AUTO_INCREMENT,
  `prod_name` varchar(45) NOT NULL,
  `prod_producer` varchar(45) DEFAULT NULL,
  `prod_price` int(11) NOT NULL,
  `prod_code` varchar(11) DEFAULT NULL,
  `prod_describe` varchar(255) DEFAULT NULL,
  `prod_long_describe` text,
  `prod_isnew` tinyint(1) NOT NULL DEFAULT '0',
  `prod_on_stock` int(11) NOT NULL DEFAULT '0',
  `prod_is_active` tinyint(1) NOT NULL DEFAULT '1',
  `total_votes` int(11) NOT NULL DEFAULT '0',
  `total_value` int(11) NOT NULL DEFAULT '0',
  `used_ips` longtext,
  PRIMARY KEY (`prod_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Vypisuji data pro tabulku `product`
--

INSERT INTO `product` (`prod_id`, `prod_name`, `prod_producer`, `prod_price`, `prod_code`, `prod_describe`, `prod_long_describe`, `prod_isnew`, `prod_on_stock`, `prod_is_active`, `total_votes`, `total_value`, `used_ips`) VALUES
(1, 'Vodní kobereček + pěnové puzzle zdarma', 'babyShop', 179, '', '<p>V&yacute;jimečn&aacute; vodn&iacute; podložka (souč&aacute;st&iacute; vodn&iacute; pero) je dokonalou hračkou pro nejmen&scaron;&iacute; už od 12. měs&iacute;ců</p>', '<p><strong>V&yacute;jimečn&aacute; vodn&iacute; podložka (souč&aacute;st&iacute; vodn&iacute; pero) je dokonalou hračkou pro nejmen&scaron;&iacute; už od 12. měs&iacute;ců</strong></p>', 1, 4, 1, 0, 0, ''),
(2, 'Play-Doh - Pizza hrací set', 'babyShop', 279, '', '<p>Novinka Play-Doh. Děti si z t&eacute;to sady mohou připravit v&yacute;bornou sn&iacute;dani s pizzou, palačinkami, vaj&iacute;čky nebo vaflemi! Model&iacute;na s formou, se ktrou si užijete spousty z&aacute;bavy.</p>', '<p><strong>Novinka Play-Doh. Děti si z t&eacute;to sady mohou připravit v&yacute;bornou sn&iacute;dani s pizzou, palačinkami, vaj&iacute;čky nebo vaflemi! Model&iacute;na s formou, se ktrou si užijete spousty z&aacute;bavy.</strong></p>', 1, 4, 1, 0, 0, ''),
(3, 'Robot skládací cca 16 cm', 'babyShop', 55, '', '<p>Skl&aacute;dac&iacute; robot, kter&yacute; se um&iacute; přeměnit v letadlo</p>', '<p>Skl&aacute;dac&iacute; robot, kter&yacute; se um&iacute; přeměnit v letadlo</p>', 1, 0, 1, 0, 0, ''),
(4, 'Tučňáci POP N'' DROP', 'babyShop', 129, '', '<p>Z&aacute;bavn&aacute; rodinn&aacute; hra Tučň&aacute;ci je z&aacute;bavněj&scaron;&iacute; forma klasick&eacute; hry Člověče nezlob se</p>', '<p>Z&aacute;bavn&aacute; rodinn&aacute; hra Tučň&aacute;ci je z&aacute;bavněj&scaron;&iacute; forma klasick&eacute; hry Člověče nezlob se</p>', 1, 9, 1, 2, 9, 'a:2:{i:0;s:9:"127.1.0.1";i:1;s:9:"127.0.0.1";}'),
(5, 'Panenka hadrová 40 cm', 'babyShop', 115, '', '<p>Tradičn&iacute; hadrov&aacute; panenka, kter&aacute; je opravdu kr&aacute;sn&aacute;. Po jednom kusu z každ&eacute; barvy.</p>', '<p>Tradičn&iacute; hadrov&aacute; panenka, kter&aacute; je opravdu kr&aacute;sn&aacute;. Po jednom kusu z každ&eacute; barvy.</p>', 1, 3, 1, 0, 0, ''),
(6, 'Panenka', 'babyShop', 419, '', '<p>Polož&iacute;te-li ji do post&yacute;lky, zavře oči a pokud ji vezmete zpět do n&aacute;ruče tak otevře očka.</p>', '<p>Polož&iacute;te-li ji do post&yacute;lky, zavře oči a pokud ji vezmete zpět do n&aacute;ruče tak otevře očka.</p>', 1, 0, 1, 0, 0, ''),
(7, 'Rybka plastová pískací na kolečkách 9cm', 'babyShop', 15, '', '<p>Rybka plastov&aacute; p&iacute;skac&iacute; na kolečk&aacute;ch 9cm</p>', '<p>Rybka plastov&aacute; p&iacute;skac&iacute; na kolečk&aacute;ch 9cm</p>', 1, 25, 1, 0, 0, ''),
(8, 'Mikrovlná trouba', 'babyShop', 215, '', '<p>Růžov&aacute; mikrovln&aacute; trouba pro mal&eacute; hospodyňky.</p>', '<p>Růžov&aacute; mikrovln&aacute; trouba pro mal&eacute; hospodyňky.</p>', 0, 2, 1, 0, 0, ''),
(9, 'Dřevěné PUZZLE', 'babyShop', 32, '', '<p>Dřevěn&eacute; puzzle s dev&iacute;ti d&iacute;ly. Tato tradičn&iacute; dřevěn&aacute; hračka z&aacute;bavnou formou rozv&iacute;j&iacute; představivost a koordinaci dět&iacute; a uč&iacute; trpělivosti.</p>', '<p>Dřevěn&eacute; puzzle s dev&iacute;ti d&iacute;ly. Tato tradičn&iacute; dřevěn&aacute; hračka z&aacute;bavnou formou rozv&iacute;j&iacute; představivost a koordinaci dět&iacute; a uč&iacute; trpělivosti.</p>', 0, 25, 1, 0, 0, ''),
(11, 'Lodičky plastové 3 ks', NULL, 49, '', '<p>adadad adadaaaaaaa aaaaaaaa aaaaaaaa aaaaaa aaaaaaaa aaa aaaa aaaaaa aa aaaaa aaaa aaaaaaa aaaaaaa aaaaa aaaaa aaaaaa</p>', '<p>adadadadadad</p>', 1, 4, 1, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `ratings`
--

CREATE TABLE IF NOT EXISTS `ratings` (
  `id` varchar(11) NOT NULL,
  `total_votes` int(11) NOT NULL DEFAULT '0',
  `total_value` int(11) NOT NULL DEFAULT '0',
  `used_ips` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `ratings`
--

INSERT INTO `ratings` (`id`, `total_votes`, `total_value`, `used_ips`) VALUES
('2id', 2, 6, 'a:2:{i:0;s:9:"127.0.0.1";i:1;s:9:"127.0.0.1";}'),
('3xx', 0, 0, ''),
('4test', 0, 0, ''),
('5560', 0, 0, ''),
('63334', 0, 0, ''),
('66234', 0, 0, ''),
('66334', 0, 0, ''),
('id1', 0, 0, ''),
('id21', 0, 0, ''),
('id22', 0, 0, '');

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_password` varchar(100) NOT NULL,
  `user_email` varchar(45) NOT NULL,
  `basket_basket_id` int(11) DEFAULT NULL,
  `user_name` varchar(45) NOT NULL,
  `user_surname` varchar(45) NOT NULL,
  `user_telefon` varchar(9) NOT NULL,
  `user_street` varchar(45) NOT NULL,
  `user_city` varchar(45) NOT NULL,
  `user_psc` varchar(5) NOT NULL,
  `user_firmName` varchar(45) DEFAULT NULL,
  `user_ico` varchar(8) DEFAULT NULL,
  `user_dic` varchar(12) DEFAULT NULL,
  `user_hash` varchar(255) DEFAULT NULL,
  `user_is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `fk_user_basket` (`basket_basket_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`user_id`, `user_password`, `user_email`, `basket_basket_id`, `user_name`, `user_surname`, `user_telefon`, `user_street`, `user_city`, `user_psc`, `user_firmName`, `user_ico`, `user_dic`, `user_hash`, `user_is_active`) VALUES
(1, '$2a$07$y5b4rwqnzua3bdb2q9hqmuvt.JyOygnsK/dpoShRvyen3aGvlxWtq', 'admin@admin.cz', NULL, 'Admin', 'Admin', '000000000', '000000000', '000000000', '00000', NULL, NULL, NULL, NULL, 1),
(3, '$2a$07$jrjcxotq1e5nz4abv5d5pe0it2Hp48YsIu/jvSKx3EdNoY1xylIe.', 'jerry.klimcik1@gmail.com', NULL, 'Jaroslav', 'Klimčík', '736670038', 'Švédská 46', 'Ostrava', '71200', '', '', '', NULL, 1),
(4, '$2a$07$vj48emq1mefqjwxqrj7otuM84LJZ5ucLKeEhR2ZY3fqObhPgw2ZJm', 'jerry.klimcik2@gmail.com', NULL, 'Štěpánka', 'Klimčíková', '738483999', 'Švédská 46', 'Ostrava', '71200', '', '', '', NULL, 1),
(8, '$2a$07$9kp9xu8pnxcfj93beoghruFnEzNEHbE3v29k32typN2fdS7CjZop.', 'jerry.klimcik@gmail.com', NULL, 'Alojz', 'Jirásek', '738483994', 'Švédská 46', 'Ostrava', '71200', '', '', '', NULL, 1);

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `basket_has_product`
--
ALTER TABLE `basket_has_product`
  ADD CONSTRAINT `fk_basket_has_product_basket` FOREIGN KEY (`basket_basket_id`) REFERENCES `basket` (`basket_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_basket_has_product_product` FOREIGN KEY (`product_prod_id`) REFERENCES `product` (`prod_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `category_has_product`
--
ALTER TABLE `category_has_product`
  ADD CONSTRAINT `category_has_product_ibfk_4` FOREIGN KEY (`product_prod_id`) REFERENCES `product` (`prod_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `category_has_product_ibfk_3` FOREIGN KEY (`category_cat_id`) REFERENCES `category` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_product` FOREIGN KEY (`product_prod_id`) REFERENCES `product` (`prod_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `image_ibfk_3` FOREIGN KEY (`product_prod_id`) REFERENCES `product` (`prod_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_delivery` FOREIGN KEY (`delivery_delivery_id`) REFERENCES `delivery` (`delivery_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_orders_payment` FOREIGN KEY (`payment_payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `order_has_product`
--
ALTER TABLE `order_has_product`
  ADD CONSTRAINT `order_has_product_ibfk_2` FOREIGN KEY (`product_prod_id`) REFERENCES `product` (`prod_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_order_has_product_order` FOREIGN KEY (`order_ord_id`) REFERENCES `orders` (`ord_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_basket` FOREIGN KEY (`basket_basket_id`) REFERENCES `basket` (`basket_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
