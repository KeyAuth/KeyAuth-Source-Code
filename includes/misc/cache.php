<?php

namespace misc\cache;

function fetch($redisKey, $sqlQuery, $multiRowed, $expiry = null)
{
	global $redis;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/redis.php'; // create connection with redis
	$data = $redis->get($redisKey);
	if (!$data) {
		global $link;
		include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
		$result = mysqli_query($link, $sqlQuery);
		if (mysqli_num_rows($result) < 1) // check if MySQL found any rows
		{
			$redis->set($redisKey, 'not_found'); // save redis key indicating record not found
			$redis->expire($redisKey, 300); // if the data doesn't exist, only keep Redis key for 5 minutes to mitigate against spam
			return 'not_found';
		}
		if ($multiRowed) { // return multi-rowed response for applications such as chat channels where multiple rows containing messages
			while ($r = mysqli_fetch_assoc($result)) {
				$data[] = $r;
			}
		} else {
			$data = mysqli_fetch_array($result);
		}

		$redis->set($redisKey, serialize($data)); // save data to redis key so next time it's retrieved much quicker from cache

		if (strpos($redisKey, 'KeyAuthSubs:') !== false) { // ensure no users can login for longer than they're supposed to
			$expiries = array();
			foreach ($data as $row) {
				$expiries[] = $row['expiry'];
			}
			$ttl = min($expiries) - time();
			$redis->expire($redisKey, $ttl);
		}

		if (strpos($redisKey, 'KeyAuthSellerCheck:') !== false) { // ensure no customers can use SellerAPI for longer than the period they have seller plan
			$ttl = $data["expires"] - time();
			$redis->expire($redisKey, $ttl);
		}

		if (strpos($redisKey, 'KeyAuthState:') !== false) { // ensure no users stay logged in for longer than they're supposed to
			$ttl = $data["expiry"] - time();
			$redis->expire($redisKey, $ttl);
		}

		if (strpos($redisKey, 'KeyAuthStateDuplicates:') !== false) { // ensure no users stay logged in for longer than they're supposed to
			$ttl = $data["expiry"] - time();
			$redis->expire($redisKey, $ttl);
		}

		if (!is_null($expiry)) {
			$redis->expire($redisKey, $expiry); // set TTL if specified
		}
	} else {
		if ($data == "not_found") {
			return 'not_found';
		}
		$data = unserialize($data);
	}
	return $data; // return data from either MySQL or Redis
}
function purge($redisKey) // delete key from Redis cache (typically called when MySQL row(s) updated or deleted)
{
	global $redis;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/redis.php'; // create connection with Redis
	$redis->del($redisKey);
}

function purgePattern($redisKey) // purge all data starting with, ending with, or both and unknown text in between
{
	global $redis;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/redis.php'; // create connection with Redis
	$redis->delete($redis->keys($redisKey . '*'));
}