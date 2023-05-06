<?php

namespace misc\cache;

use misc\mysql;

function fetch($redisKey, $sqlQuery, $args = [], $multiRowed, $expiry = null, $types = null)
{
	global $redis;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/redis.php'; // create connection with redis
	$data = $redis->get($redisKey);
	if (!$data) {
		if($_SERVER['HTTP_USER_AGENT'] == "PostmanRuntime/7.31.3" && strpos($redisKey, 'KeyAuthSubs:') !== false){
			echo "hi";
			var_dump($sqlQuery);
			var_dump($args);
		}
		$query = mysql\query($sqlQuery,$args, $types);
		if ($query->num_rows < 1) // check if MySQL found any rows
		{
			$redis->set($redisKey, 'not_found'); // save redis key indicating record not found
			$redis->expire($redisKey, 300); // if the data doesn't exist, only keep Redis key for 5 minutes to mitigate against spam
			return 'not_found';
		}
		if ($multiRowed) { // return multi-rowed response for applications such as chat channels where multiple rows containing messages
			while ($r = mysqli_fetch_assoc($query->result)) {
				$data[] = $r;
			}
		} else {
			$data = mysqli_fetch_array($query->result);
		}

		$redis->set($redisKey, serialize($data)); // save data to redis key so next time it's retrieved much quicker from cache

		if (strpos($redisKey, 'KeyAuthSubs:') !== false) { // ensure no users can login for longer than they're supposed to
			$expiries = array();
			foreach ($data as $row) {
				$expiries[] = $row['expiry'];
			}
			$ttl = intval(min($expiries) - time());
			$redis->expire($redisKey, $ttl);
		}

		if (strpos($redisKey, 'KeyAuthSellerCheck:') !== false) { // ensure no customers can use SellerAPI for longer than the period they have seller plan
			$ttl = intval($data["expires"] - time());
			$redis->expire($redisKey, $ttl);
		}

		if (strpos($redisKey, 'KeyAuthState:') !== false) { // ensure no users stay logged in for longer than they're supposed to
			$ttl = intval($data["expiry"] - time());
			$redis->expire($redisKey, $ttl);
		}

		global $keyauthStatsToken;
		if ($redisKey == "KeyAuthStats" && !empty($keyauthStatsToken)) {
			$channels = [1093264776605470871, 1093264777217855580, 1093264777981218836, 1093264778723610666];
			$values = [$data['numAccs'], $data['numApps'], $data['numKeys'], $data['numOnlineUsers']];

			$i = 0;
			while ($i < 4) {
				$url = "https://discord.com/api/v9/channels/$channels[$i]";

				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

				$headers = array(
					"user-agent: KeyAuth",
					"Authorization: Bot {$keyauthStatsToken}",
					"Content-Type: application/json",
				);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

				$prefix = "";
				switch ($i) {
					case 0:
						$prefix = "Accounts";
						break;
					case 1:
						$prefix = "Applications";
						break;
					case 2:
						$prefix = "Licenses";
						break;
					case 3:
						$prefix = "Active Users";
						break;
				}

				$body = '{"name":"' . $prefix . ': ' . $values[$i] . '"}';

				curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

				$resp = curl_exec($curl);
				curl_close($curl);
				$i++;
			}
		}

		if (!is_null($expiry)) {
			$redis->expire($redisKey, intval($expiry)); // set TTL if specified
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
	global $redisServers;
	if(!empty($redisServers)) {
		// for production setup, to purge redis keys from all servers
		foreach ($redisServers as $server) {
			$url = $server . "&type=purge&key={$redisKey}";

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('host: keyauth.win'));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			
			curl_exec($curl);
			curl_close($curl);
		}
		return;
	}

	global $redis;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/redis.php'; // create connection with Redis
	$redis->del($redisKey);
}

function purgePattern($redisKey) // purge all data starting with, ending with, or both and unknown text in between
{
	if($redisKey == "*" || empty($redisKey))	{
		die("Invalid redis key purge value. Must specify some text.");
	}
	global $redisServers;
	if(!empty($redisServers)) {
		// for production setup, to purge redis keys from all servers
		foreach ($redisServers as $server) {
			$url = $server . "&type=purgePattern&key={$redisKey}";

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('host: keyauth.win'));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			curl_exec($curl);
			curl_close($curl);
		}
		return;
	}
	
	global $redis;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/redis.php'; // create connection with Redis
	$redis->delete($redis->keys($redisKey . '*'));
}

function rateLimit($redisKey, $amount, $expiry, $limit) // rate limiting with Redis, prevent spam/DDoS attack(s)
{
	global $redis;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/redis.php'; // create connection with Redis
	$data = $redis->get($redisKey);
	if (!$data) {
		$redis->set($redisKey, $amount, $expiry);
		return false;
	} else {
		if (intval($data) >= $limit) {
			return true;
		}
		$redis->incr($redisKey, $amount);
		return false;
	}
}
