<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require('./vendor/autoload.php');
require 'mailingvariables.php';

/**
 * Enterprise Mail Utility
 * @param array $options Configuration for the specific email
 * @return array Response status and metadata
 */
function mailfunction($to, $subject, $message, $attachments = [], $replyTo = null, $cc = [], $bcc = []) {
    
    $mail = new PHPMailer(true);

    try {
        // --- Server Settings ---
        $mail->isSMTP();
        $mail->Host       = $GLOBALS['mail_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $GLOBALS['mail_sender_email'];
        $mail->Password   = $GLOBALS['mail_sender_password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $GLOBALS['mail_port'];
        $mail->CharSet    = 'UTF-8'; // Unique Feature: Ensure special characters work

        // --- Recipients ---
        $mail->setFrom($GLOBALS['mail_sender_email'], $GLOBALS['mail_sender_name']);
        $mail->addAddress($to);
        
        // Feature: Support for CC and BCC (Essential for HR copies)
        if (!empty($cc)) foreach((array)$cc as $email) $mail->addCC($email);
        if (!empty($bcc)) foreach((array)$bcc as $email) $mail->addBCC($email);

        if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($replyTo);
        }

        // --- Attachments & Inline Images ---
        if (!empty($attachments)) {
            $fileArray = is_array($attachments) ? $attachments : [$attachments];
            foreach ($fileArray as $key => $filePath) {
                if (file_exists($filePath)) {
                    // Unique Feature: Embed images automatically if they are images
                    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $mail->addEmbeddedImage($filePath, 'logo_img'); // Usage: <img src="cid:logo_img">
                    } else {
                        $mail->addAttachment($filePath);
                    }
                }
            }
        }

        // --- Content ---
        $mail->isHTML(true);
        $mail->Subject = $subject;
        
        // Unique Feature: HTML Email Template Wrapper
        // This wraps your message in a professional container automatically
        $mail->Body = "
            <div style='background-color: #f4f4f4; padding: 20px; font-family: sans-serif;'>
                <div style='max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; border-top: 5px solid #007bff;'>
                    $message
                    <div style='margin-top: 20px; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 10px;'>
                        This email was sent from the " . $GLOBALS['mail_sender_name'] . " automated system.
                    </div>
                </div>
            </div>";
        
        $mail->AltBody = strip_tags(str_replace(['<br>', '<p>'], "\n", $message));

        // Unique Feature: Execution Timer
        $start = microtime(true);
        $mail->send();
        $time_taken = round(microtime(true) - $start, 4);

        return [
            'status' => true, 
            'execution_time' => $time_taken . 's',
            'timestamp' => date('Y-m-d H:i:s')
        ];

    } catch (Exception $e) {
        error_log("[" . date('Y-m-d H:i:s') . "] Mailer Error: {$mail->ErrorInfo}");
        return ['status' => false, 'error' => $mail->ErrorInfo];
    }
}
