-- phpMyAdmin SQL Dump
-- version OVH
-- https://www.phpmyadmin.net/
--
-- Host: omitted
-- Generation Time: Jun 23, 2021 at 11:06 PM
-- Server version: 5.6.50-log
-- PHP Version: 7.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Table structure for table `acclogs`
--

CREATE TABLE `acclogs` (
  `username` varchar(49) NOT NULL,
  `date` varchar(10) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `useragent` varchar(199) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `username` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `ownerid` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `app` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `owner` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `isbanned` int(1) NOT NULL,
  `img` varchar(90) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'https://i.imgur.com/cVPXjIH.jpg',
  `pp` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `dayrate` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `weekrate` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `monthrate` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `threemonthrate` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sixmonthrate` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `liferate` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `balance` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `expires` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `registrationip` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lastip` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `twofactor` int(1) NOT NULL DEFAULT '0',
  `googleAuthCode` varchar(59) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `darkmode` int(1) NOT NULL DEFAULT '0',
  `format` varchar(99) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `apps`
--

CREATE TABLE `apps` (
  `owner` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `ownerid` varchar(39) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` int(1) NOT NULL,
  `paused` int(11) NOT NULL DEFAULT '0',
  `hwidcheck` int(1) NOT NULL,
  `sellerkey` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `ver` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1.0',
  `download` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `webhook` varchar(130) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `resellerstore` varchar(69) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `appdisabled` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'This application is disabled',
  `usernametaken` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Username Already Exists.',
  `keynotfound` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Key Not Found.',
  `keyused` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Key Already Used.',
  `nosublevel` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'There is no subscription created for your key level. Contact appplicaton developer.',
  `usernamenotfound` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Username not found.',
  `passmismatch` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Password does not match.',
  `hwidmismatch` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'HWID Doesn''t match. Ask for key reset.',
  `noactivesubs` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No active subscriptions found.',
  `hwidblacked` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'You''ve been blacklisted from our application',
  `keypaused` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Your Key is paused and cannot be used at the moment.',
  `keyexpired` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Key has expired.',
  `sellixsecret` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `dayproduct` varchar(13) COLLATE utf8_unicode_ci NOT NULL,
  `weekproduct` varchar(13) COLLATE utf8_unicode_ci NOT NULL,
  `monthproduct` varchar(13) COLLATE utf8_unicode_ci NOT NULL,
  `lifetimeproduct` varchar(13) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `bans`
--

CREATE TABLE `bans` (
  `hwid` varchar(100) DEFAULT NULL,
  `ip` varchar(49) DEFAULT NULL,
  `type` varchar(5) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `name` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `id` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `size` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `uploaddate` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `uploader` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `app` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `keys`
--

CREATE TABLE `keys` (
  `key` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `note` varchar(69) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `expires` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `lastlogin` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `hwid` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `level` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `genby` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `gendate` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `app` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `cooldown` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `banned` varchar(99) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ip` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `logdate` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `logdata` varchar(275) COLLATE utf8_unicode_ci NOT NULL,
  `logkey` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `logowner` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `logapp` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(10) NOT NULL,
  `credential` varchar(255) DEFAULT NULL,
  `app` varchar(64) NOT NULL,
  `expiry` int(10) NOT NULL,
  `enckey` varchar(64) NOT NULL,
  `validated` varchar(5) NOT NULL DEFAULT 'false'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `subs`
--

CREATE TABLE `subs` (
  `user` varchar(49) NOT NULL,
  `subscription` varchar(49) NOT NULL,
  `expiry` varchar(49) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `name` varchar(49) NOT NULL,
  `level` varchar(49) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(70) NOT NULL,
  `password` varchar(70) NOT NULL,
  `expires` varchar(70) NOT NULL DEFAULT '',
  `hwid` varchar(70) NOT NULL,
  `app` varchar(64) NOT NULL,
  `banned` varchar(99) DEFAULT NULL,
  `ip` varchar(49) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `vars`
--

CREATE TABLE `vars` (
  `varid` varchar(49) NOT NULL,
  `msg` varchar(99) NOT NULL,
  `app` varchar(64) NOT NULL,
  `author` varchar(49) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `webhooks`
--

CREATE TABLE `webhooks` (
  `webid` varchar(10) NOT NULL,
  `baselink` varchar(200) NOT NULL,
  `useragent` varchar(49) NOT NULL DEFAULT 'KeyAuth',
  `app` varchar(64) NOT NULL,
  `owner` varchar(40) NOT NULL
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
-- Indexes for table `webhooks`
--
ALTER TABLE `webhooks`
  ADD KEY `baselink` (`baselink`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
