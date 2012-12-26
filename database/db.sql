-- phpMyAdmin SQL Dump
-- version 3.5.2.1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Stř 26. pro 2012, 14:03
-- Verze MySQL: 5.5.27
-- Verze PHP: 5.3.15

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `basket`
--

INSERT INTO `basket` (`basket_id`, `basket_session_id`, `basket_quantity`, `basket_ip_address`, `basket_timestamp`, `user_id`) VALUES
(4, 'fupkhii5d9gum32om4scg7a5m5', 1, '127.0.0.1', '2012-12-23 22:11:41', 1);

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
(4, 6);

-- --------------------------------------------------------

--
-- Struktura tabulky `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(45) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

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
(1, 2),
(1, 5),
(1, 6),
(1, 8),
(2, 2),
(2, 3),
(3, 9),
(4, 1),
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `comments`
--

INSERT INTO `comments` (`com_id`, `com_subject`, `com_text`, `com_date`, `product_prod_id`, `user_user_id`) VALUES
(9, 'Méďa', 'Dobrý den, opravdu se jedná o hru s tučňáky?', '2012-12-19 16:14:35', 4, 3),
(10, 'Tučňáci POP N'' DROP', 'Ano, už to tak bude', '2012-12-24 16:38:12', 4, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Vypisuji data pro tabulku `image`
--

INSERT INTO `image` (`image_id`, `product_prod_id`, `image_name`, `image_is_main`) VALUES
(1, 1, 'carp.jpg', 1),
(3, 3, 'robot.jpg', 1),
(4, 4, 'tuc.jpg', 1),
(5, 5, 'panenka-hadrova.jpg', 1),
(6, 6, 'panenka-38-cm.jpg', 1),
(7, 4, 'tuc2.jpg', 0),
(8, 4, 'tuc3.jpg', 0),
(9, 1, 'carp2.jpg', 0),
(10, 1, 'carp3.jpg', 0),
(11, 2, 'pizza1.jpg', 1),
(12, 2, 'pizza2.jpg', 0),
(13, 7, 'rybka.jpg', 1),
(14, 8, 'mikrovlnka.jpg', 1),
(15, 9, 'drevene-puzzle-9-dilku---falko.jpg', 1);

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
  `cust_payment` varchar(45) DEFAULT NULL,
  `cust_delivery` varchar(45) DEFAULT NULL,
  `cust_note` varchar(255) DEFAULT NULL,
  `isGift` tinyint(1) NOT NULL,
  `deliveryPrice` int(11) NOT NULL,
  PRIMARY KEY (`ord_id`),
  KEY `fk_order_user` (`user_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

--
-- Vypisuji data pro tabulku `orders`
--

INSERT INTO `orders` (`ord_id`, `ord_date`, `user_user_id`, `cust_name`, `cust_surname`, `cust_email`, `cust_telefon`, `cust_street`, `cust_city`, `cust_psc`, `cust_firmName`, `cust_ico`, `cust_dic`, `cust_bname`, `cust_bsurname`, `cust_bemail`, `cust_btelefon`, `cust_bstreet`, `cust_bcity`, `cust_bpsc`, `cust_bfirmName`, `cust_payment`, `cust_delivery`, `cust_note`, `isGift`, `deliveryPrice`) VALUES
(1, '2012-11-17 15:21:34', NULL, 'sdfdsf', 'sdfsdf', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'directDebit', 'post', 'ssfgdfsg', 0, 0),
(2, '2012-11-17 15:23:26', NULL, 'sadf', 'asdad', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'directDebit', 'post', '', 0, 0),
(3, '2012-12-09 14:53:50', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'post', '', 1, 119),
(4, '2012-12-09 14:56:32', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'directDebit', 'post', '', 0, 89),
(5, '2012-12-09 14:57:18', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'post', '', 1, 119),
(6, '2012-12-09 15:04:11', NULL, 'sdfdsf', 'sdfsdf', '', '', '', '', '', '', '', '', 'sdfdsf', 'sdfsdf', NULL, NULL, '', '', '', NULL, 'cash', 'personalCollection', '', 0, 0),
(7, '2012-12-09 17:48:28', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(8, '2012-12-09 18:46:24', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'post', '', 0, 119),
(9, '2012-12-09 18:46:24', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'post', '', 0, 119),
(10, '2012-12-09 18:54:37', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(11, '2012-12-09 19:00:30', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'directDebit', 'post', '', 0, 89),
(12, '2012-12-09 19:03:08', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(13, '2012-12-09 20:29:56', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'post', '', 0, 119),
(14, '2012-12-09 20:34:01', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(15, '2012-12-09 20:37:40', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(16, '2012-12-09 20:42:07', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(17, '2012-12-09 20:43:59', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'post', '', 0, 119),
(18, '2012-12-09 20:56:11', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(19, '2012-12-09 20:57:55', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(20, '2012-12-09 21:00:31', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'directDebit', 'postWithCashOnDelivery', '', 0, 89),
(21, '2012-12-09 21:04:38', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'post', '', 0, 119),
(22, '2012-12-09 21:10:01', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(23, '2012-12-09 21:11:00', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(24, '2012-12-09 21:11:08', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(25, '2012-12-09 21:11:20', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(26, '2012-12-09 21:11:33', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(27, '2012-12-09 21:27:11', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(28, '2012-12-09 21:27:43', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(29, '2012-12-09 21:30:02', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'directDebit', 'personalCollection', '', 0, 0),
(30, '2012-12-09 21:31:10', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cash', 'personalCollection', '', 0, 0),
(31, '2012-12-09 21:37:51', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'post', '', 0, 119),
(32, '2012-12-09 22:12:19', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'post', '', 0, 119),
(33, '2012-12-09 22:15:50', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'post', '', 0, 119),
(34, '2012-12-09 23:31:07', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'directDebit', 'postWithCashOnDelivery', '', 0, 89),
(35, '2012-12-10 17:53:14', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'directDebit', 'post', '', 0, 89),
(36, '2012-12-10 23:17:58', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'directDebit', 'postWithCashOnDelivery', '', 0, 89),
(37, '2012-12-10 23:18:56', NULL, 'Jarda', 'sdfsdf', '', '', '', '', '', '', '', '', 'Jarda', 'sdfsdf', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119),
(38, '2012-12-12 21:08:28', NULL, 'asd', 'asf', '', '', '', '', '', '', '', '', 'asd', 'asf', NULL, NULL, '', '', '', NULL, 'directDebit', 'post', '', 0, 89),
(39, '2012-12-15 19:38:28', NULL, 'Jarda', 'Klimčík', '', '', '', '', '', '', '', '', 'Jarda', 'Klimčík', NULL, NULL, '', '', '', NULL, 'cashOnDelivery', 'postWithCashOnDelivery', '', 0, 119);

-- --------------------------------------------------------

--
-- Struktura tabulky `order_has_product`
--

CREATE TABLE IF NOT EXISTS `order_has_product` (
  `order_ord_id` int(11) NOT NULL,
  `product_prod_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `actual_price_of_product` int(11) NOT NULL,
  PRIMARY KEY (`order_ord_id`,`product_prod_id`),
  KEY `fk_order_has_product_order` (`order_ord_id`),
  KEY `fk_order_has_product_product` (`product_prod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `order_has_product`
--

INSERT INTO `order_has_product` (`order_ord_id`, `product_prod_id`, `quantity`, `actual_price_of_product`) VALUES
(38, 1, 10, 125),
(38, 5, 1, 27),
(39, 6, 2, 40);

-- --------------------------------------------------------

--
-- Struktura tabulky `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `prod_id` int(11) NOT NULL AUTO_INCREMENT,
  `prod_name` varchar(45) NOT NULL,
  `prod_producer` varchar(45) NOT NULL,
  `prod_price` int(11) NOT NULL,
  `prod_describe` varchar(255) DEFAULT NULL,
  `prod_isnew` tinyint(1) NOT NULL DEFAULT '0',
  `prod_on_stock` int(11) NOT NULL DEFAULT '0',
  `prod_is_active` tinyint(1) NOT NULL DEFAULT '1',
  `total_votes` int(11) NOT NULL DEFAULT '0',
  `total_value` int(11) NOT NULL DEFAULT '0',
  `used_ips` longtext NOT NULL,
  PRIMARY KEY (`prod_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Vypisuji data pro tabulku `product`
--

INSERT INTO `product` (`prod_id`, `prod_name`, `prod_producer`, `prod_price`, `prod_describe`, `prod_isnew`, `prod_on_stock`, `prod_is_active`, `total_votes`, `total_value`, `used_ips`) VALUES
(1, 'Vodní kobereček + pěnové puzzle zdarma', 'babyShop', 179, 'Výjimečná vodní podložka (součástí vodní pero) je dokonalou hračkou pro nejmenší už od 12. měsíců', 1, 0, 1, 0, 0, ''),
(2, 'Play-Doh - Pizza hrací set', 'babyShop', 279, 'Novinka Play-Doh. Děti si z této sady mohou připravit výbornou snídani s pizzou, palačinkami, vajíčky nebo vaflemi! Modelína s formou, se ktrou si užijete spousty zábavy.', 1, 0, 1, 0, 0, ''),
(3, 'Robot skládací cca 16 cm', 'babyShop', 55, 'Skládací robot, který se umí přeměnit v letadlo', 1, 0, 1, 0, 0, ''),
(4, 'Tučňáci POP N'' DROP', 'babyShop', 129, 'Zábavná rodinná hra Tučňáci je zábavnější forma klasické hry Člověče nezlob se', 1, 0, 1, 2, 9, 'a:2:{i:0;s:9:"127.1.0.1";i:1;s:9:"127.0.0.1";}'),
(5, 'Panenka hadrová 40 cm', 'babyShop', 115, 'Tradiční hadrová panenka, která je opravdu krásná. Po jednom kusu z každé barvy.', 1, 0, 1, 0, 0, ''),
(6, 'Panenka', 'babyShop', 419, 'Položíte-li ji do postýlky, zavře oči a pokud ji vezmete zpět do náruče tak otevře očka.', 1, 0, 1, 0, 0, ''),
(7, 'Rybka plastová pískací na kolečkách 9cm', 'babyShop', 15, 'Rybka plastová pískací na kolečkách 9cm', 1, 0, 1, 0, 0, ''),
(8, 'Mikrovlná trouba', 'babyShop', 215, 'Růžová mikrovlná trouba pro malé hospodyňky.', 0, 0, 1, 0, 0, ''),
(9, 'Dřevěné PUZZLE', 'babyShop', 32, 'Dřevěné puzzle s devíti díly. Tato tradiční dřevěná hračka zábavnou formou rozvíjí představivost a koordinaci dětí a učí trpělivosti.', 0, 0, 1, 0, 0, '');

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
  `user_name` varchar(45) DEFAULT NULL,
  `user_surname` varchar(45) DEFAULT NULL,
  `user_telefon` varchar(9) DEFAULT NULL,
  `user_street` varchar(45) DEFAULT NULL,
  `user_city` varchar(45) DEFAULT NULL,
  `user_psc` varchar(5) DEFAULT NULL,
  `user_firmName` varchar(45) DEFAULT NULL,
  `user_ico` varchar(8) DEFAULT NULL,
  `user_dic` varchar(12) DEFAULT NULL,
  `user_hash` varchar(255) DEFAULT NULL,
  `user_is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `fk_user_basket` (`basket_basket_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`user_id`, `user_password`, `user_email`, `basket_basket_id`, `user_name`, `user_surname`, `user_telefon`, `user_street`, `user_city`, `user_psc`, `user_firmName`, `user_ico`, `user_dic`, `user_hash`, `user_is_active`) VALUES
(1, '$2a$07$y5b4rwqnzua3bdb2q9hqmuvt.JyOygnsK/dpoShRvyen3aGvlxWtq', 'admin@admin.cz', NULL, 'Admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(3, '$2a$07$7enf3g58gn71xgu7m8eg8uO8ZmlWPz8g.FSIZbjOKN3g7ciwWjQuq', 'Jerry.klimcik@gmail.com', NULL, 'Jaroslav', 'Klimčík', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

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
  ADD CONSTRAINT `fk_category_has_product_category` FOREIGN KEY (`category_cat_id`) REFERENCES `category` (`cat_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_category_has_product_product` FOREIGN KEY (`product_prod_id`) REFERENCES `product` (`prod_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
  ADD CONSTRAINT `fk_image_product` FOREIGN KEY (`product_prod_id`) REFERENCES `product` (`prod_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `order_has_product`
--
ALTER TABLE `order_has_product`
  ADD CONSTRAINT `fk_order_has_product_order` FOREIGN KEY (`order_ord_id`) REFERENCES `orders` (`ord_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_order_has_product_product` FOREIGN KEY (`product_prod_id`) REFERENCES `product` (`prod_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_basket` FOREIGN KEY (`basket_basket_id`) REFERENCES `basket` (`basket_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
