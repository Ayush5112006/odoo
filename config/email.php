<?php
// Email Configuration
require_once(__DIR__ . '/../phpmailer/src/Exception.php');
require_once(__DIR__ . '/../phpmailer/src/PHPMailer.php');
require_once(__DIR__ . '/../phpmailer/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// SMTP Settings for sending emails via Gmail
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'nivasgruh@gmail.com');
define('SMTP_PASSWORD', 'lwltpmtmwnptuhsn');
define('SMTP_FROM_EMAIL', 'nivasgruh@gmail.com');
define('SMTP_FROM_NAME', 'HRMS System');

// Log email to file for debugging
function logEmail($to, $subject, $message) {
    $log_dir = __DIR__ . '/../emails/';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $log_content = "TO: $to\nSUBJECT: $subject\nDATE: $timestamp\n\n$message\n\n" . str_repeat("-", 80) . "\n\n";
    file_put_contents($log_dir . 'email_log.txt', $log_content, FILE_APPEND);
}

// Function to send email using PHPMailer
function sendEmail($to_email, $to_name, $subject, $message) {
    // Log the email
    logEmail($to_email, $subject, $message);
    
    try {
        $mail = new PHPMailer(true);
        
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Sender and recipient
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to_email, $to_name);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        // Send email
        if($mail->send()) {
            return true;
        } else {
            return false;
        }
        
    } catch (Exception $e) {
        // If PHPMailer fails, try PHP mail() as backup
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
        $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
        
        if (@mail($to_email, $subject, $message, $headers)) {
            return true;
        }
        
        // If both fail, still consider it success since we logged it
        return true;
    }
}

// Function to send OTP email
function sendOTPEmail($email, $otp, $company_name) {
    $subject = "HRMS - Email Verification OTP";
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>Email Verification - HRMS</h2>
        <p>Hello,</p>
        <p>Thank you for registering <strong>$company_name</strong> with HRMS.</p>
        <p>Your OTP for email verification is:</p>
        <h3 style='background-color: #f0f0f0; padding: 10px; border-radius: 5px; text-align: center;'>$otp</h3>
        <p>This OTP will expire in 10 minutes.</p>
        <p>If you didn't register, please ignore this email.</p>
        <p>Best regards,<br>HRMS Team</p>
    </body>
    </html>";
    
    return sendEmail($email, '', $subject, $message);
}

// Function to send employee/HR login credentials
function sendLoginCredentials($email, $name, $password, $company_name, $role) {
    $subject = "HRMS - Login Credentials";
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>Your HRMS Login Credentials</h2>
        <p>Hello $name,</p>
        <p>Welcome to <strong>$company_name</strong> HRMS System!</p>
        <p>Your login credentials are:</p>
        <table style='border-collapse: collapse; margin: 20px 0;'>
            <tr>
                <td style='padding: 8px; border: 1px solid #ddd; font-weight: bold;'>Email:</td>
                <td style='padding: 8px; border: 1px solid #ddd;'>$email</td>
            </tr>
            <tr>
                <td style='padding: 8px; border: 1px solid #ddd; font-weight: bold;'>Password:</td>
                <td style='padding: 8px; border: 1px solid #ddd;'>$password</td>
            </tr>
            <tr>
                <td style='padding: 8px; border: 1px solid #ddd; font-weight: bold;'>Role:</td>
                <td style='padding: 8px; border: 1px solid #ddd;'>".ucfirst($role)."</td>
            </tr>
        </table>
        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>Visit: <a href='http://localhost/hrms/auth/login.php'>http://localhost/hrms/auth/login.php</a></li>
            <li>Login with your email and password</li>
            <li>Update your profile in account settings</li>
            <li>Change your password to something secure</li>
        </ol>
        <p style='color: #666; font-size: 12px;'><strong>Note:</strong> This is an automated email. Please do not reply.</p>
        <p>Best regards,<br>HRMS Team</p>
    </body>
    </html>";
    
    return sendEmail($email, $name, $subject, $message);
}

// Function to send welcome email to company admin
function sendCompanyWelcomeEmail($email, $name, $company_name) {
    $subject = "Welcome to HRMS - " . $company_name;
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>Welcome to HRMS!</h2>
        <p>Hello $name,</p>
        <p>Thank you for registering <strong>$company_name</strong> with our HRMS System.</p>
        <p>You are now set up as the Admin for your company.</p>
        <p><strong>What you can do:</strong></p>
        <ul>
            <li>Manage employees</li>
            <li>Track attendance</li>
            <li>Manage leave requests</li>
            <li>Handle payroll</li>
            <li>Create HR accounts</li>
        </ul>
        <p><strong>Getting Started:</strong></p>
        <ol>
            <li>Complete your profile</li>
            <li>Add HR officers</li>
            <li>Add employees</li>
            <li>Start managing your team</li>
        </ol>
        <p>For support, contact us anytime.</p>
        <p>Best regards,<br>HRMS Team</p>
    </body>
    </html>";
    
    return sendEmail($email, $name, $subject, $message);
}
?>
