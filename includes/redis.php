<?php
error_reporting(0); // disable useless warnings, should turn this on if you need to debug a problem

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
// $redis->auth(''); // you should use Redis password unless testing on your local server.