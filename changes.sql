SET @ORIGINAL_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS,
    FOREIGN_KEY_CHECKS = 0;
SET @ORIGINAL_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0;
SET @ORIGINAL_SQL_MODE = @@SQL_MODE,
    SQL_MODE = 'ALLOW_INVALID_DATES,NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE resets
(
   secret    CHAR(32)
               CHARACTER SET utf8mb4
               COLLATE utf8mb4_general_ci
               NOT NULL,
   email     VARCHAR(65)
               CHARACTER SET utf8mb4
               COLLATE utf8mb4_general_ci
               NOT NULL,
   `time`    INT(11) NOT NULL
)
ENGINE INNODB
COLLATE 'utf8mb4_general_ci'
ROW_FORMAT DEFAULT;

ALTER TABLE sessions
   MODIFY COLUMN validated INT(1) NOT NULL DEFAULT 0;

CREATE TABLE uservars
(
   name    VARCHAR(99)
             CHARACTER SET utf8mb4
             COLLATE utf8mb4_general_ci
             NOT NULL,
   data    VARCHAR(500)
             CHARACTER SET utf8mb4
             COLLATE utf8mb4_general_ci
             NOT NULL,
   user    VARCHAR(70)
             CHARACTER SET utf8mb4
             COLLATE utf8mb4_general_ci
             NOT NULL,
   app     VARCHAR(64)
             CHARACTER SET utf8mb4
             COLLATE utf8mb4_general_ci
             NOT NULL,
   UNIQUE KEY `user vars`(name, user, app)
)
ENGINE INNODB
COLLATE 'utf8mb4_general_ci'
ROW_FORMAT DEFAULT;

ALTER TABLE `keys`
   MODIFY COLUMN usedby VARCHAR(70)
                   CHARACTER SET utf8
                   COLLATE utf8_unicode_ci
                   NULL
                   DEFAULT NULL;

ALTER TABLE `keys`
   ADD COLUMN id INT(11) NOT NULL FIRST;

ALTER TABLE `keys`
   MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `keys`
   AUTO_INCREMENT 132;

CREATE TABLE chats
(
   name     VARCHAR(50)
              CHARACTER SET utf8mb4
              COLLATE utf8mb4_general_ci
              NOT NULL,
   delay    INT(10) NOT NULL,
   app      VARCHAR(64)
              CHARACTER SET utf8mb4
              COLLATE utf8mb4_general_ci
              NOT NULL
)
ENGINE INNODB
COLLATE 'utf8mb4_general_ci'
ROW_FORMAT DEFAULT;

ALTER TABLE files
   ADD COLUMN authed INT(1) NOT NULL DEFAULT 1;

ALTER TABLE subs
   ADD COLUMN id INT(255) NOT NULL FIRST;

ALTER TABLE subs
   ADD COLUMN paused INT(1) NOT NULL DEFAULT 0;

ALTER TABLE subs
   ADD PRIMARY KEY(id);

ALTER TABLE subs
   MODIFY COLUMN id INT(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE subs
   AUTO_INCREMENT 12;

CREATE TABLE chatmutes
(
   user      VARCHAR(70)
               CHARACTER SET utf8mb4
               COLLATE utf8mb4_general_ci
               NOT NULL,
   `time`    INT(10) NOT NULL,
   app       VARCHAR(64)
               CHARACTER SET utf8mb4
               COLLATE utf8mb4_general_ci
               NOT NULL
)
ENGINE INNODB
COLLATE 'utf8mb4_general_ci'
ROW_FORMAT DEFAULT;

ALTER TABLE webhooks
   ADD COLUMN authed INT(1) NOT NULL DEFAULT 1;

CREATE TABLE chatmsgs
(
   id             INT(255) NOT NULL AUTO_INCREMENT,
   author         VARCHAR(70)
                    CHARACTER SET utf8mb4
                    COLLATE utf8mb4_general_ci
                    NOT NULL,
   message        VARCHAR(2000)
                    CHARACTER SET utf8mb4
                    COLLATE utf8mb4_general_ci
                    NOT NULL,
   `timestamp`    INT(10) NOT NULL,
   channel        VARCHAR(50)
                    CHARACTER SET utf8mb4
                    COLLATE utf8mb4_general_ci
                    NOT NULL,
   app            VARCHAR(64)
                    CHARACTER SET utf8mb4
                    COLLATE utf8mb4_general_ci
                    NOT NULL,
   PRIMARY KEY(id)
)
ENGINE INNODB
AUTO_INCREMENT 41
COLLATE 'utf8mb4_general_ci'
ROW_FORMAT DEFAULT;

ALTER TABLE users
   MODIFY COLUMN password VARCHAR(70)
                   CHARACTER SET latin1
                   COLLATE latin1_swedish_ci
                   NULL
                   DEFAULT NULL;

ALTER TABLE users
   MODIFY COLUMN hwid VARCHAR(70)
                   CHARACTER SET latin1
                   COLLATE latin1_swedish_ci
                   NULL
                   DEFAULT NULL;

ALTER TABLE users
   ADD COLUMN createdate INT(10) NULL DEFAULT NULL AFTER owner;

ALTER TABLE users
   ADD COLUMN lastlogin INT(10) NULL DEFAULT NULL AFTER createdate;

ALTER TABLE apps
   CHANGE COLUMN lifetimeproduct sellixlifetimeproduct VARCHAR(13)
                                    CHARACTER SET utf8
                                    COLLATE utf8_unicode_ci
                                    NULL
                                    DEFAULT NULL;

ALTER TABLE apps
   CHANGE COLUMN dayproduct sellixdayproduct VARCHAR(13)
                               CHARACTER SET utf8
                               COLLATE utf8_unicode_ci
                               NULL
                               DEFAULT NULL;

ALTER TABLE apps
   CHANGE COLUMN weekproduct sellixweekproduct VARCHAR(13)
                                CHARACTER SET utf8
                                COLLATE utf8_unicode_ci
                                NULL
                                DEFAULT NULL;

ALTER TABLE apps
   CHANGE COLUMN monthproduct sellixmonthproduct VARCHAR(13)
                                 CHARACTER SET utf8
                                 COLLATE utf8_unicode_ci
                                 NULL
                                 DEFAULT NULL;

ALTER TABLE apps
   CHANGE COLUMN keypaused pausedsub VARCHAR(100)
                              CHARACTER SET utf8
                              COLLATE utf8_unicode_ci
                              NOT NULL
                              DEFAULT 'Your Key is paused and cannot be used at the moment.';

ALTER TABLE apps
   MODIFY COLUMN ver VARCHAR(5)
                   CHARACTER SET utf8
                   COLLATE utf8_unicode_ci
                   NOT NULL
                   DEFAULT '1.0';

ALTER TABLE apps
   MODIFY COLUMN panelstatus INT(1) NOT NULL DEFAULT 1;

ALTER TABLE apps
   ADD COLUMN hash VARCHAR(32)
                CHARACTER SET utf8
                COLLATE utf8_unicode_ci
                NULL
                DEFAULT NULL
   AFTER download;

ALTER TABLE apps
   ADD COLUMN vpnblocked VARCHAR(100)
                CHARACTER SET utf8
                COLLATE utf8_unicode_ci
                NOT NULL
                DEFAULT 'VPNs are disallowed on this application'
   AFTER pausedsub;

ALTER TABLE apps
   ADD COLUMN keybanned VARCHAR(100)
                CHARACTER SET utf8
                COLLATE utf8_unicode_ci
                NOT NULL
                DEFAULT 'Your license is banned'
   AFTER vpnblocked;

ALTER TABLE apps
   ADD COLUMN userbanned VARCHAR(100)
                CHARACTER SET utf8
                COLLATE utf8_unicode_ci
                NOT NULL
                DEFAULT 'The user is banned'
   AFTER keybanned;

ALTER TABLE apps
   ADD COLUMN sessionunauthed VARCHAR(100)
                CHARACTER SET utf8
                COLLATE utf8_unicode_ci
                NOT NULL
                DEFAULT 'Session is not validated'
   AFTER userbanned;

ALTER TABLE apps
   ADD COLUMN hashcheckfail VARCHAR(100)
                CHARACTER SET utf8
                COLLATE utf8_unicode_ci
                NOT NULL
                DEFAULT 'This program hash does not match, make sure you''re using latest version'
   AFTER sessionunauthed;

ALTER TABLE apps
   ADD COLUMN shoppysecret VARCHAR(16)
                CHARACTER SET utf8
                COLLATE utf8_unicode_ci
                DEFAULT NULL
   AFTER sellixlifetimeproduct;

ALTER TABLE apps
   ADD COLUMN shoppydayproduct VARCHAR(7)
                CHARACTER SET utf8
                COLLATE utf8_unicode_ci
                DEFAULT NULL
   AFTER shoppysecret;

ALTER TABLE apps
   ADD COLUMN shoppyweekproduct VARCHAR(7)
                CHARACTER SET utf8
                COLLATE utf8_unicode_ci
                DEFAULT NULL
   AFTER shoppydayproduct;

ALTER TABLE apps
   ADD COLUMN shoppymonthproduct VARCHAR(7)
                CHARACTER SET utf8
                COLLATE utf8_unicode_ci
                DEFAULT NULL
   AFTER shoppyweekproduct;

ALTER TABLE apps
   ADD COLUMN shoppylifetimeproduct VARCHAR(7)
                CHARACTER SET utf8
                COLLATE utf8_unicode_ci
                DEFAULT NULL
   AFTER shoppymonthproduct;

ALTER TABLE apps
   ADD COLUMN cooldown INT(10) NOT NULL DEFAULT 604800
   AFTER shoppylifetimeproduct;

ALTER TABLE apps
   ADD COLUMN session INT(10) NOT NULL DEFAULT 21600 AFTER panelstatus;

ALTER TABLE apps
   ADD COLUMN hashcheck INT(1) NOT NULL DEFAULT 0;

ALTER TABLE accounts
   ADD COLUMN lastreset INT(10) NULL DEFAULT NULL;

ALTER TABLE vars
   ADD COLUMN authed INT(1) NOT NULL DEFAULT 1;

SET FOREIGN_KEY_CHECKS = @ORIGINAL_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS = @ORIGINAL_UNIQUE_CHECKS;
SET SQL_MODE = @ORIGINAL_SQL_MODE;
