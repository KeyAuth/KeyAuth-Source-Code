<?php

namespace misc\email;

function send($username, $email, $content, $subject) {
        global $awsAccessKey;
        global $awsSecretKey;
        
        if(empty($awsAccessKey)) {
                return;
        }
        
        require_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/vendor/autoload.php';
        
        $m = new \SimpleEmailServiceMessage();
        $m->addTo("{$username} <{$email}>");
        $m->setFrom('KeyAuth <noreply@keyauth.cc>');
        $m->setSubject("{$subject}");
        $m->setMessageFromString($content, $content);
        
        $region_endpoint = \SimpleEmailService::AWS_US_EAST_2;
        $ses = new \SimpleEmailService($awsAccessKey, $awsSecretKey, $region_endpoint);
        $ses->sendEmail($m);
}
