# KeyAuth-Source-Code
KeyAuth is an open source authentication system with cloud-hosted subscriptions available aswell, view at https://keyauth.com

[![CodeFactor](https://img.shields.io/codefactor/grade/github/KeyAuth/KeyAuth-Source-Code?label=CodeFactor&cacheSeconds=3600)](https://www.codefactor.io/repository/github/KeyAuth/KeyAuth-Source-Code)
[![Release](https://img.shields.io/github/v/release/KeyAuth/KeyAuth-Source-Code?label=Release&color=brightgreen&cacheSeconds=3600)](https://github.com/KeyAuth/KeyAuth-Source-Code/releases/latest)
[![Discord](https://img.shields.io/discord/824397012685291520?label=Discord&cacheSeconds=3600)](https://discord.gg/8CqcCTbEEh)
[![Twitter](https://img.shields.io/twitter/follow/KeyAuth?cacheSeconds=3600)](https://twitter.com/KeyAuth)

[![Screenshot](https://i.imgur.com/PceOYKw.png)](https://keyauth.com)

## Setup ##

- Download The Repository
- Upload all files to your PHP host of choice
- Right click the db_structure.sql file and click edit. Then copy the contents and paste into SQL import tab on phpmyadmin
- Change https://github.com/KeyAuth/KeyAuth-Source-Code/blob/main/includes/connection.php#L4 to your database credentials
- (Optional) Change https://github.com/KeyAuth/KeyAuth-Source-Code/blob/main/login/index.php#L358 to your Discord webhook link if you want to log all logins to a Discord webhook
- (Optional) Change https://github.com/KeyAuth/KeyAuth-Source-Code/blob/main/dashboard/app/licenses/index.php#L747 to your Discord webhook link if you want to log all logins to a Discord webhook

Some pages such as the API endpoint that upgrades users after they purchase a subscription have been omitted to prevent violation of the license (No Commercial Access Allowed)

You may purchase a subscription for cheaper than the cost of hosting this yourself at https://keyauth.com/
You may also use our tester subscription which is completley free with less features.

No support is given towards setup of self-hosted KeyAuth. You may recieve support if you have an active cloud-hosted subscription of KeyAuth though, https://keyauth.com/discord 
