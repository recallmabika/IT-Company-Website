<?php
declare(strict_types=1);
require("./mailing/mailfunction.php");

// 1. Improved Style: Use null coalescing to prevent "Undefined Index" notices
$name       = htmlspecialchars($_POST["name"] ?? 'Anonymous');
$phone      = htmlspecialchars($_POST['phone'] ?? 'N/A');
$email      = filter_var($_POST["email"] ?? '', FILTER_VALIDATE_EMAIL);
$applyFor   = htmlspecialchars($_POST["status"] ?? 'General');
$experience = (int)($_POST["experience"] ?? 0);
$details    = htmlspecialchars($_POST["details"] ?? '');

// 2. New Feature: File Validation
$file = $_FILES["fileToUpload"] ?? null;
$uploadOk = true;
$errorMessage = "";

if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    $uploadOk = false;
    $errorMessage = "File upload error or no file selected.";
} else {
    $fileExt = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $fileSize = $file["size"];
    
    // Constraint: Only allow PDFs under 5MB
    if ($fileExt !== "pdf") {
        $uploadOk = false;
        $errorMessage = "Only PDF files are allowed.";
    } elseif ($fileSize > 5 * 1024 * 1024) {
        $uploadOk = false;
        $errorMessage = "File is too large (Max 5MB).";
    }
}

// 3. Secure File Naming
// Avoid using just $name to prevent directory traversal or overwriting
$safeName = preg_replace("/[^a-zA-Z0-9]/", "_", $name);
$targetDirectory = "uploads/"; // Ensure this folder exists and is protected
$finalFileName = $targetDirectory . $safeName . "_" . time() . ".pdf";

if ($uploadOk && $email) {
    if (move_uploaded_file($file["tmp_name"], $finalFileName)) {
        
        // 4. Improved HTML Body Style
        $body = "
            <h2>New Job Application</h2>
            <hr>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Phone:</strong> {$phone}</p>
            <p><strong>Position:</strong> {$applyFor}</p>
            <p><strong>Experience:</strong> {$experience} Years</p>
            <p><strong>Additional Details:</strong><br>{$details}</p>
        ";

        $status = mailfunction("hr@company.com", "Hiring Department", $body, $finalFileName);

        if ($status) {
            echo "<h1>Success!</h1><p>We have received your application.</p>";
        } else {
            echo "<h1>Error</h1><p>Mail could not be sent. Please try again later.</p>";
        }
    } else {
        echo "<h1>Upload Error</h1><p>Failed to move uploaded file.</p>";
    }
} else {
    echo "<h1>Validation Failed</h1><p>" . ($errorMessage ?: "Please provide a valid email.") . "</p>";
}
