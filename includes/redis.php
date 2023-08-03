<?php
include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/credentials.php'; // reference credentials

if (!class_exists('Redis')) {
        die('Redis isn\'t installed. You must install Redis server and Redis PHP extension.<br><br>Or if your configuration can\'t install Redis, remove code containing Redis from /includes/misc/cache.php');
}

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

global $redisPass;

if(!empty($redisPass)) {
        $redis->auth($redisPass);
}
