# KeyAuth-Source-Code
KeyAuth is an open source authentication system with cloud-hosted subscriptions available aswell, view at https://keyauth.win
<br>
*You're not allowed to sell KeyAuth. source made avaliable only for you to use*

Credits to https://github.com/fingu/c_auth for his client examples he gave me permission to use. Much appreciated <3
<br>
You can use the changes.sql file to modify your current database if you had KeyAuth source setup before an update.
## Donate ##
|BTC|ETH|LTC|
|---|---|---|
|1PnYzj5AsP14MvLAeBpnpGQS4C3Md1sWin|Lc3KhDNNr65HzYGzQajfYCprJRLaGkKiHz|0xC4ED0251eC83Ab95cd634D0aaAE79942720A1F4d|

_Donations of all sizes welcome - Show proof of donation for exclusive role in Discord https://keyauth.win/discord

You may purchase a subscription for cheaper than the cost of hosting this yourself at https://keyauth.win/
You may also use our tester subscription which is completley free with less features.

No support is given towards setup of this. Feel free to create issue though if code is broken https://github.com/KeyAuth/KeyAuth-Source-Code/issues/new

[![CodeFactor](https://img.shields.io/codefactor/grade/github/KeyAuth/KeyAuth-Source-Code?label=CodeFactor&cacheSeconds=3600)](https://www.codefactor.io/repository/github/KeyAuth/KeyAuth-Source-Code)
[![Release](https://img.shields.io/github/v/release/KeyAuth/KeyAuth-Source-Code?label=Release&color=brightgreen&cacheSeconds=3600)](https://github.com/KeyAuth/KeyAuth-Source-Code/releases/latest)
[![Discord](https://img.shields.io/discord/824397012685291520?label=Discord&cacheSeconds=3600)](https://discord.gg/UNk3MphscB)
[![Twitter](https://img.shields.io/twitter/follow/KeyAuth?cacheSeconds=3600)](https://twitter.com/KeyAuth)

[![Screenshot](https://i.imgur.com/PceOYKw.png)](https://keyauth.win)

## Setup ##

- Download The Repository
- Upload all files to your PHP host of choice
- Right click the db_structure.sql file and click edit. Then copy the contents and paste into SQL import tab on phpmyadmin
- Change https://github.com/KeyAuth/KeyAuth-Source-Code/blob/main/includes/connection.php#L16 to your database credentials
- (Optional) Change https://github.com/KeyAuth/KeyAuth-Source-Code/blob/main/includes/connection.php#L22 to your Discord webhook link if you want to log all logins to a Discord webhook

Some pages such as the API endpoint that upgrades users after they purchase a subscription have been omitted to prevent violation of the license (No Commercial Access Allowed)

## Updates ##

https://headwayapp.co/keyauth-changelog/
