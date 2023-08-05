SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `acclogs` (
  `id` int NOT NULL,
  `username` varchar(65) DEFAULT NULL,
  `date` varchar(10) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `useragent` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `accounts` (
  `username` varchar(65) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ownerid` varchar(65) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `role` varchar(65) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `app` varchar(65) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner` varchar(49) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `banned` varchar(99) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `locked` int NOT NULL DEFAULT '0',
  `warning` varchar(999) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin` int NOT NULL DEFAULT '0',
  `img` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'https://cdn.keyauth.cc/assets/img/favicon.png',
  `balance` varchar(49) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `keylevels` varchar(49) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N/A',
  `expires` varchar(49) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `registrationip` varchar(49) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastip` varchar(49) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `region` varchar(99) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `asNum` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `twofactor` int NOT NULL DEFAULT '0',
  `googleAuthCode` varchar(59) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `darkmode` int NOT NULL DEFAULT '0',
  `acclogs` int NOT NULL DEFAULT '1',
  `lastreset` int DEFAULT NULL,
  `emailVerify` int NOT NULL DEFAULT '1',
  `permissions` bit(64) NOT NULL DEFAULT b'11111111111',
  `securityKey` int NOT NULL DEFAULT '0',
  `staff` int DEFAULT '0',
  `staffDiscordID` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `formBanned` int DEFAULT '0',
  `connection` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `stafftype` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `apps` (
  `id` int NOT NULL,
  `owner` varchar(65) NOT NULL,
  `name` varchar(40) NOT NULL,
  `secret` varchar(64) NOT NULL,
  `ownerid` varchar(39) NOT NULL,
  `enabled` int NOT NULL,
  `banned` int NOT NULL DEFAULT '0',
  `paused` int NOT NULL DEFAULT '0',
  `hwidcheck` int NOT NULL,
  `vpnblock` int NOT NULL DEFAULT '0',
  `sellerkey` varchar(32) NOT NULL,
  `ver` varchar(5) NOT NULL DEFAULT '1.0',
  `download` varchar(120) DEFAULT NULL,
  `hash` varchar(2000) DEFAULT NULL,
  `webhook` varchar(130) DEFAULT NULL,
  `auditLogWebhook` varchar(130) DEFAULT NULL,
  `resellerstore` varchar(69) DEFAULT NULL,
  `appdisabled` varchar(100) NOT NULL DEFAULT 'This application is disabled',
  `usernametaken` varchar(100) NOT NULL DEFAULT 'Username already taken, choose a different one',
  `keynotfound` varchar(100) NOT NULL DEFAULT 'Invalid license key',
  `keyused` varchar(100) NOT NULL DEFAULT 'License key has already been used',
  `nosublevel` varchar(100) DEFAULT 'There is no subscription created for your key level. Contact application developer.',
  `usernamenotfound` varchar(100) NOT NULL DEFAULT 'Invalid username',
  `passmismatch` varchar(100) NOT NULL DEFAULT 'Password does not match.',
  `hwidmismatch` varchar(100) NOT NULL DEFAULT 'HWID doesn''t match. Ask for a HWID reset',
  `noactivesubs` varchar(100) NOT NULL DEFAULT 'No active subscription(s) found',
  `hwidblacked` varchar(100) NOT NULL DEFAULT 'You''ve been blacklisted from our application',
  `pausedsub` varchar(100) NOT NULL DEFAULT 'Your subscription is paused and can''t be used right now',
  `vpnblocked` varchar(100) NOT NULL DEFAULT 'VPNs are blocked on this application',
  `keybanned` varchar(100) NOT NULL DEFAULT 'Your license is banned',
  `userbanned` varchar(100) NOT NULL DEFAULT 'The user is banned',
  `sessionunauthed` varchar(100) NOT NULL DEFAULT 'Session is not validated',
  `hashcheckfail` varchar(100) NOT NULL DEFAULT 'This program hash does not match, make sure you''re using latest version',
  `loggedInMsg` varchar(99) NOT NULL DEFAULT 'Logged in!',
  `pausedApp` varchar(99) NOT NULL DEFAULT 'Application is currently paused, please wait for the developer to say otherwise.',
  `unTooShort` varchar(99) NOT NULL DEFAULT 'Username too short, try longer one.',
  `pwLeaked` varchar(99) NOT NULL DEFAULT 'This password has been leaked in a data breach (not from us), please use a different one.',
  `chatHitDelay` varchar(99) NOT NULL DEFAULT 'Chat slower, you''ve hit the delay limit',
  `sellixsecret` varchar(32) DEFAULT NULL,
  `sellixdayproduct` varchar(13) DEFAULT NULL,
  `sellixweekproduct` varchar(13) DEFAULT NULL,
  `sellixmonthproduct` varchar(13) DEFAULT NULL,
  `sellixlifetimeproduct` varchar(13) DEFAULT NULL,
  `shoppysecret` varchar(16) DEFAULT NULL,
  `shoppydayproduct` varchar(7) DEFAULT NULL,
  `shoppyweekproduct` varchar(7) DEFAULT NULL,
  `shoppymonthproduct` varchar(7) DEFAULT NULL,
  `shoppylifetimeproduct` varchar(7) DEFAULT NULL,
  `sellappsecret` varchar(64) DEFAULT NULL,
  `sellappdayproduct` varchar(199) DEFAULT NULL,
  `sellappweekproduct` varchar(199) DEFAULT NULL,
  `sellappmonthproduct` varchar(199) DEFAULT NULL,
  `sellapplifetimeproduct` varchar(199) DEFAULT NULL,
  `cooldown` int NOT NULL DEFAULT '604800',
  `panelstatus` int NOT NULL DEFAULT '1',
  `session` int NOT NULL DEFAULT '21600',
  `hashcheck` int NOT NULL DEFAULT '0',
  `webdownload` varchar(120) DEFAULT NULL,
  `customDomain` varchar(253) DEFAULT NULL,
  `format` varchar(99) NOT NULL DEFAULT '******-******-******-******-******-******',
  `amount` int DEFAULT NULL,
  `lvl` int DEFAULT NULL,
  `note` varchar(69) DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `unit` int DEFAULT NULL,
  `killOtherSessions` int NOT NULL DEFAULT '0',
  `cooldownUnit` int NOT NULL DEFAULT '86400',
  `sessionUnit` int NOT NULL DEFAULT '3600',
  `minUsernameLength` int NOT NULL DEFAULT '1',
  `blockLeakedPasswords` int NOT NULL DEFAULT '0',
  `forceEncryption` int NOT NULL DEFAULT '0',
  `customDomainAPI` varchar(253) DEFAULT NULL,
  `customerPanelIcon` varchar(200) NOT NULL DEFAULT 'https://cdn.keyauth.cc/front/assets/img/favicon.png',
  `forceHwid` int DEFAULT '1',
  `minHwid` int DEFAULT '20',
  `sellerLogs` int DEFAULT '0',
  `sellerApiWhitelist` varchar(49) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `auditLog` (
  `id` int NOT NULL,
  `user` varchar(65) NOT NULL,
  `event` varchar(999) NOT NULL,
  `time` int NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `bans` (
  `id` int NOT NULL,
  `hwid` varchar(500) DEFAULT NULL,
  `ip` varchar(49) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `app` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `buttons` (
  `id` int NOT NULL,
  `text` varchar(99) NOT NULL,
  `value` varchar(99) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `chatmsgs` (
  `id` int NOT NULL,
  `author` varchar(70) NOT NULL,
  `message` varchar(2000) NOT NULL,
  `timestamp` int NOT NULL,
  `channel` varchar(50) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `chatmutes` (
  `id` int NOT NULL,
  `user` varchar(70) NOT NULL,
  `time` int NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `chats` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `delay` int NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `emailverify` (
  `id` int NOT NULL,
  `secret` varchar(32) NOT NULL,
  `email` varchar(40) NOT NULL,
  `time` int NOT NULL,
  `region` varchar(99) DEFAULT NULL,
  `asNum` varchar(20) DEFAULT NULL,
  `newEmail` varchar(40) DEFAULT NULL,
  `newUsername` varchar(99) DEFAULT NULL,
  `oldUsername` varchar(99) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `files` (
  `pk` int NOT NULL,
  `name` varchar(49) NOT NULL,
  `id` varchar(49) NOT NULL,
  `url` varchar(2048) DEFAULT NULL,
  `size` varchar(49) NOT NULL,
  `uploaddate` varchar(49) NOT NULL,
  `app` varchar(64) NOT NULL,
  `authed` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `keys` (
  `id` int NOT NULL,
  `key` varchar(70) NOT NULL,
  `note` varchar(69) DEFAULT NULL,
  `expires` varchar(49) NOT NULL,
  `status` varchar(49) NOT NULL,
  `level` varchar(12) NOT NULL DEFAULT '',
  `genby` varchar(65) DEFAULT NULL,
  `gendate` varchar(49) NOT NULL,
  `usedon` int DEFAULT NULL,
  `usedby` varchar(70) DEFAULT NULL,
  `app` varchar(64) NOT NULL,
  `banned` varchar(99) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `logs` (
  `id` int NOT NULL,
  `logdate` varchar(49) NOT NULL,
  `logdata` varchar(275) NOT NULL,
  `credential` varchar(70) DEFAULT NULL,
  `pcuser` varchar(32) DEFAULT NULL,
  `logapp` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `orderID` varchar(36) NOT NULL,
  `username` varchar(65) NOT NULL,
  `date` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `resets` (
  `id` int NOT NULL,
  `secret` char(32) NOT NULL,
  `email` varchar(40) NOT NULL,
  `time` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `resetUsers` (
  `id` int NOT NULL,
  `secret` varchar(32) NOT NULL,
  `email` varchar(40) NOT NULL,
  `username` varchar(70) NOT NULL,
  `app` varchar(64) NOT NULL,
  `time` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `securityKeys` (
  `id` int NOT NULL,
  `username` varchar(65) DEFAULT NULL,
  `name` varchar(99) DEFAULT NULL,
  `credentialId` varchar(999) DEFAULT NULL,
  `credentialPublicKey` varchar(999) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `sellerLogs` (
  `id` int NOT NULL,
  `ip` varchar(45) NOT NULL,
  `path` varchar(999) NOT NULL,
  `date` int NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `sessions` (
  `id` varchar(10) NOT NULL,
  `credential` varchar(70) DEFAULT NULL,
  `app` varchar(64) NOT NULL,
  `expiry` int NOT NULL,
  `created_at` int DEFAULT NULL,
  `enckey` varchar(100) DEFAULT NULL,
  `validated` int NOT NULL DEFAULT '0',
  `ip` varchar(45) DEFAULT NULL,
  `pk` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `subs` (
  `id` int NOT NULL,
  `user` varchar(70) NOT NULL,
  `subscription` varchar(49) NOT NULL,
  `expiry` varchar(49) NOT NULL,
  `app` varchar(64) NOT NULL,
  `key` varchar(70) DEFAULT NULL,
  `paused` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `subscriptions` (
  `id` int NOT NULL,
  `name` varchar(49) NOT NULL,
  `level` varchar(12) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `support` (
  `id` int NOT NULL,
  `username` varchar(65) NOT NULL,
  `time` int NOT NULL,
  `message` varchar(200) DEFAULT NULL,
  `staff` int NOT NULL DEFAULT '0',
  `ownerid` varchar(65) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(70) NOT NULL,
  `email` varchar(40) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `hwid` varchar(2000) DEFAULT NULL,
  `app` varchar(64) NOT NULL,
  `owner` varchar(65) DEFAULT NULL,
  `createdate` int DEFAULT NULL,
  `lastlogin` int DEFAULT NULL,
  `banned` varchar(99) DEFAULT NULL,
  `ip` varchar(49) DEFAULT NULL,
  `cooldown` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `uservars` (
  `id` int NOT NULL,
  `name` varchar(99) NOT NULL,
  `data` varchar(500) NOT NULL,
  `user` varchar(70) NOT NULL,
  `app` varchar(64) NOT NULL,
  `readOnly` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `vars` (
  `id` int NOT NULL,
  `varid` varchar(49) NOT NULL,
  `msg` varchar(1000) NOT NULL,
  `app` varchar(64) NOT NULL,
  `authed` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `webhooks` (
  `id` int NOT NULL,
  `webid` varchar(10) NOT NULL,
  `baselink` varchar(200) NOT NULL,
  `useragent` varchar(49) NOT NULL DEFAULT 'KeyAuth',
  `app` varchar(64) NOT NULL,
  `authed` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `whitelist` (
  `id` int NOT NULL,
  `ip` varchar(49) NOT NULL,
  `app` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


ALTER TABLE `acclogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username` (`username`);

ALTER TABLE `accounts`
  ADD PRIMARY KEY (`username`),
  ADD KEY `idx_email_sha1` (`email`,`username`),
  ADD KEY `idx_accounts_owner` (`owner`);

ALTER TABLE `apps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sellerkey_idx` (`sellerkey`),
  ADD KEY `name_owner_idx` (`name`,`owner`),
  ADD KEY `idx_apps_secret` (`secret`),
  ADD KEY `idx_apps_owner_ownerid` (`owner`,`ownerid`),
  ADD KEY `idx_apps_customdomain` (`customDomain`);

ALTER TABLE `auditLog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_app` (`app`);

ALTER TABLE `bans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bans_hwid_ip_app` (`hwid`,`ip`,`app`),
  ADD KEY `idx_bans_app_hwid_ip` (`app`,`hwid`,`ip`);

ALTER TABLE `buttons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `value` (`value`,`app`),
  ADD KEY `app_index` (`app`);

ALTER TABLE `chatmsgs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_channel_app` (`channel`,`app`),
  ADD KEY `idx_app` (`app`);

ALTER TABLE `chatmutes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_index` (`app`);

ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `one name per app` (`name`,`app`),
  ADD KEY `app_index` (`app`);

ALTER TABLE `emailverify`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `files`
  ADD PRIMARY KEY (`pk`),
  ADD KEY `idx_app_id` (`app`,`id`);

ALTER TABLE `keys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_index` (`app`),
  ADD KEY `idx_app_key` (`app`,`key`);

ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logs_logapp_logdata_credential_pcuser` (`logapp`,`logdata`,`credential`,`pcuser`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `resets`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `resetUsers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `securityKeys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username_index` (`username`);

ALTER TABLE `sellerLogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_index` (`app`);

ALTER TABLE `sessions`
  ADD PRIMARY KEY (`pk`),
  ADD KEY `session index` (`id`,`app`),
  ADD KEY `app_validated_expiry_index` (`app`,`validated`,`expiry`);

ALTER TABLE `subs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_app_paused_idx` (`user`,`app`,`paused`),
  ADD KEY `idx_subs_user_app_expiry` (`user`,`app`,`expiry`),
  ADD KEY `app_subscription_expiry_idx` (`app`,`subscription`,`expiry`);

ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_level_idx` (`app`,`level`);

ALTER TABLE `support`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ownerid_time` (`ownerid`,`time`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user index` (`username`,`app`),
  ADD KEY `app_index` (`app`);

ALTER TABLE `uservars`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user vars` (`name`,`user`,`app`),
  ADD KEY `idx_uservars_app` (`app`);

ALTER TABLE `vars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vars_varid_app` (`varid`,`app`,`msg`(50),`authed`),
  ADD KEY `index_app` (`app`),
  ADD KEY `idx_vars_app_varid_msg` (`app`,`varid`,`msg`(50));

ALTER TABLE `webhooks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `webid_app_idx` (`webid`,`app`);

ALTER TABLE `whitelist`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `acclogs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `apps`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `auditLog`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `bans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `buttons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `chatmsgs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `chatmutes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `chats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `emailverify`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `files`
  MODIFY `pk` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `keys`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `resetUsers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `securityKeys`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `sellerLogs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `sessions`
  MODIFY `pk` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `subs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `subscriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `support`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `uservars`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `vars`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `webhooks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `whitelist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
