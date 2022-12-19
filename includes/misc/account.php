<?php

namespace misc\account;

use misc\etc;

function addAccount($username, $role, $email, $password, $keyLevels, $owner, $name, $permissions)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$username = etc\sanitize($username);
	$role = etc\sanitize($role);
	$email = etc\sanitize($email);
	$password = etc\sanitize($password);
	$keyLevels = etc\sanitize($keyLevels) ?? "N/A";
	$owner = etc\sanitize($owner);
	$name = etc\sanitize($name);
	$permissions = etc\sanitize($permissions);

	if (!in_array($role, array(
		"Manager",
		"Reseller"
	))) {
		return 'invalid_role';
	}

	if (is_null($email)) {
		return 'invalid_email';
	}

	$pass_encrypted = password_hash($password, PASSWORD_BCRYPT);

	$user_check = mysqli_query($link, "SELECT `username` FROM `accounts` WHERE `username` = '$username'") or die(mysqli_error($link));
	$do_user_check = mysqli_num_rows($user_check);

	if ($do_user_check > 0) {
		return 'username_taken';
	}
	$email_check = mysqli_query($link, "SELECT `username` FROM `accounts` WHERE `email` = SHA1('$email')") or die(mysqli_error($link));
	$do_email_check = mysqli_num_rows($email_check);
	if ($do_email_check > 0) {
		return 'email_taken';
	}
	
	if($permissions <= 0 || !is_numeric($permissions)) { // Manager users must have access to at least one page
		return 'invalid_perms';
	}
	$permissions = decbin($permissions);
	
	mysqli_query($link, "INSERT INTO `accounts` (`username`, `email`, `password`, `role`, `app`, `owner`, `balance`, `keylevels`, `permissions`) VALUES ('$username',SHA1('$email'),'$pass_encrypted','$role','$name','$owner', '0|0|0|0|0|0', '$keyLevels', b'$permissions')") or die(mysqli_error($link));

	if (mysqli_affected_rows($link) > 0) {
		return 'success';
	}
	else {
		return 'failure';
	}
}

?>