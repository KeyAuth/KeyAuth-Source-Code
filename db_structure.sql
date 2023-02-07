SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `acclogs` (
  `id` int(11) NOT NULL,
  `username` varchar(49) NOT NULL,
  `date` varchar(10) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `useragent` varchar(199) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `accounts` (
  `username` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `ownerid` varchar(65) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `app` varchar(65) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `banned` varchar(99) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locked` int(1) NOT NULL DEFAULT '0',
  `warning` varchar(999) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin` int(1) NOT NULL DEFAULT '0',
  `img` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'https://cdn.keyauth.cc/assets/img/favicon.png',
  `balance` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `keylevels` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N/A',
  `expires` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registrationip` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastip` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `region` varchar(99) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asNum` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twofactor` int(1) NOT NULL DEFAULT '0',
  `googleAuthCode` varchar(59) COLLATE utf8_unicode_ci DEFAULT NULL,
  `darkmode` int(1) NOT NULL DEFAULT '0',
  `acclogs` int(1) NOT NULL DEFAULT '1',
  `lastreset` int(10) DEFAULT NULL,
  `emailVerify` int(1) NOT NULL DEFAULT '1',
  `permissions` bit(64) NOT NULL DEFAULT b'11111111111',
  `afCode` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `affiliatedBy` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `securityKey` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `afLogs` (
  `id` int(11) NOT NULL,
  `afCode` varchar(50) DEFAULT NULL,
  `referrer` varchar(3000) DEFAULT NULL,
  `username` varchar(65) DEFAULT NULL,
  `date` int(10) DEFAULT NULL,
  `action` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `apps` (
  `id` int(11) NOT NULL,
  `owner` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `ownerid` varchar(39) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` int(1) NOT NULL,
  `banned` int(1) NOT NULL DEFAULT '0',
  `paused` int(11) NOT NULL DEFAULT '0',
  `hwidcheck` int(1) NOT NULL,
  `vpnblock` int(1) NOT NULL DEFAULT '0',
  `sellerkey` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `ver` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1.0',
  `download` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hash` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `webhook` varchar(130) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auditLogWebhook` varchar(130) COLLATE utf8_unicode_ci DEFAULT NULL,
  `resellerstore` varchar(69) COLLATE utf8_unicode_ci DEFAULT NULL,
  `appdisabled` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'This application is disabled',
  `usernametaken` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Username Already Exists.',
  `keynotfound` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Key Not Found.',
  `keyused` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Key Already Used.',
  `nosublevel` varchar(100) COLLATE utf8_unicode_ci DEFAULT 'There is no subscription created for your key level. Contact application developer.',
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
  `loggedInMsg` varchar(99) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Logged in!',
  `pausedApp` varchar(99) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Application is currently paused, please wait for the developer to say otherwise.',
  `unTooShort` varchar(99) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Username too short, try longer one.',
  `pwLeaked` varchar(99) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'This password has been leaked in a data breach (not from us), please use a different one.',
  `chatHitDelay` varchar(99) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Chat slower, you''ve hit the delay limit',
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
  `sellappsecret` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sellappdayproduct` varchar(199) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sellappweekproduct` varchar(199) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sellappmonthproduct` varchar(199) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sellapplifetimeproduct` varchar(199) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cooldown` int(10) NOT NULL DEFAULT '604800',
  `panelstatus` int(1) NOT NULL DEFAULT '1',
  `session` int(10) NOT NULL DEFAULT '21600',
  `hashcheck` int(1) NOT NULL DEFAULT '0',
  `webdownload` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customDomain` varchar(253) COLLATE utf8_unicode_ci DEFAULT NULL,
  `format` varchar(99) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX',
  `amount` int(3) DEFAULT NULL,
  `lvl` int(3) DEFAULT NULL,
  `note` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `duration` int(3) DEFAULT NULL,
  `unit` int(3) DEFAULT NULL,
  `killOtherSessions` int(1) NOT NULL DEFAULT '0',
  `cooldownUnit` int(1) NOT NULL DEFAULT '86400',
  `sessionUnit` int(1) NOT NULL DEFAULT '3600',
  `minUsernameLength` int(1) NOT NULL DEFAULT '0',
  `blockLeakedPasswords` int(1) NOT NULL DEFAULT '0',
  `forceEncryption` int(1) NOT NULL DEFAULT '0',
  `customDomainAPI` varchar(253) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customerPanelIcon` varchar(99) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'https://cdn.keyauth.cc/front/assets/img/favicon.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `bans` (
  `id` int(11) NOT NULL,
  `hwid` varchar(2000) DEFAULT NULL,
  `ip` varchar(49) DEFAULT NULL,
  `type` varchar(5) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `buttons` (
  `id` int(11) NOT NULL,
  `text` varchar(99) NOT NULL,
  `value` varchar(99) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `chatmsgs` (
  `id` int(255) NOT NULL,
  `author` varchar(70) NOT NULL,
  `message` varchar(2000) NOT NULL,
  `timestamp` int(10) NOT NULL,
  `channel` varchar(50) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `chatmutes` (
  `id` int(11) NOT NULL,
  `user` varchar(70) NOT NULL,
  `time` int(10) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `chats` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `delay` int(10) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `emailverify` (
  `id` int(11) NOT NULL,
  `secret` varchar(32) NOT NULL,
  `email` varchar(40) NOT NULL,
  `time` int(1) NOT NULL,
  `region` varchar(99) DEFAULT NULL,
  `asNum` varchar(20) DEFAULT NULL,
  `newEmail` varchar(40) DEFAULT NULL,
  `newUsername` varchar(99) DEFAULT NULL,
  `oldUsername` varchar(99) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `files` (
  `pk` int(11) NOT NULL,
  `name` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `id` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `uploaddate` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `app` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `authed` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

CREATE TABLE `logs` (
  `id` int(1) NOT NULL,
  `logdate` varchar(49) COLLATE utf8_unicode_ci NOT NULL,
  `logdata` varchar(275) COLLATE utf8_unicode_ci NOT NULL,
  `credential` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pcuser` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logapp` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `resets` (
  `id` int(11) NOT NULL,
  `secret` char(32) NOT NULL,
  `email` varchar(65) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `securityKeys` (
  `id` int(11) NOT NULL,
  `username` varchar(65) DEFAULT NULL,
  `name` varchar(99) DEFAULT NULL,
  `credentialId` varchar(999) DEFAULT NULL,
  `credentialPublicKey` varchar(999) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sellerLogs` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `path` varchar(999) NOT NULL,
  `date` int(10) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sessions` (
  `id` varchar(10) NOT NULL,
  `credential` varchar(255) DEFAULT NULL,
  `app` varchar(64) NOT NULL,
  `expiry` int(10) NOT NULL,
  `created_at` int(10) DEFAULT NULL,
  `enckey` varchar(100) DEFAULT NULL,
  `validated` int(1) NOT NULL DEFAULT '0',
  `ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `subs` (
  `id` int(255) NOT NULL,
  `user` varchar(49) NOT NULL,
  `subscription` varchar(49) NOT NULL,
  `expiry` varchar(49) NOT NULL,
  `app` varchar(64) NOT NULL,
  `key` varchar(49) DEFAULT NULL,
  `paused` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `name` varchar(49) NOT NULL,
  `level` varchar(49) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hwid` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(65) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `createdate` int(10) DEFAULT NULL,
  `lastlogin` int(10) DEFAULT NULL,
  `banned` varchar(99) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(49) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cooldown` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `uservars` (
  `id` int(11) NOT NULL,
  `name` varchar(99) NOT NULL,
  `data` varchar(500) NOT NULL,
  `user` varchar(70) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `vars` (
  `id` int(11) NOT NULL,
  `varid` varchar(49) NOT NULL,
  `msg` varchar(20000) NOT NULL,
  `app` varchar(64) NOT NULL,
  `authed` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `webhooks` (
  `id` int(11) NOT NULL,
  `webid` varchar(10) NOT NULL,
  `baselink` varchar(200) NOT NULL,
  `useragent` varchar(49) NOT NULL DEFAULT 'KeyAuth',
  `app` varchar(64) NOT NULL,
  `authed` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `acclogs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `accounts`
  ADD PRIMARY KEY (`username`);

ALTER TABLE `afLogs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `apps`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bans`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `buttons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `value` (`value`,`app`);

ALTER TABLE `chatmsgs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `chatmutes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `one name per app` (`name`,`app`);

ALTER TABLE `emailverify`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `files`
  ADD PRIMARY KEY (`pk`);

ALTER TABLE `keys`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `resets`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `securityKeys`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sellerLogs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sessions`
  ADD KEY `session index` (`id`,`app`);

ALTER TABLE `subs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user index` (`username`,`app`);

ALTER TABLE `uservars`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user vars` (`name`,`user`,`app`);

ALTER TABLE `vars`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `webhooks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `baselink` (`baselink`);


ALTER TABLE `acclogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `afLogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `apps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `buttons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chatmsgs`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chatmutes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `emailverify`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `files`
  MODIFY `pk` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `logs`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT;

ALTER TABLE `resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `securityKeys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `sellerLogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `subs`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `uservars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `vars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `webhooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;