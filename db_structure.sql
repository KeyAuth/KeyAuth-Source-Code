-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jan 02, 2022 at 07:08 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `keyauth`
--

-- --------------------------------------------------------

--
-- Table structure for table `acclogs`
--

CREATE TABLE `acclogs` (
  `username` varchar(49) NOT NULL,
  `date` varchar(10) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `useragent` varchar(199) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `username` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `ownerid` varchar(65) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `app` varchar(65) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `banned` varchar(69) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin` int(1) NOT NULL DEFAULT 0,
  `img` varchar(90) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'https://i.imgur.com/cVPXjIH.jpg',
  `balance` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `keylevels` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N/A',
  `expires` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registrationip` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastip` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twofactor` int(1) NOT NULL DEFAULT 0,
  `googleAuthCode` varchar(59) COLLATE utf8_unicode_ci DEFAULT NULL,
  `darkmode` int(1) NOT NULL DEFAULT 0,
  `acclogs` int(1) NOT NULL DEFAULT 1,
  `format` varchar(99) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` int(3) DEFAULT NULL,
  `lvl` int(3) DEFAULT NULL,
  `note` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `duration` int(3) DEFAULT NULL,
  `lastreset` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `apps`
--

CREATE TABLE `apps` (
  `owner` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `ownerid` varchar(39) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` int(1) NOT NULL,
  `banned` int(1) NOT NULL DEFAULT 0,
  `paused` int(11) NOT NULL DEFAULT 0,
  `hwidcheck` int(1) NOT NULL,
  `vpnblock` int(1) NOT NULL DEFAULT 0,
  `sellerkey` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `ver` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1.0',
  `download` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `webdownload` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hash` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `webhook` varchar(130) COLLATE utf8_unicode_ci DEFAULT NULL,
  `resellerstore` varchar(69) COLLATE utf8_unicode_ci DEFAULT NULL,
  `appdisabled` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'This application is disabled',
  `usernametaken` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Username Already Exists.',
  `keynotfound` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Key Not Found.',
  `keyused` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Key Already Used.',
  `nosublevel` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'There is no subscription created for your key level. Contact application developer.',
  `usernamenotfound` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Username not found.',
  `passmismatch` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Password does not match.',
  `hwidmismatch` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'HWID Doesn''t match. Ask for key reset.',
  `noactivesubs` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No active subscriptions found.',
  `hwidblacked` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'You''ve been blacklisted from our application',
  `pausedsub` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Your Key is paused and cannot be used at the moment.',
  `vpnblocked` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'VPNs are disallowed on this application',
  `keybanned` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Your license is banned',
  `userbanned` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'The user is banned',
  `sessionunauthed` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Session is not validated',
  `hashcheckfail` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'This program hash does not match, make sure you''re using latest version',
  `keyexpired` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Key has expired.',
  `sellixsecret` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sellixdayproduct` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sellixweekproduct` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sellixmonthproduct` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sellixlifetimeproduct` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shoppysecret` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shoppydayproduct` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shoppyweekproduct` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shoppymonthproduct` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shoppylifetimeproduct` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cooldown` int(10) NOT NULL DEFAULT 604800,
  `panelstatus` int(1) NOT NULL DEFAULT 1,
  `session` int(10) NOT NULL DEFAULT 21600,
  `hashcheck` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bans`
--

CREATE TABLE `bans` (
  `hwid` varchar(100) DEFAULT NULL,
  `ip` varchar(49) DEFAULT NULL,
  `type` varchar(5) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `buttons`
--

CREATE TABLE `buttons` (
  `id` int(11) NOT NULL,
  `text` varchar(99) NOT NULL,
  `value` varchar(99) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `chatmsgs`
--

CREATE TABLE `chatmsgs` (
  `id` int(255) NOT NULL,
  `author` varchar(70) NOT NULL,
  `message` varchar(2000) NOT NULL,
  `timestamp` int(10) NOT NULL,
  `channel` varchar(50) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `chatmutes`
--

CREATE TABLE `chatmutes` (
  `user` varchar(70) NOT NULL,
  `time` int(10) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `name` varchar(50) NOT NULL,
  `delay` int(10) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `name` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `id` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `uploaddate` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `app` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `authed` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keys`
--

CREATE TABLE `keys` (
  `id` int(11) NOT NULL,
  `key` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `note` varchar(69) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expires` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `level` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `genby` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `gendate` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `usedon` int(10) DEFAULT NULL,
  `usedby` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `app` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `banned` varchar(99) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `logdate` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `logdata` varchar(275) COLLATE utf8_unicode_ci NOT NULL,
  `credential` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pcuser` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logapp` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resets`
--

CREATE TABLE `resets` (
  `secret` char(32) NOT NULL,
  `email` varchar(65) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(10) NOT NULL,
  `credential` varchar(255) DEFAULT NULL,
  `app` varchar(64) NOT NULL,
  `expiry` int(10) NOT NULL,
  `enckey` varchar(64) NOT NULL,
  `validated` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subs`
--

CREATE TABLE `subs` (
  `id` int(255) NOT NULL,
  `user` varchar(49) NOT NULL,
  `subscription` varchar(49) NOT NULL,
  `expiry` varchar(49) NOT NULL,
  `app` varchar(64) NOT NULL,
  `key` varchar(49) DEFAULT NULL,
  `paused` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `name` varchar(49) NOT NULL,
  `level` varchar(49) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(70) NOT NULL,
  `password` varchar(70) DEFAULT NULL,
  `hwid` varchar(2000) DEFAULT NULL,
  `app` varchar(64) NOT NULL,
  `owner` varchar(65) DEFAULT NULL,
  `createdate` int(10) DEFAULT NULL,
  `lastlogin` int(10) DEFAULT NULL,
  `banned` varchar(99) DEFAULT NULL,
  `ip` varchar(49) DEFAULT NULL,
  `cooldown` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uservars`
--

CREATE TABLE `uservars` (
  `name` varchar(99) NOT NULL,
  `data` varchar(500) NOT NULL,
  `user` varchar(70) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `vars`
--

CREATE TABLE `vars` (
  `varid` varchar(49) NOT NULL,
  `msg` varchar(20000) NOT NULL,
  `app` varchar(64) NOT NULL,
  `authed` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `webhooks`
--

CREATE TABLE `webhooks` (
  `webid` varchar(10) NOT NULL,
  `baselink` varchar(200) NOT NULL,
  `useragent` varchar(49) NOT NULL DEFAULT 'KeyAuth',
  `app` varchar(64) NOT NULL,
  `authed` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `buttons`
--
ALTER TABLE `buttons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `value` (`value`,`app`);

--
-- Indexes for table `chatmsgs`
--
ALTER TABLE `chatmsgs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`name`,`app`);

--
-- Indexes for table `keys`
--
ALTER TABLE `keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subs`
--
ALTER TABLE `subs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `username` (`username`,`app`);

--
-- Indexes for table `uservars`
--
ALTER TABLE `uservars`
  ADD UNIQUE KEY `user vars` (`name`,`user`,`app`);

--
-- Indexes for table `webhooks`
--
ALTER TABLE `webhooks`
  ADD KEY `baselink` (`baselink`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buttons`
--
ALTER TABLE `buttons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chatmsgs`
--
ALTER TABLE `chatmsgs`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keys`
--
ALTER TABLE `keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subs`
--
ALTER TABLE `subs`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
