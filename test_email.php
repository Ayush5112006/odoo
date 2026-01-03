<?php
include("config/email.php");

// Test email sending
$test_email = "nivasgruh@gmail.com"; // Send to yourself for testing
$subject = "HRMS - Test Email";
$message = "
<html>
<body style='font-family: Arial, sans-serif;'>
    <h2>Test Email</h2>
    <p>This is a test email from your HRMS system.</p>
    <p>If you received this, your email system is working correctly!</p>
    <p>Timestamp: " . date("Y-m-d H:i:s") . "</p>
</body>
</html>";

$result = sendEmail($test_email, "Admin", $subject, $message);

if ($result) {
    echo "✅ <strong>SUCCESS!</strong> Test email sent to: " . $test_email;
    echo "<br>Check your inbox.";
} else {
    echo "❌ <strong>FAILED!</strong> Email could not be sent.";
    echo "<br>Check your SMTP credentials in config/email.php";
}

echo "<br><br>";
echo "Email Configuration:<br>";
echo "Host: " . SMTP_HOST . "<br>";
echo "Port: " . SMTP_PORT . "<br>";
echo "Username: " . SMTP_USERNAME . "<br>";
echo "<a href='index.php'>Back to Home</a>";
?>
