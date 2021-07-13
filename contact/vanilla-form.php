<?php

define('FROM_EMAIL', '');
define('TO_EMAIL', 'testbergen1@gmail.com');

function sendMessage() {
   
    $json = array();
    $token = "9320087105434084715";

    
    $contact_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $contact_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $contact_tel = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_STRING);
    $contact_department = filter_input(INPUT_POST, 'department', FILTER_SANITIZE_STRING);
    $contact_subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
   
    switch ($contact_subject) {
        case "General":
            $contact_subject = "General";
            break;
        case "Hi":
            $contact_subject = "Say Hi";
            break;
        case "Other":
            $contact_subject = "Other";
            break;
        default:
            $contact_subject = "Not selected";
            break;
    }

    $contact_message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    $contact_secret = filter_input(INPUT_POST, 'contact_secret', FILTER_SANITIZE_STRING);
    $contact_secret = strrev($contact_secret);

    if ($contact_secret !== $token) {
        $json['result'] = 'NO_SPAM';
        header('Access-Control-Allow-Origin: *');
        echo json_encode($json);
        die();
    }

    // Adding e-mail headers
    $headers = "";
    if (FROM_EMAIL !== '') {
        $headers .= 'From: '.FROM_EMAIL."\r\n";
    }
    $headers .= 'Reply-To: '.$contact_email."\r\n";
    $headers .= 'Content-Type: text/plain; charset=UTF-8'."\r\n";

    /*
     * Formatting message.
     * It can be customizable in any way you like.
     */
    $title = 'Новое письмо с сайта от: '.$contact_name;
    $message = 'Добрый день,'."\n\n"
        .'Новое сообщение:'."\n\n"
        .'\'s IP address: '.getIp()."\n"
        .'Subject: '.$contact_subject."\n"
        .'Им: '.$contact_name."\n"
        .'E-mail: '.$contact_email."\n"
        .'Телефон: '.$contact_tel."\n"
        .'Тема: '.$contact_department."\n\n"
        .'сообщение:'."\n"
        .$contact_message;


    // Mail it!
    $result = mail(TO_EMAIL, $title, $message, $headers);

    // Notify contact form about result of sending.
    if ($result) {
        $json['result'] = 'OK';
    } else {
        $json['result'] = 'SEND_ERROR';
    }
    header('Access-Control-Allow-Origin: *');
    echo json_encode($json);
    die();
}

/**
 * Function for getting visitor's IP address
 * @return string
 */
function getIp() {
    $ip = '';

    if (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } else if(getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } else if(getenv('HTTP_X_FORWARDED')) {
        $ip = getenv('HTTP_X_FORWARDED');
    } else if(getenv('HTTP_FORWARDED_FOR')) {
        $ip = getenv('HTTP_FORWARDED_FOR');
    } else if(getenv('HTTP_FORWARDED')) {
        $ip = getenv('HTTP_FORWARDED');
    } else if(getenv('REMOTE_ADDR')) {
        $ip = getenv('REMOTE_ADDR');
    } else {
        $ip = 'N/A';
    }

    return $ip;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    sendMessage();
    die();
} else {
    if (function_exists('mail')) {
        die('OK');
    } else {
        die('PHP parser works, but <b>mail()</b> function seems to doesn\'t exist');
    }

}