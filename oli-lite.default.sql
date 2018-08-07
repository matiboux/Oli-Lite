/*\
|*|  -----------------------------------------
|*|  --- [  Oli Lite - Default SQL file  ] ---
|*|  -----------------------------------------
|*|  / Built for Oli Lite Beta 1.0.0
|*|  
|*|  This is the default SQL file for Oli Lite, a simple and open source PHP Framework.
|*|  You can use this SQL template to setup a MySQL database to use with the framework.
|*|  Created and developed by Matiboux (Mathieu Guérin).
|*|   
|*|  Oli Lite Github repository: https://github.com/OliFramework/Oli-Lite/
|*|   — see more infos in the README.md file on the repository.
|*|  
|*|  --- --- ---
|*|  
|*|  Summary:
|*|  
|*|  I. Table `settings`
|*|  II. Table `shortcut_links`
\*/

-- phpMyAdmin SQL Dump
-- version 4.7.0-rc1
-- https://www.phpmyadmin.net/
--
-- PHP Version: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- *** *** *** --

-- I. Table `settings`

	-- I. A. Create the table

	CREATE TABLE `settings` (
	  `name` varchar(64) NOT NULL,
	  `value` text
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	-- I. B. Insert the data

	INSERT INTO `settings` (`name`, `value`) VALUES
	('url', 'urwebs.it/'),
	('name', 'Your own Oli website!'),
	('description', 'Is that your website?'),
	('version', '1.0'),
	('creation_date', '2018-08-07'),
	('status', ''),
	('owner', '');

	-- I. 1. C. Extras

	ALTER TABLE `settings`
	  ADD PRIMARY KEY (`name`);

-- I. 2. Table `shortcut_links`

	-- I. 2. A. Create the table
	CREATE TABLE `shortcut_links` (
	  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
	  `url` varchar(256) COLLATE utf8_unicode_ci NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

	-- III. 2. B. Insert the data

	INSERT INTO `shortcut_links` (`name`, `url`) VALUES
	('Oli', 'https://github.com/OliFramework/Oli/');
	('Oli Lite', 'https://github.com/OliFramework/Oli-Lite/');

	-- III. 2. C. Extras

	ALTER TABLE `shortcut_links`
	  ADD PRIMARY KEY (`name`);

-- *** *** *** --

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
