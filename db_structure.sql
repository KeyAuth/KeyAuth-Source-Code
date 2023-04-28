/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acclogs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(65) DEFAULT NULL,
  `date` varchar(10) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `useragent` varchar(199) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `username` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `ownerid` varchar(65) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `app` varchar(65) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `banned` varchar(99) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locked` int NOT NULL DEFAULT '0',
  `warning` varchar(999) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin` int NOT NULL DEFAULT '0',
  `img` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'https://cdn.keyauth.cc/assets/img/favicon.png',
  `balance` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `keylevels` varchar(49) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N/A',
  `expires` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registrationip` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastip` varchar(49) COLLATE utf8_unicode_ci DEFAULT NULL,
  `region` varchar(99) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asNum` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twofactor` int NOT NULL DEFAULT '0',
  `googleAuthCode` varchar(59) COLLATE utf8_unicode_ci DEFAULT NULL,
  `darkmode` int NOT NULL DEFAULT '0',
  `acclogs` int NOT NULL DEFAULT '1',
  `lastreset` int DEFAULT NULL,
  `emailVerify` int NOT NULL DEFAULT '1',
  `permissions` bit(64) NOT NULL DEFAULT b'11111111111',
  `afCode` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `affiliatedBy` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `securityKey` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`username`),
  KEY `idx_email_sha1` (`email`,`username`),
  KEY `idx_accounts_owner` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afLogs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `afCode` varchar(50) DEFAULT NULL,
  `referrer` varchar(3000) DEFAULT NULL,
  `username` varchar(65) DEFAULT NULL,
  `date` int DEFAULT NULL,
  `action` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apps` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `usernametaken` varchar(100) NOT NULL DEFAULT 'Username Already Exists.',
  `keynotfound` varchar(100) NOT NULL DEFAULT 'Key Not Found.',
  `keyused` varchar(100) NOT NULL DEFAULT 'Key Already Used.',
  `nosublevel` varchar(100) DEFAULT 'There is no subscription created for your key level. Contact application developer.',
  `usernamenotfound` varchar(100) NOT NULL DEFAULT 'Username not found.',
  `passmismatch` varchar(100) NOT NULL DEFAULT 'Password does not match.',
  `hwidmismatch` varchar(100) NOT NULL DEFAULT 'HWID Doesn''t match. Ask for key reset.',
  `noactivesubs` varchar(100) NOT NULL DEFAULT 'No active subscriptions found.',
  `hwidblacked` varchar(100) NOT NULL DEFAULT 'You''ve been blacklisted from our application',
  `pausedsub` varchar(100) NOT NULL DEFAULT 'Your Key is paused and cannot be used at the moment.',
  `vpnblocked` varchar(100) NOT NULL DEFAULT 'VPNs are disallowed on this application',
  `keybanned` varchar(100) NOT NULL DEFAULT 'Your license is banned',
  `userbanned` varchar(100) NOT NULL DEFAULT 'The user is banned',
  `sessionunauthed` varchar(100) NOT NULL DEFAULT 'Session is not validated',
  `hashcheckfail` varchar(100) NOT NULL DEFAULT 'This program hash does not match, make sure you''re using latest version',
  `keyexpired` varchar(100) NOT NULL DEFAULT 'Key has expired.',
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
  `minUsernameLength` int NOT NULL DEFAULT '0',
  `blockLeakedPasswords` int NOT NULL DEFAULT '0',
  `forceEncryption` int NOT NULL DEFAULT '0',
  `customDomainAPI` varchar(253) DEFAULT NULL,
  `customerPanelIcon` varchar(99) NOT NULL DEFAULT 'https://cdn.keyauth.cc/front/assets/img/favicon.png',
  PRIMARY KEY (`id`),
  KEY `sellerkey_idx` (`sellerkey`),
  KEY `name_owner_idx` (`name`,`owner`),
  KEY `idx_apps_secret` (`secret`),
  KEY `idx_apps_owner_ownerid` (`owner`,`ownerid`),
  KEY `idx_apps_customdomain` (`customDomain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditLog` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` varchar(65) NOT NULL,
  `event` varchar(999) NOT NULL,
  `time` int NOT NULL,
  `app` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hwid` varchar(500) DEFAULT NULL,
  `ip` varchar(49) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `app` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_bans_hwid_ip_app` (`hwid`,`ip`,`app`),
  KEY `idx_bans_app_hwid_ip` (`app`,`hwid`,`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buttons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `text` varchar(99) NOT NULL,
  `value` varchar(99) NOT NULL,
  `app` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `value` (`value`,`app`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatmsgs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author` varchar(70) NOT NULL,
  `message` varchar(2000) NOT NULL,
  `timestamp` int NOT NULL,
  `channel` varchar(50) NOT NULL,
  `app` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatmutes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` varchar(70) NOT NULL,
  `time` int NOT NULL,
  `app` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `delay` int NOT NULL,
  `app` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `one name per app` (`name`,`app`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emailverify` (
  `id` int NOT NULL AUTO_INCREMENT,
  `secret` varchar(32) NOT NULL,
  `email` varchar(40) NOT NULL,
  `time` int NOT NULL,
  `region` varchar(99) DEFAULT NULL,
  `asNum` varchar(20) DEFAULT NULL,
  `newEmail` varchar(40) DEFAULT NULL,
  `newUsername` varchar(99) DEFAULT NULL,
  `oldUsername` varchar(99) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `pk` int NOT NULL AUTO_INCREMENT,
  `name` varchar(49) NOT NULL,
  `id` varchar(49) NOT NULL,
  `url` varchar(2048) DEFAULT NULL,
  `size` varchar(49) NOT NULL,
  `uploaddate` varchar(49) NOT NULL,
  `app` varchar(64) NOT NULL,
  `authed` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`pk`),
  KEY `idx_app_id` (`app`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keys` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key` varchar(49) NOT NULL,
  `note` varchar(69) DEFAULT NULL,
  `expires` varchar(49) NOT NULL,
  `status` varchar(49) NOT NULL,
  `level` varchar(12) NOT NULL DEFAULT '',
  `genby` varchar(65) DEFAULT NULL,
  `gendate` varchar(49) NOT NULL,
  `usedon` int DEFAULT NULL,
  `usedby` varchar(70) DEFAULT NULL,
  `app` varchar(64) NOT NULL,
  `banned` varchar(99) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_index` (`app`),
  KEY `idx_app_key` (`app`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `logdate` varchar(49) NOT NULL,
  `logdata` varchar(275) NOT NULL,
  `credential` varchar(70) DEFAULT NULL,
  `pcuser` varchar(32) DEFAULT NULL,
  `logapp` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_logs_logapp_logdata_credential_pcuser` (`logapp`,`logdata`,`credential`,`pcuser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `orderID` varchar(36) NOT NULL,
  `username` varchar(65) NOT NULL,
  `date` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resetUsers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `secret` varchar(32) NOT NULL,
  `email` varchar(40) NOT NULL,
  `username` varchar(70) NOT NULL,
  `app` varchar(64) NOT NULL,
  `time` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `secret` char(32) NOT NULL,
  `email` varchar(40) NOT NULL,
  `time` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `securityKeys` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(65) DEFAULT NULL,
  `name` varchar(99) DEFAULT NULL,
  `credentialId` varchar(999) DEFAULT NULL,
  `credentialPublicKey` varchar(999) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sellerLogs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `path` varchar(999) NOT NULL,
  `date` int NOT NULL,
  `app` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(10) NOT NULL,
  `credential` varchar(255) DEFAULT NULL,
  `app` varchar(64) NOT NULL,
  `expiry` int NOT NULL,
  `created_at` int DEFAULT NULL,
  `enckey` varchar(100) DEFAULT NULL,
  `validated` int NOT NULL DEFAULT '0',
  `ip` varchar(45) DEFAULT NULL,
  `pk` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`pk`),
  KEY `session index` (`id`,`app`),
  KEY `app_validated_expiry_index` (`app`,`validated`,`expiry`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` varchar(49) NOT NULL,
  `subscription` varchar(49) NOT NULL,
  `expiry` varchar(49) NOT NULL,
  `app` varchar(64) NOT NULL,
  `key` varchar(49) DEFAULT NULL,
  `paused` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_app_paused_idx` (`user`,`app`,`paused`),
  KEY `idx_subs_user_app_expiry` (`user`,`app`,`expiry`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(49) NOT NULL,
  `level` varchar(49) NOT NULL,
  `app` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `app_level_idx` (`app`,`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(49) NOT NULL,
  `time` int NOT NULL,
  `message` varchar(200) DEFAULT NULL,
  `staff` int NOT NULL DEFAULT '0',
  `ownerid` varchar(65) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `cooldown` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user index` (`username`,`app`),
  KEY `app_index` (`app`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uservars` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(99) NOT NULL,
  `data` varchar(500) NOT NULL,
  `user` varchar(70) NOT NULL,
  `app` varchar(64) NOT NULL,
  `readOnly` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user vars` (`name`,`user`,`app`),
  KEY `idx_uservars_app` (`app`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vars` (
  `id` int NOT NULL AUTO_INCREMENT,
  `varid` varchar(49) NOT NULL,
  `msg` varchar(20000) NOT NULL,
  `app` varchar(64) NOT NULL,
  `authed` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_vars_varid_app` (`varid`,`app`,`msg`(50),`authed`),
  KEY `index_app` (`app`),
  KEY `idx_vars_app_varid_msg` (`app`,`varid`,`msg`(50))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhooks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `webid` varchar(10) NOT NULL,
  `baselink` varchar(200) NOT NULL,
  `useragent` varchar(49) NOT NULL DEFAULT 'KeyAuth',
  `app` varchar(64) NOT NULL,
  `authed` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `baselink` (`baselink`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whitelist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip` varchar(49) NOT NULL,
  `app` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;