# KeyAuth-Source-Code
KeyAuth is an open source authentication system with cloud-hosted subscriptions available aswell, view at https://keyauth.com
<br>
*You're not allowed to sell KeyAuth. source made avaliable only for personal use.*

You may purchase a subscription for cheaper than the cost of hosting this yourself at https://keyauth.com/
You may also use our tester subscription which is completley free with less features.

No support is given towards setup of self-hosted KeyAuth. You may recieve support if you have an active cloud-hosted subscription of KeyAuth though, https://keyauth.com/discord

[![CodeFactor](https://img.shields.io/codefactor/grade/github/KeyAuth/KeyAuth-Source-Code?label=CodeFactor&cacheSeconds=3600)](https://www.codefactor.io/repository/github/KeyAuth/KeyAuth-Source-Code)
[![Release](https://img.shields.io/github/v/release/KeyAuth/KeyAuth-Source-Code?label=Release&color=brightgreen&cacheSeconds=3600)](https://github.com/KeyAuth/KeyAuth-Source-Code/releases/latest)
[![Discord](https://img.shields.io/discord/824397012685291520?label=Discord&cacheSeconds=3600)](https://discord.gg/8CqcCTbEEh)
[![Twitter](https://img.shields.io/twitter/follow/KeyAuth?cacheSeconds=3600)](https://twitter.com/KeyAuth)

[![Screenshot](https://i.imgur.com/PceOYKw.png)](https://keyauth.com)

## Setup ##

- Download The Repository
- Upload all files to your PHP host of choice
- Right click the db_structure.sql file and click edit. Then copy the contents and paste into SQL import tab on phpmyadmin
- Change https://github.com/KeyAuth/KeyAuth-Source-Code/blob/main/includes/connection.php#L19 to your database credentials
- (Optional) Change https://github.com/KeyAuth/KeyAuth-Source-Code/blob/main/login/index.php#L191 to your Discord webhook link if you want to log all logins to a Discord webhook
- (Optional) Change https://github.com/KeyAuth/KeyAuth-Source-Code/blob/main/dashboard/app/licenses/index.php#L715 to your Discord webhook link if you want to log all logins to a Discord webhook

Some pages such as the API endpoint that upgrades users after they purchase a subscription have been omitted to prevent violation of the license (No Commercial Access Allowed)

How to get SSL key?

c#: https://www.mediafire.com/file/2089zh0kd5s8urn/GetSSChsarp.exe/file - watch this video: https://a.pomf.cat/ontnkt.mp4
c++: https://www.ssllabs.com/ - watch this video: https://a.pomf.cat/cglexh.mp4

## Updates ##

- (08/12/2021) Suggestion added (Limit resellers to only be able to generate a certain key level) by SoniC#1337

- (08/13/2021) Added discord link api

- (08/13/2021) Fixed 1970 bug on license tab

- (08/13/2021) Fixed 1970 bug on reseller tab
