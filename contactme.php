<?php   
    // 1. Load dependencies
    require("./mailing/mailfunction.php");

    // 2. Security: Sanitize all inputs to prevent XSS and header injection
    // We use null coalescing (??) to prevent "Undefined Index" errors
    $name       = htmlspecialchars(strip_tags($_POST["name"] ?? ''));
    $phone      = htmlspecialchars(strip_tags($_POST["phone"] ?? ''));
    $email      = filter_var($_POST["email"] ?? '', FILTER_SANITIZE_EMAIL);
    $role       = htmlspecialchars(strip_tags($_POST["status"] ?? ''));
    $experience = htmlspecialchars(strip_tags($_POST["experience"] ?? ''));
    $details    = htmlspecialchars(strip_tags($_POST["details"] ?? ''));

    // 3. Basic Validation
    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid submission. Please check your name and email.");
    }

    // 4. File Handling (Resume Upload)
    $attachment = null;
    $uploadSuccess = false;

    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['fileToUpload']['tmp_name'];
        $fileName = $_FILES['fileToUpload']['name'];
        $fileSize = $_FILES['fileToUpload']['size'];
        $fileType = $_FILES['fileToUpload']['type'];
        
        // Limit file size (e.g., 5MB)
        if ($fileSize < 5000000) { 
            $attachment = $fileTmpPath; // Pass this path to your mail function
            $uploadSuccess = true;
        }
    }

    // 5. Professional Email Body (HTML Table)
    $body = "
    <div style='font-family: Arial, sans-serif; color: #333;'>
        <h2 style='color: #007bff;'>New Career Application</h2>
        <table border='0' cellpadding='10' cellspacing='0' style='width: 100%; border: 1px solid #eee;'>
            <tr style='background: #f9f9f9;'><td><strong>Full Name:</strong></td><td>$name</td></tr>
            <tr><td><strong>Email:</strong></td><td>$email</td></tr>
            <tr style='background: #f9f9f9;'><td><strong>Phone:</strong></td><td>$phone</td></tr>
            <tr><td><strong>Applied Position:</strong></td><td><span style='padding: 5px 10px; background: #e7f3ff; color: #007bff; border-radius: 4px;'>$role</span></td></tr>
            <tr style='background: #f9f9f9;'><td><strong>Experience:</strong></td><td>$experience Years</td></tr>
            <tr><td><strong>Details:</strong></td><td>$details</td></tr>
        </table>
        <p style='font-size: 12px; color: #777; margin-top: 20px;'>Submission Date: " . date("Y-m-d H:i:s") . "</p>
    </div>";

    /**
     * NOTE: You will need to update your mailfunction() inside mailfunction.php 
     * to accept an optional 4th parameter for the attachment path.
     */
    $status = mailfunction("hr@yourcompany.com", "Career Dept", $body, $attachment); 

    // 6. User Experience: Redirect with status
    if($status) {
        // Redirect back to careers page with a success flag
        header("Location: careers.html?status=success");
        exit();
    } else {
        echo '<div style="text-align:center; margin-top:50px; font-family:sans-serif;">
                <h1 style="color:red;">Error sending application!</h1>
                <p>Please check your internet connection and try again.</p>
                <a href="careers.html">Go Back</a>
              </div>';
    }
?>
