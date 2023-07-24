<?php

namespace misc\cache;

use misc\mysql;
use api\shared;

function fetch($redisKey, $sqlQuery, $args = [], $multiRowed, $expiry = null, $types = null)
{
        global $redis;
        include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/redis.php'; // create connection with redis
        $redisKey = strtolower($redisKey); // redis is case-insensitive
        $data = $redis->get($redisKey);
        if (!$data) {
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

                if(str_contains($redisKey, "keyauthsubs")) { // ensure no users can login for longer than they're supposed to
                        $expiries = array();
                        foreach ($data as $row) {
                                $expiries[] = $row['expiry'];
                        }
                        $ttl = intval(min($expiries) - time());
                        $redis->expire($redisKey, $ttl);
                }

                if(str_contains($redisKey, "keyauthsellercheck")) { // ensure no customers can use SellerAPI for longer than the period they have seller plan
                        $ttl = intval($data["expires"] - time());
                        $redis->expire($redisKey, $ttl);
                }

                if(str_contains($redisKey, "keyauthstate")) { // ensure no users stay logged in for longer than they're supposed to
                        $ttl = intval($data["expiry"] - time());
                        $redis->expire($redisKey, $ttl);
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
        $redisKey = strtolower($redisKey); // redis is case-insensitive, and key must be encoded for HTTP request used in production
        global $redisServers;
        if(!empty($redisServers)) {
                $redisKey = urlencode($redisKey);
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
        if($redisKey == "*" || empty($redisKey)) {
                die("Invalid redis key purge value. Must specify some text.");
        }
        $redisKey = strtolower($redisKey); // redis is case-insensitive, and key must be encoded for HTTP request used in production
        global $redisServers;
        if(!empty($redisServers)) {
                $redisKey = urlencode($redisKey);
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
        $redisKey = strtolower($redisKey); // redis is case-insensitive
        $data = $redis->get($redisKey);
        if (!$data) {
                $redis->set($redisKey, $amount, $expiry);
                return false;
        } else {
                if (intval($data) >= $limit) {
                        $ttl = $redis->ttl($redisKey);
                        if($ttl > $expiry || $ttl < 0) {
                                purge($redisKey);
                                return false;
                        }
                        return true;
                }
                $redis->incr($redisKey, $amount);
                return false;
        }
}

function select($redisKey) {
        global $redis;
        include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/redis.php'; // create connection with Redis
        $redisKey = strtolower($redisKey); // redis is case-insensitive
        return $redis->get($redisKey);
}

function insert($redisKey, $value, $expiry) {
        $redisKey = strtolower($redisKey); // redis is case-insensitive

        global $redisServers;
        if(!empty($redisServers)) {
                // for production setup, to purge redis keys from all servers

                $data = base64_encode($value);
                foreach ($redisServers as $server) {
                        $url = $server . "&type=insert&key={$redisKey}&data={$data}&expiry={$expiry}";

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
        $redis->set($redisKey, $value, $expiry);
}

function update($redisKey, ...$replacements) {
        global $redis;
        include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/redis.php'; // create connection with Redis
        $redisKey = strtolower($redisKey); // redis is case-insensitive
        
        $data = unserialize($redis->get($redisKey));

        if(!$data) {
                return false;
        }

        global $redisServers;
        if(!empty($redisServers)) {
                // for production setup, to update redis keys on all servers
                foreach ($redisServers as $server) {
                        $url = $server . "&type=update&key={$redisKey}";

                        $json_data = json_encode([
                                "data" => $replacements[0]
                        ]);

                        $curl = curl_init($url);
                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                'Content-type: application/json',
                                'host: keyauth.win'
                        ));
                        curl_setopt($curl, CURLOPT_POST, 1);
                            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                        curl_exec($curl);
                        curl_close($curl);
                }
                return true;
        }

        $new = serialize(array_replace($data, ...$replacements));

        $redis->set($redisKey, $new, ['KEEPTTL']);

        return true;
}
