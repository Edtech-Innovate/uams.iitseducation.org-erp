<?php

require_once 'Mailer.php';

// Create new mailer instance
$mailer = new Mailer('sender@yourdomain.com', 'Your Name');

// Example HTML email content
$htmlContent = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Email</title>
</head>
<body>
    <h1>Hello!</h1>
    <p>This is a test email sent using our custom mailer.</p>
    <p>It includes:</p>
    <ul>
        <li>Proper headers</li>
        <li>HTML and text versions</li>
        <li>Attachment support</li>
        <li>Logging</li>
    </ul>
</body>
</html>
';

// Optional plain text version (will be auto-generated from HTML if not provided)
$textContent = "Hello!\n\nThis is a test email sent using our custom mailer.\n\n";

try {
    // Configure additional options
    $mailer->addReplyTo('reply@yourdomain.com', 'Reply Handler')
           ->addCC('cc@yourdomain.com')
           ->addBCC('bcc@yourdomain.com')
           ->addAttachment('test.pdf');

    // Send the email
    $result = $mailer->send(
        'recipient@example.com',
        'Test Email',
        $htmlContent,
        $textContent
    );

    if ($result) {
        echo "Email sent successfully!";
    } else {
        echo "Failed to send email.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} 