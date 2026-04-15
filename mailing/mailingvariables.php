<?php

define('IS_PRODUCTION', false); 

$mail_config = [
    'host'     => 'smtp.gmail.com',
    'port'     => 587,
    'name'     => 'IT Company Recruitment',
    // Using environment variables is safer than hardcoding passwords
    'email'    => getenv('MAIL_USER') ?: "your-email@gmail.com", 
    'password' => getenv('MAIL_PASS') ?: "your-app-password",
    'charset'  => 'UTF-8',
    'timeout'  => 30
];

// Global mapping for your existing scripts to remain compatible
$GLOBALS['mail_host']            = $mail_config['host'];
$GLOBALS['mail_port']            = $mail_config['port'];
$GLOBALS['mail_sender_email']    = $mail_config['email'];
$GLOBALS['mail_sender_password'] = $mail_config['password'];
$GLOBALS['mail_sender_name']     = $mail_config['name'];


?>
