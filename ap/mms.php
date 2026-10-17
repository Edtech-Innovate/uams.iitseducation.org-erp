<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug information
$debug = [
    'status' => 'processing',
    'post_data' => $_POST,
    'server_info' => [
        'php_version' => PHP_VERSION,
        'mail_function_exists' => function_exists('mail'),
        'last_error' => error_get_last()
    ]
];

// Very simple mail sending function
function sendMail($to, $from, $name, $subject, $message) {
    $headers = "From: $name <$from>\r\n";
    $headers .= "Reply-To: $from\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    return mail($to, $subject, $message, $headers);
}

// Handle form submission
if(isset($_POST['submit'])) {
    try {
        $to = $_POST['email'];
        $from = $_POST['from'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        
        // Generate a unique boundary
        $boundary = md5(uniqid(time()));
        
        // Proper MIME headers exactly matching working version
        $headers = array();
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'X-Mailer: Apple Mail (2.1283)';
        $headers[] = 'Message-ID: <' . md5(uniqid()) . '@wptest.techoriz.in>';
        $headers[] = 'Date: ' . date('r');
        $headers[] = 'From: "GLPI " <' . $from . '>';
        $headers[] = 'X-Sender-IP: 162.240.236.114';
        $headers[] = 'X-Sending-Domain: ';
        $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';
        $headers[] = 'Content-Transfer-Encoding: base64';
        
        // Format the message with proper MIME structure
        $formatted_message = "--" . $boundary . "\r\n";
        $formatted_message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $formatted_message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $formatted_message .= base64_encode($message) . "\r\n";
        $formatted_message .= "--" . $boundary . "--";
        
        $debug['mail_attempt'] = [
            'to' => $to,
            'from' => $from,
            'subject' => $subject,
            'headers' => implode("\r\n", $headers),
            'message' => $formatted_message
        ];
        
        // Log the attempt
        $log_message = date('Y-m-d H:i:s') . " - Attempting to send mail to: $to\n";
        $log_message .= "Headers: " . implode("\n", $headers) . "\n";
        $log_message .= "Message: $formatted_message\n";
        file_put_contents('mail_log.txt', $log_message, FILE_APPEND);
        
        if(mail($to, $subject, $formatted_message, implode("\r\n", $headers))) {
            $debug['status'] = 'success';
            $debug['result'] = 'Email sent successfully';
            
            // Log success
            file_put_contents('mail_log.txt', date('Y-m-d H:i:s') . " - Mail sent successfully to: $to\n", FILE_APPEND);
        } else {
            $debug['status'] = 'error';
            $debug['result'] = 'Failed to send email';
            $debug['last_error'] = error_get_last();
            
            // Log failure
            file_put_contents('mail_log.txt', date('Y-m-d H:i:s') . " - Failed to send mail to: $to\n", FILE_APPEND);
        }
    } catch (Exception $e) {
        $debug['status'] = 'exception';
        $debug['error'] = $e->getMessage();
        $debug['trace'] = $e->getTraceAsString();
        
        // Log exception
        file_put_contents('mail_log.txt', date('Y-m-d H:i:s') . " - Exception: " . $e->getMessage() . "\n", FILE_APPEND);
    }
    
    // Output debug information
    header('Content-Type: application/json');
    echo json_encode($debug, JSON_PRETTY_PRINT);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mailer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; }
    </style>
</head>
<body>
    <form method="post">
        <div class="form-group">
            <label>To (Email or SMS):</label>
            <input type="text" name="email" required placeholder="email@example.com or 1234567890@vtext.com">
        </div>
        <div class="form-group">
            <label>From Email:</label>
            <input type="email" name="from" required value="walmart@wptest.techoriz.in">
        </div>
        <div class="form-group">
            <label>Subject:</label>
            <input type="text" name="subject" required>
        </div>
        <div class="form-group">
            <label>Message:</label>
            <textarea name="message" required></textarea>
        </div>
        <input type="submit" name="submit" value="Send">
    </form>
    
    <h3>Debug Information:</h3>
    <pre><?php echo json_encode($debug, JSON_PRETTY_PRINT); ?></pre>
    
    <?php if(file_exists('mail_log.txt')): ?>
    <h3>Mail Log:</h3>
    <pre><?php echo htmlspecialchars(file_get_contents('mail_log.txt')); ?></pre>
    <?php endif; ?>
</body>
</html> 