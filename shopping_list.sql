-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 04, 2020 at 08:49 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shopping_list`
--
CREATE DATABASE IF NOT EXISTS `shopping_list` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `shopping_list`;

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_article_to_list` (IN `id_list` INT(11), IN `id_article` INT(11), IN `quanity` FLOAT(6,2))  NO SQL
BEGIN
    INSERT INTO 
        `list_articles`(id_list, id_article, article_quanity)
    VALUES
        (id_list, id_article, quanity);
        
	UPDATE
    	`lists`
	SET
    	`lists`.`last_edit` = CURRENT_TIMESTAMP;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `add_subowner` (IN `id_list` INT(11), IN `id_subowner` INT(11))  NO SQL
BEGIN
    SELECT COUNT(*) into @owner FROM list_owner WHERE list_owner.id_list = id_list AND list_owner.id_owner = id_subowner;

    IF (@owner = 0) THEN
        INSERT INTO list_owner(id_list, id_owner) VALUES(id_list, id_subowner);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_article` (IN `id_list` INT(11), IN `id_article` INT(11))  NO SQL
BEGIN 
    DELETE FROM
        `list_articles`
    WHERE
    	`list_articles`.`id_list` = id_list AND
        `list_articles`.`id_article` = id_article;
        
    UPDATE 
    	`lists`
	SET 
    	`lists`.`last_edit` = CURRENT_TIMESTAMP;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_list` (IN `id_list` INT(11))  NO SQL
BEGIN
	DELETE FROM list_articles WHERE list_articles.id_list = id_list;
    
    DELETE FROM list_owner WHERE list_owner.id_list = id_list;
    
    DELETE FROM lists WHERE lists.id_list = id_list;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list_owners` (IN `id_list` INT(11))  NO SQL
SELECT
	list_owner.id_owner,
    users.email
FROM
	list_owner,
    users
WHERE
	list_owner.id_list = id_list AND
    list_owner.id_owner = users.id_user$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list_value` (IN `id_list` INT(11))  NO SQL
SELECT 
	list_articles.id_list, 
    sum(article_price*article_quanity) 
FROM 
	list_articles 
WHERE 
	list_articles.id_list = id_list AND 
    article_bought = 1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_unit_for_article` (IN `id_article` INT(11))  NO SQL
BEGIN
    SELECT 
        units.id_unit,
        units.name
    FROM 
        units, articles 
    WHERE 
        articles.id_article = id_article AND
        articles.id_unit=units.id_unit;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `list_details` (IN `listId` INT(11))  NO SQL
SELECT 
	lists.name as 'list', 
    articles.name 'article',
    articles.id_article as 'article id',
    categories.name as 'category', 
    units.name as 'unit',
    if(list_articles.article_bought != 0,'true','false') as 'status',
    list_articles.article_price as 'price',
    list_articles.article_quanity as 'quanity'
FROM 
	lists, articles, units, list_articles, categories 
WHERE 
	lists.id_list = listId AND 
    list_articles.id_list = lists.id_list AND
    list_articles.id_article = articles.id_article AND
    articles.id_unit = units.id_unit AND
    categories.id_category = articles.id_category
GROUP BY
	list_articles.id_article$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_article_price` (IN `id_list` INT(11), IN `id_article` INT(11), IN `article_price` FLOAT(6,2))  NO SQL
BEGIN
    UPDATE 
        `list_articles` 
    SET 
        `article_bought` = true,
        `article_price` = article_price
    WHERE 
        `list_articles`.`id_list` = id_list AND 
        `list_articles`.`id_article` = id_article;

    UPDATE
        `lists`
    SET 
        `lists`.`last_edit` = CURRENT_TIMESTAMP
    WHERE 
        `lists`.`id_list` = id_list;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_article_quanity` (IN `quanity` FLOAT(6,2), IN `listId` INT(11), IN `articleId` INT(11))  NO SQL
BEGIN

    UPDATE `list_articles` SET `article_quanity` = quanity WHERE `list_articles`.`id_list` = listId AND `list_articles`.`id_article` = articleId;

    UPDATE `lists` SET `last_edit` = CURRENT_TIMESTAMP WHERE `lists`.`id_list` = listId;

END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `check_if_article_is_on_list` (`idArticle` INT(11), `idList` INT(11)) RETURNS TINYINT(1) NO SQL
BEGIN

    SELECT COUNT(*) into @articles FROM list_articles WHERE list_articles.id_article = idArticle AND list_articles.id_list = idList;

    IF (@articles != 0) THEN
        RETURN true;
    ELSE
        RETURN false;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `check_list_name` (`list_name` VARCHAR(255) CHARSET utf8) RETURNS TINYINT(1) NO SQL
BEGIN
    SELECT count(*) into @list FROM lists WHERE lists.name = list_name;
    IF (@list != 0) THEN
        RETURN true;
    ELSE 
        RETURN false;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id_article` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `id_category` int(11) NOT NULL,
  `id_unit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id_article`, `name`, `id_category`, `id_unit`) VALUES
(1, 'Banana / Count', 1, 7),
(2, 'Bananas / Package', 1, 8),
(3, 'Bananas / Kilogram', 1, 4),
(4, 'Clementine / Count', 1, 7),
(5, 'Clementines / Package', 1, 8),
(6, 'Clementines / Kilogram', 1, 4),
(7, 'Lemon / Count', 1, 7),
(8, 'Lemons / Package', 1, 8),
(9, 'Lemons / Kilogram', 1, 4),
(10, 'Strawberries / Package', 1, 8),
(11, 'Strawberries / Kilogram', 1, 4),
(12, 'Apple / Count', 1, 7),
(13, 'Apples / Package', 1, 8),
(14, 'Apples / Kilogram', 1, 4),
(15, 'Grapes / Package', 1, 8),
(16, 'Grapes / Kilogram', 1, 4),
(17, 'Lime / Count', 1, 7),
(18, 'Limes / Package', 1, 8),
(19, 'Limes / Kilogram', 1, 4),
(20, 'Kiwi / Count', 1, 7),
(21, 'Kiwi / Package', 1, 8),
(22, 'Kiwi / Kilogram', 1, 4),
(23, 'Pineapple / Count', 1, 7),
(24, 'Pineapple / Package', 1, 8),
(25, 'Pineapple / Kilogram', 1, 4),
(26, 'Raspberries / Package', 1, 8),
(27, 'Raspberries / Kilogram', 1, 4),
(28, 'Cucumber / Count', 1, 7),
(29, 'Cucumber / Package', 1, 8),
(30, 'Cucumber / Kilogream', 1, 4),
(31, 'Zucchini / Count', 1, 7),
(32, 'Zucchini / Package', 1, 8),
(33, 'Zucchini / Kilogram', 1, 4),
(34, 'Iceberg Lettuce / Count', 1, 7),
(35, 'Iceberg Lettuce / Package', 1, 8),
(36, 'Iceberg Lettuce / Kilogram', 1, 4),
(37, 'Spinach', 1, 8),
(38, 'Green Bell Pepper / Count', 1, 7),
(39, 'Green Bell Pepper / Package', 1, 8),
(40, 'Green Bell Pepper / Kilogrma', 1, 4),
(41, 'Tomatoe / Count', 1, 7),
(42, 'Tomatoes / Package', 1, 8),
(43, 'Tomatoes / Kilogram', 1, 4),
(44, 'Potato / Count', 1, 7),
(45, 'Potatoes / Package', 1, 8),
(46, 'Potatoes / Kilogram', 1, 4),
(47, 'Corn / Count', 1, 7),
(48, 'Corn / Package', 1, 8),
(49, 'Corn / Kilogram', 1, 4),
(50, 'Garlic / Count', 1, 7),
(51, 'Garlic / Package', 1, 8),
(52, 'Garlic / Kilogram', 1, 4),
(53, 'Asparagus / Package', 1, 8),
(54, 'Asparagus / Kilogram', 1, 4),
(55, 'Chicken', 2, 8),
(56, 'Beef', 2, 8),
(57, 'Pork', 2, 8),
(58, 'Turkey', 2, 8),
(59, 'Salmon', 2, 8),
(60, 'Shrimp', 2, 8),
(61, 'Cod', 2, 8),
(62, 'Tilapia', 2, 8),
(63, 'Mussel', 2, 8),
(64, 'Crawfish', 2, 8),
(65, 'Chicken', 2, 7),
(66, 'Beef', 2, 7),
(67, 'Pork', 2, 7),
(68, 'Turkey', 2, 7),
(69, 'Salmon', 2, 7),
(70, 'Shrimp', 2, 7),
(71, 'Cod', 2, 7),
(72, 'Tilapia', 2, 7),
(73, 'Mussel', 2, 7),
(74, 'Crawfish', 2, 7),
(84, 'Cheese', 3, 8),
(85, 'Milk', 3, 8),
(86, 'Yogurt', 3, 8),
(87, 'Eggs', 3, 8),
(88, 'Butter & Margarine', 3, 8),
(89, 'Sliced Bread', 4, 7),
(90, 'Bread', 4, 8),
(91, 'Rolls & Buns', 4, 7),
(92, 'Tortillas', 4, 8),
(93, 'Donut', 4, 7),
(94, 'Donuts', 4, 8),
(95, 'Cakes & Cupcakes', 4, 7),
(96, 'Cookies & Brownies', 4, 8),
(97, 'Chips & Dip', 5, 8),
(98, 'Cookies & Crackers', 5, 8),
(99, 'Fruit Snacks', 5, 8),
(100, 'Popcorn & Pretzels', 5, 8),
(101, 'Nuts & Dried Fruits', 5, 8),
(102, 'Chocolate', 5, 8),
(103, 'Sour Candy', 5, 8),
(104, 'Sweet Candy', 5, 8),
(105, 'Gums', 5, 8),
(106, 'Bulk Beverages', 6, 8),
(107, 'Soft Drink', 6, 7),
(108, 'Water', 6, 7),
(109, 'Energy Drink', 6, 7),
(110, 'Coffe', 6, 8),
(111, 'Tea', 6, 8),
(112, 'Fresh Juice', 6, 2),
(113, 'Beer', 7, 7),
(114, 'Wine', 7, 7),
(115, 'Liquor', 7, 7),
(116, 'Coctail', 7, 7),
(117, 'Frozen Ice Cream', 8, 8),
(118, 'Frozen Pizza & Pasta', 8, 8),
(119, 'Frozen Bread', 8, 8),
(120, 'Frozen Fruits & Vegetables', 8, 8),
(121, 'Frozen Meat & Seafood', 8, 8),
(122, 'Vitamins & Supplements', 9, 8),
(123, 'Cough, Cold & Flu medicine', 9, 8),
(124, 'Allergy medicine', 9, 8),
(125, 'First Aid', 9, 8),
(126, 'Painkillers', 9, 8),
(127, 'Sunscreens', 10, 7),
(128, 'Oral Care', 10, 8),
(129, 'Shave', 10, 7),
(130, 'Hair Care', 10, 7),
(131, 'Makeup', 10, 7),
(132, 'Skin Care', 10, 7),
(133, 'Diapers', 11, 8),
(134, 'Wipes', 11, 8),
(135, 'Feeding', 11, 8),
(136, 'Dog Food', 12, 7),
(137, 'Dog Treats & Chews', 12, 8),
(138, 'Dog Supplies', 12, 8),
(139, 'Cat Food', 12, 8),
(140, 'Cat Treats', 12, 8),
(141, 'Cat Litter & Accessories', 12, 8),
(142, 'Birds, Fish & Other Pets', 12, 8),
(143, 'Bakeware', 13, 7),
(144, 'Coffe, Tea & Espresso', 13, 7),
(145, 'Drinkware & Bar', 13, 7),
(146, 'Home Decor', 13, 7),
(147, 'Storage & Organization', 13, 7),
(152, 'Printers & Ink', 14, 7),
(153, 'Headphones & Speakers', 14, 7),
(154, 'TV & TV Accessories', 14, 7),
(155, 'Electrical Tools', 15, 7),
(156, 'Gardening & Lawn Care', 15, 8),
(157, 'Grills & Outdoor Cooking', 15, 7),
(158, 'Cycling', 16, 7),
(159, 'Camping & Hiking', 16, 7),
(160, 'Golf', 16, 7),
(161, 'Exercise & Fitness', 16, 7),
(166, 'artykuł2', 8, 8);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id_category` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id_category`, `name`) VALUES
(7, 'Alcohol'),
(11, 'Baby'),
(10, 'Beauty & Personal Care'),
(6, 'Beverages'),
(4, 'Bread & Bakery'),
(3, 'Eggs & Diary'),
(8, 'Frozen'),
(1, 'Fruits & Vegetables'),
(15, 'Garden & Tools'),
(9, 'Health & Nutrition'),
(13, 'Home & Kitchen'),
(2, 'Meat & Seafood'),
(14, 'Office & Electronics'),
(12, 'Pets'),
(5, 'Snacks & Candy'),
(16, 'Sports & Outdoor');

-- --------------------------------------------------------

--
-- Table structure for table `lists`
--

CREATE TABLE `lists` (
  `id_list` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_edit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lists`
--

INSERT INTO `lists` (`id_list`, `name`, `create_date`, `last_edit`) VALUES
(1, 'admin@admin.com_moja lista', '2020-11-23 18:58:53', '2020-12-04 19:14:51'),
(2, 'admin@admin.com_moja druga lista', '2020-11-23 18:59:31', '2020-12-04 19:14:51'),
(9, 'admin@admin.com_aaa', '2020-11-28 16:33:42', '2020-12-04 19:15:06'),
(15, 'admin@admin.com_litsa zakupowa ', '2020-11-28 16:50:36', '2020-12-04 19:14:51'),
(16, 'krystian@gmail.com_moja lista', '2020-11-28 16:51:37', '2020-12-04 19:14:51');

-- --------------------------------------------------------

--
-- Table structure for table `list_articles`
--

CREATE TABLE `list_articles` (
  `id_list` int(11) NOT NULL,
  `id_article` int(11) NOT NULL,
  `article_bought` tinyint(1) NOT NULL DEFAULT '0',
  `article_price` float(6,2) DEFAULT NULL,
  `article_quanity` float(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `list_articles`
--

INSERT INTO `list_articles` (`id_list`, `id_article`, `article_bought`, `article_price`, `article_quanity`) VALUES
(1, 1, 1, 1.00, 0.23),
(1, 3, 1, 1.00, 0.70),
(1, 6, 1, 10.00, 1.78),
(1, 1, 0, NULL, 15.00),
(2, 127, 1, 1.00, 1.25),
(2, 84, 0, NULL, 0.33),
(2, 122, 0, NULL, 4.22),
(9, 108, 1, 2.55, 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `list_owner`
--

CREATE TABLE `list_owner` (
  `id_list` int(11) NOT NULL,
  `id_owner` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `list_owner`
--

INSERT INTO `list_owner` (`id_list`, `id_owner`) VALUES
(2, 1),
(2, 3),
(9, 1),
(15, 1),
(15, 3),
(16, 3),
(1, 1),
(1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id_unit` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id_unit`, `name`) VALUES
(7, 'Count'),
(4, 'Kilogram'),
(2, 'Litre'),
(8, 'Package');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_role` enum('user','administrator') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `login`, `email`, `password`, `user_role`) VALUES
(1, 'admin', 'admin@admin.com', '$2y$10$Cr4JsUcVPJW9bA0SFt/wH.y7seNroTHrWbpaiV.47zPjL0aciVs..', 'administrator'),
(3, 'krystian', 'krystian@gmail.com', '$2y$10$Cr4JsUcVPJW9bA0SFt/wH.y7seNroTHrWbpaiV.47zPjL0aciVs..', 'user'),
(4, 'user', 'user@gmail.com', '$2y$10$Cr4JsUcVPJW9bA0SFt/wH.y7seNroTHrWbpaiV.47zPjL0aciVs..', 'user'),
(5, 'user2', 'user2@domena.com.pl', '$2y$10$Cr4JsUcVPJW9bA0SFt/wH.y7seNroTHrWbpaiV.47zPjL0aciVs..', 'user'),
(7, 'user3', 'user3@user.domena.com', '$2y$10$pieW6SCgQyYY2/3BLFLgKew8rk0G5L4DH4Bto5reMjoNjcA9mYpXC', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id_article`),
  ADD KEY `id_category` (`id_category`),
  ADD KEY `id_unit` (`id_unit`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_category`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `lists`
--
ALTER TABLE `lists`
  ADD PRIMARY KEY (`id_list`);

--
-- Indexes for table `list_articles`
--
ALTER TABLE `list_articles`
  ADD KEY `id_article` (`id_article`),
  ADD KEY `id_list` (`id_list`);

--
-- Indexes for table `list_owner`
--
ALTER TABLE `list_owner`
  ADD KEY `id_owner` (`id_owner`),
  ADD KEY `id_list` (`id_list`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id_unit`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id_article` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `lists`
--
ALTER TABLE `lists`
  MODIFY `id_list` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id_unit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id_category`),
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`id_unit`) REFERENCES `units` (`id_unit`);

--
-- Constraints for table `list_articles`
--
ALTER TABLE `list_articles`
  ADD CONSTRAINT `list_articles_ibfk_3` FOREIGN KEY (`id_list`) REFERENCES `lists` (`id_list`),
  ADD CONSTRAINT `list_articles_ibfk_4` FOREIGN KEY (`id_article`) REFERENCES `articles` (`id_article`);

--
-- Constraints for table `list_owner`
--
ALTER TABLE `list_owner`
  ADD CONSTRAINT `list_owner_ibfk_3` FOREIGN KEY (`id_owner`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `list_owner_ibfk_4` FOREIGN KEY (`id_list`) REFERENCES `lists` (`id_list`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
