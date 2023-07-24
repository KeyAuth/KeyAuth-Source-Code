<?php

namespace misc\account;

use misc\etc;
use misc\mysql;

function addAccount($username, $role, $email, $password, $keyLevels, $owner, $name, $permissions)
{
        $username = etc\sanitize($username);
        $role = etc\sanitize($role);
        $email = etc\sanitize($email);
        $password = etc\sanitize($password);
        $keyLevels = etc\sanitize($keyLevels) ?? "N/A";
        $owner = etc\sanitize($owner);
        $name = etc\sanitize($name);
        $permissions = etc\sanitize($permissions);

        if (!in_array($role, array("Manager", "Reseller"))) {
                return 'invalid_role';
        }

        if (is_null($email)) {
                return 'invalid_email';
        }

        $pass_encrypted = password_hash($password, PASSWORD_BCRYPT);

        $user_check = mysql\query("SELECT `username` FROM `accounts` WHERE `username` = ?", [$username]);

        if ($user_check->num_rows > 0) {
                return 'username_taken';
        }
        $email_check = mysql\query("SELECT `username` FROM `accounts` WHERE `email` = SHA1(?)", [$email]);

        if ($email_check->num_rows > 0) {
                return 'email_taken';
        }

        if ($permissions <= 0 || !is_numeric($permissions)) { // Manager users must have access to at least one page
                return 'invalid_perms';
        }
        $permissions = decbin($permissions);

        $query = mysql\query("INSERT INTO `accounts` (`username`, `email`, `password`, `role`, `app`, `owner`, `balance`, `keylevels`, `permissions`) VALUES (?, SHA1(?), ?, ?, ?, ?, '0|0|0|0|0|0', ?, b'$permissions')", [$username, $email, $pass_encrypted, $role, $name, $owner, $keyLevels]);
        if ($query->affected_rows > 0) {
                return 'success';
        } else {
                return 'failure';
        }
}
