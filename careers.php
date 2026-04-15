<?php   
    require("./mailing/mailfunction.php");

    // 1. Sanitize Inputs (Prevent XSS and injection)
    $name         = htmlspecialchars(strip_tags($_POST["name"] ?? ''));
    $phone        = htmlspecialchars(strip_tags($_POST["phone"] ?? ''));
    $email        = filter_var($_POST["email"] ?? '', FILTER_SANITIZE_EMAIL);
    $applyfor     = htmlspecialchars(strip_tags($_POST["status"] ?? ''));
    $experience   = (int)($_POST["experience"] ?? 0);
    $otherdetails = htmlspecialchars(strip_tags($_POST["details"] ?? ''));

    // 2. File Upload Configuration
    $allowed_ext  = ['pdf', 'doc', 'docx'];
    $max_size     = 5 * 1024 * 1024; // 5MB Limit
    $upload_dir   = "uploads/"; // Ensure this folder exists and is writable

    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // 3. File Validation & Processing
    if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
        $file_name = $_FILES["fileToUpload"]["name"];
        $file_size = $_FILES["fileToUpload"]["size"];
        $file_tmp  = $_FILES["fileToUpload"]["tmp_name"];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Security Check: Validate Extension and Size
        if (!in_array($ext, $allowed_ext)) {
            die("<center><h1 style='color:red;'>Error: Only PDF, DOC, and DOCX files are allowed.</h1></center>");
        }

        if ($file_size > $max_size) {
            die("<center><h1 style='color:red;'>Error: File size must be under 5MB.</h1></center>");
        }

        // Feature: Unique Naming to prevent overwriting files with the same name
        $sanitized_name = preg_replace("/[^a-zA-Z0-9]/", "_", $name);
        $unique_filename = $sanitized_name . "_" . time() . "." . $ext;
        $final_path = $upload_dir . $unique_filename;

        if (move_uploaded_file($file_tmp, $final_path)) {
            
            // 4. Professional HTML Email Body
            $body = "
            <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px; border-radius: 10px;'>
                <h2 style='color: #007bff; border-bottom: 2px solid #007bff;'>New Career Application</h2>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Phone:</strong> {$phone}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Position:</strong> <span style='background: #e7f3ff; padding: 3px 8px; border-radius: 5px;'>{$applyfor}</span></p>
                <p><strong>Experience:</strong> {$experience} Years</p>
                <p><strong>Message:</strong><br>{$otherdetails}</p>
                <hr>
                <p style='font-size: 0.8rem; color: #777;'>Attached: Resume ({$unique_filename})</p>
            </div>";

            // 5. Send Email
            $status = mailfunction("hr@yourcompany.com", "HR Department", $body, $final_path); 

            if ($status) {
                // Feature: Clean up (Delete file from server after sending to save space)
                unlink($final_path); 
                
                // Better UX: Redirect to a success page
                echo '<center><div style="margin-top:50px; font-family: sans-serif;">
                        <h1 style="color: green;">Success!</h1>
                        <p>Thank you, ' . $name . '. Your application has been received.</p>
                        <a href="index.html" style="text-decoration:none; color:#007bff;">Return to Home</a>
                      </div></center>';
            } else {
                echo '<center><h1 style="color:red;">Email delivery failed. Please contact us directly.</h1></center>';
            }
        } else {
            echo "<center><h1 style='color:red;'>Error saving the uploaded file.</h1></center>";
        }
    } else {
        echo "<center><h1 style='color:red;'>Please upload a valid resume.</h1></center>";
    }
?>
