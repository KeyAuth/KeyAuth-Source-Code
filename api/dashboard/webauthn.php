<?php
/*
 * Copyright (C) 2022 Lukas Buchs
 * license https://github.com/lbuchs/WebAuthn/blob/master/LICENSE MIT
 *
 * Server test script for WebAuthn library. Saves new registrations in session.
 *
 *            JAVASCRIPT            |          SERVER
 * ------------------------------------------------------------
 *
 *               REGISTRATION
 *
 *      window.fetch  ----------------->     getCreateArgs
 *                                                |
 *   navigator.credentials.create   <-------------'
 *           |
 *           '------------------------->     processCreate
 *                                                |
 *         alert ok or fail      <----------------'
 *
 * ------------------------------------------------------------
 *
 *              VALIDATION
 *
 *      window.fetch ------------------>      getGetArgs
 *                                                |
 *   navigator.credentials.get   <----------------'
 *           |
 *           '------------------------->      processGet
 *                                                |
 *         alert ok or fail      <----------------'
 *
 * ------------------------------------------------------------
 */
include '../../includes/misc/autoload.phtml';
require_once '../../vendor/lbuchs/webauthn/src/WebAuthn.php';
try {
    // read get argument and post body
    $fn = filter_input(INPUT_GET, 'fn');
    if(empty($fn)) {
        die("Can't access directly");
    }
    $post = trim(file_get_contents('php://input'));
    $userId = bin2hex(openssl_random_pseudo_bytes(10));
    if ($post) {
        $post = json_decode($post);
    }
    
    session_start();
    // Formats
    $formats = array();
    $formats[] = 'none';

    $rpId = $_SERVER['HTTP_HOST'];

    // cross-platform: true, if type internal is not allowed
    //                 false, if only internal is allowed
    //                 null, if internal and cross-platform is allowed
    $crossPlatformAttachment = null;


    // new Instance of the server library.
    // make sure that $rpId is the domain name.
    $WebAuthn = new lbuchs\WebAuthn\WebAuthn('KeyAuth', $rpId, $formats);

    // ------------------------------------
    // request for create arguments
    // ------------------------------------

    if ($fn === 'getCreateArgs') {
        $createArgs = $WebAuthn->getCreateArgs($userId, $_SESSION['username'], "", 20, 0, "discouraged", $crossPlatformAttachment);

        header('Content-Type: application/json');
        print(json_encode($createArgs));

        // save challange to session. you have to deliver it to processGet later.
        $_SESSION['challenge'] = $WebAuthn->getChallenge();



    // ------------------------------------
    // request for get arguments
    // ------------------------------------

    } else if ($fn === 'getGetArgs') {
        $ids = array();

        // load registrations from session stored there by processCreate.
        
        $query = misc\mysql\query("SELECT * FROM `securityKeys` WHERE `username` = ?", [$_SESSION['pendingUsername']]);
        if ($query->num_rows > 0) {
            while ($row = mysqli_fetch_array($query->result)) {
                $ids[] = base64_decode($row["credentialId"]);
            }
        }

        if (count($ids) === 0) {
            throw new Exception('No security key registrations found for this user!');
        }

        $getArgs = $WebAuthn->getGetArgs($ids, 20, 1, 1, 1, 1, 0);

        header('Content-Type: application/json');
        print(json_encode($getArgs));

        // save challange to session. you have to deliver it to processGet later.
        $_SESSION['challenge'] = $WebAuthn->getChallenge();



    // ------------------------------------
    // process create
    // ------------------------------------

    } else if ($fn === 'processCreate') {
        $clientDataJSON = base64_decode($post->clientDataJSON);
        $attestationObject = base64_decode($post->attestationObject);
        $challenge = $_SESSION['challenge'];

        // processCreate returns data to be stored for future logins.
        $data = $WebAuthn->processCreate($clientDataJSON, $attestationObject, $challenge, 0, false, false);

        unset($_SESSION['challenge']); // disgard challenge array from session file, no longer needed
        
        $name = misc\etc\sanitize($_GET['name']);

        misc\mysql\query("INSERT INTO `securityKeys` (`username`, `name`, `credentialId`, `credentialPublicKey`) VALUES (?, ?, ?, ?)", [$_SESSION['username'], $name, base64_encode($data->credentialId), $data->credentialPublicKey]);
        misc\mysql\query("UPDATE `accounts` SET `securityKey` = 1 WHERE `username` = ?", [$_SESSION['username']]);

        $return = new stdClass();
        $return->success = true;
        $return->msg = 'registration success.';

        header('Content-Type: application/json');
        print(json_encode($return));



    // ------------------------------------
    // proccess get
    // ------------------------------------

    } else if ($fn === 'processGet') {
        $clientDataJSON = base64_decode($post->clientDataJSON);
        $authenticatorData = base64_decode($post->authenticatorData);
        $signature = base64_decode($post->signature);
        $userHandle = base64_decode($post->userHandle);
        $id = base64_decode($post->id);
        $challenge = $_SESSION['challenge'];
        $credentialPublicKey = null;

        // looking up correspondending public key of the credential id
        // you should also validate that only ids of the given user name
        // are taken for the login.
        
        $query = misc\mysql\query("SELECT * FROM `securityKeys` WHERE `username` = ?", [$_SESSION['pendingUsername']]);
        if ($query->num_rows > 0) {
            while ($row = mysqli_fetch_array($query->result)) {
                if(base64_decode($row["credentialId"]) === $id) {
                    $credentialPublicKey = $row["credentialPublicKey"];
                    break;
                }
            }
        }

        if ($credentialPublicKey === null) {
            throw new Exception('This security key wasn\'t found!');
        }

        // process the get request. throws WebAuthnException if it fails
        $WebAuthn->processGet($clientDataJSON, $authenticatorData, $signature, $credentialPublicKey, $challenge, null, 0);
        
        unset($_SESSION['challenge']); // disgard challenge array from session file, no longer needed
        $_SESSION['username'] = $_SESSION['pendingUsername'];
        unset($_SESSION['pendingUsername']);

        $return = new stdClass();
        $return->success = true;

        header('Content-Type: application/json');
        print(json_encode($return));
    }

} catch (Throwable $ex) {
    $return = new stdClass();
    $return->success = false;
    $return->msg = $ex->getMessage();

    header('Content-Type: application/json');
    print(json_encode($return));
}