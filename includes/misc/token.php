<?php

namespace misc\token;

use misc\etc;
use misc\cache;
use misc\mysql;

function IsAssignedToken($credential, $type, $secret = null, $return_token = false) {

	$type = strtolower($type);

	if ($type !== "user" && $type !== "license") { return "invalid_type"; }

	$row = cache\fetch('KeyAuthUserTokenCheck:' . $secret ?? $_SESSION["app"] . ':' . $credential, "SELECT `token` FROM `tokens` WHERE `app` = ? AND `type` = ? AND `assigned` = ?", [$secret ?? $_SESSION["app"], $type, $credential], 0);

	if ($row === "not_found") {
		return false;
	}
	else {
		if ($return_token) { return $row["token"]; }
		else { return true; }
	}


}


function ModifyUserToken($credential, $type, $token = null, $username = null,  $secret = null) {

	$generated_token = bin2hex(random_bytes(16));

	switch ($type) {

		case 'User':

			$query = mysql\query("INSERT INTO `tokens` (`app`, `token`, `type`, `assigned`) VALUES (?, ?, ?, ?)", [$secret ?? $_SESSION["app"], $generated_token, "user", $credential]);

			if ($query->affected_rows < 1) {
				return "failed";
			}
			else {
				return "success";
			}

		case 'License':

			$query = mysql\query("INSERT INTO `tokens` (`app`, `token`, `type`, `assigned`) VALUES (?, ?, ?, ?)", [$secret ?? $_SESSION["app"], $generated_token,  "license", $credential]);

			if ($query->affected_rows < 1) {
				return "failed";
			}
			else {
				return "success";
			}

		case 'UpdateToUser':
			$query = mysql\query("UPDATE `tokens` SET `type` = ?, SET `assigned` = ? WHERE `app` = ? AND `token` = ?", ["user", $username, $secret ?? $_SESSION["app"], $token]); 

			if ($query->affected_rows < 1) {
				return "failed";
			}
			else {
				
				cache\purge('KeyAuthUserTokens:' . $secret . ':' . $token);

				return "success";
			}

		case 'UpdateStatus':

			$query = mysql\query("UPDATE `tokens` SET `status` = ? WHERE `token` = ? AND `app` = ?", ["Used", $token, $secret ?? $_SESSION["app"]]);

			if ($query->affected_rows < 1) {
				return "failed";
			}
			else {

				cache\purge('KeyAuthUserTokens:'.$secret ?? $_SESSION["app"] . ':' . $token);

				return "success";
			}

	}

}

function checktoken($function, $token, $secret, $username = NULL, $token_hash = NULL) {

	$token = etc\sanitize($token);

	$row = cache\fetch('KeyAuthUserTokens:'.$secret.':'.$token ,"SELECT * FROM `tokens` WHERE `token` = ? AND `app` = ?", [$token, $secret], 0);

	if ($row === "not_found") {
		return "invalid_token";
	}

	switch ($function) {

		case 'check_data':

			if ($row["banned"]) {
				return "token_blacklisted, " . $row["reason"];
			}

			if (!is_null($row["hash"]) && $row["hash"] !== $token_hash) {
				return "hash_mismatch";
			}

			if (is_null($row["hash"]))
			{
				mysql\query("UPDATE `tokens` SET `hash` = ? WHERE `token` = ? AND `app` = ?", [$token_hash, $token, $secret]);
				cache\purge('KeyAuthUserTokens:'.$secret ?? $_SESSION["app"] . ':' . $token);
			}

			return 'success';

		case 'check_username':

			if (!is_null($username) && $row["assigned"] !== $username) {
				return "token_reuse";
			}
	}


}

function addtokenblacklist($token, $reason, $secret = null) {

	$token = etc\sanitize($token);

	$reason = etc\sanitize($reason);

	$query = mysql\query("UPDATE `tokens` SET `reason` = ?, `status` = ? WHERE `token` = ? AND `app` = ?", [$reason, 1, $token, $secret ?? $_SESSION["app"]]);
	cache\purge('KeyAuthUserTokens:'.$secret ?? $_SESSION["app"] . ':' . $token);


	if ($query->affected_rows < 0) {
		return "failed";
	}

	if ($query->affected_rows > 0) {
		return "success";
	}

}

function removetokenblacklist($token, $secret = null) {

	$token = etc\sanitize($token);

	$query = mysql\query("UPDATE `tokens` SET `reason` = ?, `status` = ? WHERE `token` = ? AND `app` = ?", ["", 0, $token, $secret ?? $_SESSION["app"]]);
	cache\purge('KeyAuthUserTokens:'.$secret ?? $_SESSION["app"] . ':' . $token);

	if ($query->affected_rows < 0) {
		return "failed";
	}

	if ($query->affected_rows > 0) {
		return "success";
	}

}

function resettokenhash($token, $secret = null) {

	$token = etc\sanitize($token);

	$query = mysql\query("UPDATE `tokens` SET `hash` = ? WHERE `token` = ? AND `app` = ?", [NULL, $token, $secret]);
	cache\purge('KeyAuthUserTokens:'.$secret ?? $_SESSION["app"] . ':' . $token);

	if ($query->affected_rows < 0) {
		return "failed";
	}

	if ($query->affected_rows > 1) {
		return "success";
	}

}

?>
