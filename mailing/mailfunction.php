<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require('./vendor/autoload.php');
require 'mailingvariables.php';


function mailfunction($to, $subject, $message, $attachments = [], $replyTo = null) {
    
    $mail = new PHPMailer(true); // Passing 'true' enables exceptions

    try {
        // --- Server Settings ---
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Uncomment for deep debugging
        $mail->isSMTP();
        $mail->Host       = $GLOBALS['mail_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $GLOBALS['mail_sender_email'];
        $mail->Password   = $GLOBALS['mail_sender_password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $GLOBALS['mail_port'];

        // --- Recipients ---
        $mail->setFrom($GLOBALS['mail_sender_email'], $GLOBALS['mail_sender_name']);
        $mail->addAddress($to);
        
        // Feature: Dynamic Reply-To (Useful for contact forms)
        if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($replyTo);
        }

        // --- Attachments ---
        // Feature: Support for multiple attachments via Array
        if (!empty($attachments)) {
            $fileArray = is_array($attachments) ? $attachments : [$attachments];
            foreach ($fileArray as $filePath) {
                if (file_exists($filePath)) {
                    $mail->addAttachment($filePath);
                }
            }
        }

        // --- Content ---
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        
        // Feature: Auto-generated Plain Text version for better deliverability
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<p>'], "\n", $message));

        $mail->send();
        return ['status' => true, 'error' => null];

    } catch (Exception $e) {
        // Log the error for the developer (useful for production)
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return ['status' => false, 'error' => $mail->ErrorInfo];
    }
}
