-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 15. Aug 2020 um 15:18
-- Server-Version: 10.3.23-MariaDB-1
-- PHP-Version: 7.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `astronauth`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `accounts`
--

CREATE TABLE `accounts` (
  `account_id` varchar(8) NOT NULL,
  `account_name` varchar(32) NOT NULL,
  `account_email` varchar(254) NOT NULL,
  `account_pwhash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `devices`
--

CREATE TABLE `devices` (
  `device_key` varchar(64) NOT NULL,
  `device_tokenhash` varchar(255) NOT NULL,
  `device_timestamp` bigint(20) NOT NULL,
  `device_active` tinyint(1) NOT NULL,
  `device_account_id` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `account_name` (`account_name`),
  ADD UNIQUE KEY `account_email` (`account_email`);

--
-- Indizes für die Tabelle `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`device_key`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
