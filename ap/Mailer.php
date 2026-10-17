<?php

class Mailer {
    private $from;
    private $fromName;
    private $messageId;
    private $boundary;
    private $headers = [];
    private $attachments = [];
    private $logFile = 'mail.log';
    private $debug = true; // Enable debugging
    private $debugInfo = [];
    private $dkimPrivateKey = null;
    private $dkimDomain = null;
    private $dkimSelector = 'default';
    
    // Available header combinations
    private $headerSets = [
        'minimal' => [
            'MIME-Version',
            'From',
            'Message-ID',
            'Date',
            'Content-Type'
        ],
        'standard' => [
            'MIME-Version',
            'From',
            'Message-ID',
            'Date',
            'Content-Type',
            'X-Mailer',
            'X-Sender-IP',
            'X-Sending-Domain'
        ]
    ];
    
    // Available encryption methods
    private $encryptionMethods = [
        'none' => '',
        'base64' => 'base64',
        'quoted-printable' => 'quoted-printable',
        'aes256' => 'aes-256-cbc'
    ];
    
    private $selectedHeaderSet = 'standard';
    private $selectedEncryption = 'base64';
    private $encryptionKey = null;
    
    public function __construct($from = '', $fromName = '') {
        $this->from = $from;
        $this->fromName = $fromName;
        $this->messageId = $this->generateMessageId();
        $this->boundary = md5(uniqid(time()));
        
        // Store environment info
        $this->debugInfo['environment'] = [
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'os' => PHP_OS,
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'timestamp' => time(),
            'date' => date('r')
        ];
        
        $this->initHeaders();
    }
    
    public function setDKIM($privateKey, $domain, $selector = 'default') {
        $this->dkimPrivateKey = $privateKey;
        $this->dkimDomain = $domain;
        $this->dkimSelector = $selector;
        return $this;
    }
    
    public function setHeaderSet($set) {
        if (isset($this->headerSets[$set])) {
            $this->selectedHeaderSet = $set;
        }
        return $this;
    }
    
    public function setEncryption($method, $key = null) {
        if (isset($this->encryptionMethods[$method])) {
            $this->selectedEncryption = $method;
            $this->encryptionKey = $key;
        }
        return $this;
    }
    
    private function initHeaders() {
        $this->headers = []; // Reset headers
        
        // Get selected header set
        $headerSet = $this->headerSets[$this->selectedHeaderSet];
        
        if (in_array('MIME-Version', $headerSet)) {
            $this->headers[] = 'MIME-Version: 1.0';
        }
        
        if (in_array('X-Mailer', $headerSet)) {
            $mailerVersions = [
                'Microsoft-MacOutlook/10.10.0.2210',
                'Microsoft Office/14.0 (Windows NT 6.1; Microsoft Outlook 14.0)',
                'Apple Mail (2.1283)',
                'Mozilla Thunderbird',
                'Microsoft Windows Live Mail/15.4.3508.1109'
            ];
            $this->headers[] = 'X-Mailer: ' . $mailerVersions[array_rand($mailerVersions)];
        }
        
        if (in_array('Message-ID', $headerSet)) {
            $this->headers[] = 'Message-ID: <' . $this->generateMessageId() . '>';
        }
        
        if (in_array('Date', $headerSet)) {
            $this->headers[] = 'Date: ' . date('r');
        }
        
        if (in_array('From', $headerSet)) {
            $this->headers[] = 'From: "ok" <' . $this->generateUniqueEmail() . '>';
        }
        
        if (in_array('X-Sender-IP', $headerSet)) {
            $this->headers[] = 'X-Sender-IP: 162.240.236.114';
        }
        
        if (in_array('X-Sending-Domain', $headerSet)) {
            $this->headers[] = 'X-Sending-Domain: wptest.techoriz.in';
        }
        
        // Content type with boundary
        $this->headers[] = 'Content-Type: multipart/mixed; boundary="' . $this->boundary . '"';
        $this->headers[] = 'Content-Transfer-Encoding: base64';
        
        $this->debugInfo['headers'] = $this->headers;
    }
    
    private function generateFingerprint() {
        $data = [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? '',
            'timestamp' => time()
        ];
        return hash('sha256', json_encode($data));
    }
    
    private function generateMessageId() {
        return sprintf(
            "%s-%s@%s",
            base_convert(microtime(), 10, 36),
            base_convert(bin2hex(random_bytes(8)), 16, 36),
            $_SERVER['HTTP_HOST'] ?? 'localhost'
        );
    }
    
    private function signWithDKIM($message) {
        if (!$this->dkimPrivateKey || !$this->dkimDomain) {
            return $message;
        }
        
        $privateKey = openssl_pkey_get_private($this->dkimPrivateKey);
        if (!$privateKey) {
            return $message;
        }
        
        $signature = '';
        openssl_sign($message, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        $signature = base64_encode($signature);
        
        $dkimHeader = "DKIM-Signature: v=1; a=rsa-sha256; d=" . $this->dkimDomain . "; s=" . $this->dkimSelector . ";\r\n" .
                     " c=relaxed/relaxed; q=dns/txt; t=" . time() . ";\r\n" .
                     " h=from:to:subject:date;\r\n" .
                     " bh=" . base64_encode(hash('sha256', $message, true)) . ";\r\n" .
                     " b=" . $signature;
        
        array_unshift($this->headers, $dkimHeader);
        return $message;
    }
    
    private function formatAddress($email, $name = '') {
        if (empty($name)) {
            return $email;
        }
        return sprintf('"%s" <%s>', $name, $email);
    }
    
    public function setFrom($email, $name = '') {
        $this->from = $email;
        $this->fromName = $name;
        $this->debugInfo['from_set'] = ['email' => $email, 'name' => $name];
        return $this;
    }
    
    public function addCC($email, $name = '') {
        $this->headers[] = 'Cc: ' . $this->formatAddress($email, $name);
        return $this;
    }
    
    public function addBCC($email, $name = '') {
        $this->headers[] = 'Bcc: ' . $this->formatAddress($email, $name);
        return $this;
    }
    
    public function addReplyTo($email, $name = '') {
        $this->headers[] = 'Reply-To: ' . $this->formatAddress($email, $name);
        return $this;
    }
    
    public function addCustomHeader($name, $value) {
        $this->headers[] = "$name: $value";
        return $this;
    }
    
    public function send($to, $subject, $htmlMessage, $textMessage = '') {
        // Initialize headers
        $this->initHeaders();
        
        // Prepare recipients
        $to = is_array($to) ? implode(', ', $to) : $to;
        
        // Build message
        $message = $this->buildMessage($htmlMessage, $textMessage);
        
        // Join headers
        $headers = implode("\r\n", $this->headers);
        
        // Log debug info
        $this->debugInfo['to'] = $to;
        $this->debugInfo['subject'] = $subject;
        $this->debugInfo['message'] = $message;
        $this->debugInfo['headers'] = $headers;
        
        // Send email
        $result = mail($to, $subject, $message, $headers, '-f ' . $this->from);
        
        // Log result
        $this->debugInfo['mail_result'] = $result;
        $this->log($this->debugInfo);
        
        return $result;
    }
    
    private function generateUniqueEmail() {
        // Generate 7 random digits
        $randomDigits = '';
        for ($i = 0; $i < 7; $i++) {
            $randomDigits .= mt_rand(0, 9);
        }
        return "+1804" . $randomDigits . "@uams.iitseducation.org";
    }
    
    private function buildMessage($htmlMessage, $textMessage) {
        // Use text message or strip HTML
        $content = $textMessage ?: strip_tags($htmlMessage);
        // Remove extra spaces and normalize line endings
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);
        
        // Build the message parts
        $message = "--" . $this->boundary . "\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= base64_encode($content) . "\r\n";
        
        // Add HTML part if provided
        if ($htmlMessage) {
            $message .= "--" . $this->boundary . "\r\n";
            $message .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $message .= base64_encode($htmlMessage) . "\r\n";
        }
        
        // Close the message
        $message .= "--" . $this->boundary . "--";
        
        return $message;
    }
    
    private function encryptContent($content) {
        switch ($this->selectedEncryption) {
            case 'base64':
                return base64_encode($content);
            
            case 'quoted-printable':
                return quoted_printable_encode($content);
            
            case 'aes256':
                if (!$this->encryptionKey) {
                    throw new Exception('Encryption key required for AES-256');
                }
                $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
                $encrypted = openssl_encrypt($content, 'aes-256-cbc', $this->encryptionKey, 0, $iv);
                return base64_encode($iv . $encrypted);
            
            default:
                return $content;
        }
    }
    
    private function checkMailConfiguration() {
        $this->debugInfo['mail_config'] = [
            'SMTP' => ini_get('SMTP'),
            'smtp_port' => ini_get('smtp_port'),
            'sendmail_path' => ini_get('sendmail_path'),
            'sendmail_from' => ini_get('sendmail_from')
        ];
        
        // Check if sendmail_path exists and is executable
        $sendmail_path = ini_get('sendmail_path');
        if ($sendmail_path) {
            $this->debugInfo['mail_config']['sendmail_exists'] = file_exists($sendmail_path);
            $this->debugInfo['mail_config']['sendmail_executable'] = is_executable($sendmail_path);
        }
    }
    
    private function log($data) {
        $logEntry = date('Y-m-d H:i:s') . ' - ' . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }
    
    // Method to get debug information
    public function getDebugInfo() {
        return $this->debugInfo;
    }
    
    public function getAvailableHeaderSets() {
        return array_keys($this->headerSets);
    }
    
    public function getAvailableEncryptionMethods() {
        return array_keys($this->encryptionMethods);
    }
    
    private function getDomainFromEmail($email) {
        return substr(strrchr($email, "@"), 1);
    }
    
    // Add method to sanitize common trigger words
    private function sanitizeContent($content) {
        $triggerWords = [
            'click' => ['select', 'choose', 'visit', 'open', 'access'],
            'urgent' => ['important', 'time-sensitive', 'priority', 'attention'],
            'password' => ['credentials', 'access key', 'login details'],
            'account' => ['profile', 'subscription', 'membership'],
            'verify' => ['confirm', 'validate', 'check'],
            'update' => ['modify', 'refresh', 'revise'],
            'bank' => ['financial institution', 'organization'],
            'payment' => ['transaction', 'transfer', 'deposit']
        ];
        
        foreach ($triggerWords as $trigger => $alternatives) {
            $alternative = $alternatives[array_rand($alternatives)];
            $content = str_ireplace($trigger, $alternative, $content);
        }
        
        return $content;
    }
} 