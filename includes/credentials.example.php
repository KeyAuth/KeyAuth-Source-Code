<?php
/*
All sensitive credentials stored here.

NOTE: change this file name from credentials.example.php to credentials.php
*/

error_reporting(0);

$databaseHost = "localhost";
$databaseUsername = "root";
$databasePassword = "";
$databaseName = "main";

$mysqlRequireSSL = false; // in case the MySQL server requires SSL


$logwebhook = ""; // discord webhook which receives login logs and keys created

$adminwebhook = ""; // discord webhook which receives admin actions

$redisServers = []; // URLs to purge redis keys from each server (used on live KeyAuth website only)

$redisPass = "";

$keyauthStatsToken = ""; // discord bot token for KeyAuth Stats

$webhookun = "KeyAuth Logs"; // webhook username

$adminwebhookun = "KeyAuth Admin Logs"; // admin webhook's username

$awsAccessKey = ""; // used for AWS SES to send emails

$awsSecretKey = ""; // used for AWS SES to send emails
