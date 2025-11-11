<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;
use Exception;

/**
 * SMS Service Library
 * 
 * Handles SMS sending through modem gateway
 * Supports multiple carrier detection (PLDT, Globe, Smart, TNT, Sun, DITO)
 */
class SmsService
{
    /**
     * Modem gateway URL
     * @var string
     */
    private string $gatewayUrl;

    /**
     * Modem authentication username
     * @var string
     */
    private string $username;

    /**
     * Modem authentication password
     * @var string
     */
    private string $password;

    /**
     * Default line/carrier (1 = PLDT/Globe, 2 = Smart/TNT/Sun, 3 = DITO)
     * @var string
     */
    private string $defaultLine = '1';

    /**
     * Phone number prefix to carrier line mapping
     * @var array
     */
    private array $carrierMap = [
        // PLDT/Globe
        '0817' => '1', '0905' => '1', '0906' => '1', '0915' => '1',
        '0916' => '1', '0917' => '1', '0926' => '1', '0927' => '1',
        '0935' => '1', '0936' => '1', '0937' => '1', '0945' => '1',
        '0955' => '1', '0956' => '1', '0965' => '1', '0966' => '1',
        '0967' => '1', '0973' => '1', '0975' => '1', '0976' => '1',
        '0977' => '1', '0978' => '1', '0979' => '1', '0994' => '1',
        '0995' => '1', '0996' => '1', '0997' => '1',

        // Smart/TNT/Sun
        '0813' => '2', '0907' => '2', '0908' => '2', '0909' => '2',
        '0910' => '2', '0911' => '2', '0912' => '2', '0913' => '2',
        '0914' => '2', '0918' => '2', '0919' => '2', '0921' => '2',
        '0928' => '2', '0929' => '2', '0930' => '2', '0938' => '2',
        '0940' => '2', '0946' => '2', '0947' => '2', '0948' => '2',
        '0949' => '2', '0950' => '2', '0951' => '2', '0970' => '2',
        '0981' => '2', '0989' => '2', '0992' => '2', '0998' => '2',
        '0999' => '2', '0922' => '2', '0923' => '2', '0924' => '2',
        '0925' => '2', '0931' => '2', '0932' => '2', '0933' => '2',
        '0934' => '2', '0941' => '2', '0942' => '2', '0943' => '2',
        '0944' => '2',

        // DITO
        '0991' => '3', '0892' => '3', '0895' => '3', '0896' => '3',
        '0897' => '3', '0898' => '3',
    ];

    /**
     * Log messages for debugging
     * @var array
     */
    private array $logs = [];

    /**
     * Constructor
     * 
     * Can be used to override default configuration
     * 
     * @param array $config Configuration array with keys: gatewayUrl, username, password, defaultLine
     */
    public function __construct(array $config = [])
    {
        // Load from environment variables first
        $this->gatewayUrl = getenv('SMS_MODEM_URL') ?: 'http://192.168.1.251/default/en_US/send.html';
        $this->username = getenv('SMS_USER') ?: 'admin';
        $this->password = getenv('SMS_PASS') ?: '285952';
        
        // Override with passed config if provided
        if (!empty($config['gatewayUrl'])) {
            $this->gatewayUrl = $config['gatewayUrl'];
        }
        if (!empty($config['username'])) {
            $this->username = $config['username'];
        }
        if (!empty($config['password'])) {
            $this->password = $config['password'];
        }
        if (!empty($config['defaultLine'])) {
            $this->defaultLine = $config['defaultLine'];
        }
        if (!empty($config['carrierMap'])) {
            $this->carrierMap = array_merge($this->carrierMap, $config['carrierMap']);
        }
    }

    /**
     * Send SMS message to a phone number
     * 
     * @param string $phoneNumber Phone number (e.g., "09171234567")
     * @param string $message SMS message content
     * 
     * @return array Response array with keys: success (bool), message (string), response (string)
     * 
     * @throws Exception
     */
    public function send(string $phoneNumber, string $message): array
    {
        try {
            // Validate inputs
            $this->validatePhoneNumber($phoneNumber);
            $this->validateMessage($message);

            // Detect carrier
            $line = $this->detectCarrier($phoneNumber);

            // Prepare request
            $fields = [
                'u' => $this->username,
                'p' => $this->password,
                'l' => $line,
                'n' => $phoneNumber,
                'm' => $message,
            ];

            // Build query string
            $queryString = $this->buildQueryString($fields);

            // Add log entry
            $this->addLog("Attempting to send SMS to {$phoneNumber} via line {$line}");

            // Send request via cURL
            $response = $this->sendViaCurl($queryString);

            // Log response
            $this->addLog("Gateway response: {$response}");

            // Determine success
            $isSuccessful = $this->isSuccessfulResponse($response);

            return [
                'success'  => $isSuccessful,
                'message'  => $isSuccessful ? 'SMS sent successfully' : 'Failed to send SMS',
                'response' => $response,
                'phone'    => $phoneNumber,
                'carrier'  => $this->getCarrierName($line),
                'timestamp' => Time::now()->toDateTimeString(),
            ];
        } catch (Exception $e) {
            $this->addLog('Error: ' . $e->getMessage());

            return [
                'success'   => false,
                'message'   => $e->getMessage(),
                'response'  => '',
                'timestamp' => Time::now()->toDateTimeString(),
            ];
        }
    }

    /**
     * Send bulk SMS to multiple recipients
     * 
     * @param array $recipients Phone numbers array
     * @param string $message SMS message content
     * 
     * @return array Array of results for each recipient
     */
    public function sendBulk(array $recipients, string $message): array
    {
        $results = [];

        foreach ($recipients as $phone) {
            $results[] = $this->send($phone, $message);
        }

        return $results;
    }

    /**
     * Detect carrier/line based on phone number prefix
     * 
     * @param string $phoneNumber Phone number
     * 
     * @return string Line number (1, 2, or 3)
     */
    private function detectCarrier(string $phoneNumber): string
    {
        // Extract first 4 digits
        $prefix = substr($phoneNumber, 0, 4);

        // Return mapped line or default
        return $this->carrierMap[$prefix] ?? $this->defaultLine;
    }

    /**
     * Get carrier name from line number
     * 
     * @param string $line Line number
     * 
     * @return string Carrier name
     */
    private function getCarrierName(string $line): string
    {
        return match ($line) {
            '1' => 'PLDT/Globe',
            '2' => 'Smart/TNT/Sun',
            '3' => 'DITO',
            default => 'Unknown',
        };
    }

    /**
     * Validate phone number format
     * 
     * @param string $phoneNumber Phone number to validate
     * 
     * @return void
     * 
     * @throws Exception
     */
    private function validatePhoneNumber(string $phoneNumber): void
    {
        // Remove any non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Check if it's a valid Philippine mobile number
        if (strlen($cleaned) !== 11) {
            throw new Exception('Invalid phone number format. Must be 11 digits (e.g., 09171234567)');
        }

        if (!preg_match('/^09\d{9}$/', $cleaned)) {
            throw new Exception('Invalid Philippine phone number. Must start with 09');
        }
    }

    /**
     * Validate message content
     * 
     * @param string $message Message to validate
     * 
     * @return void
     * 
     * @throws Exception
     */
    private function validateMessage(string $message): void
    {
        if (empty(trim($message))) {
            throw new Exception('Message cannot be empty');
        }

        if (strlen($message) > 160) {
            throw new Exception('Message exceeds maximum length of 160 characters');
        }
    }

    /**
     * Build query string from fields array
     * 
     * @param array $fields Form fields
     * 
     * @return string Query string
     */
    private function buildQueryString(array $fields): string
    {
        $queryParts = [];
        foreach ($fields as $key => $value) {
            $queryParts[] = urlencode($key) . '=' . urlencode($value);
        }

        return implode('&', $queryParts);
    }

    /**
     * Send request via cURL
     * 
     * @param string $queryString Query string to send
     * 
     * @return string Response from gateway
     * 
     * @throws Exception
     */
    private function sendViaCurl(string $queryString): string
    {
        $ch = curl_init();

        if ($ch === false) {
            throw new Exception('Failed to initialize cURL');
        }

        try {
            curl_setopt($ch, CURLOPT_URL, $this->gatewayUrl);
            curl_setopt($ch, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            $response = curl_exec($ch);

            if ($response === false) {
                throw new Exception('cURL error: ' . curl_error($ch));
            }

            return $response;
        } finally {
            curl_close($ch);
        }
    }

    /**
     * Check if gateway response indicates success
     * 
     * @param string $response Response from gateway
     * 
     * @return bool True if successful
     */
    private function isSuccessfulResponse(string $response): bool
    {
        // Adjust these checks based on your gateway's response format
        if (empty($response)) {
            return false;
        }

        // Common success indicators - adjust based on your modem's actual response
        $successPatterns = [
            '/success/i',
            '/sent/i',
            '/ok/i',
            '/0$/', // Some gateways return 0 for success
        ];

        foreach ($successPatterns as $pattern) {
            if (preg_match($pattern, $response)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add log entry
     * 
     * @param string $message Log message
     * 
     * @return void
     */
    private function addLog(string $message): void
    {
        $this->logs[] = [
            'timestamp' => Time::now()->toDateTimeString(),
            'message'   => $message,
        ];
    }

    /**
     * Get all logs
     * 
     * @return array Array of log entries
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * Clear logs
     * 
     * @return void
     */
    public function clearLogs(): void
    {
        $this->logs = [];
    }

    /**
     * Get configuration
     * 
     * @return array Current configuration
     */
    public function getConfig(): array
    {
        return [
            'gatewayUrl'  => $this->gatewayUrl,
            'username'    => $this->username,
            'defaultLine' => $this->defaultLine,
        ];
    }

    /**
     * Test connection to SMS gateway
     * 
     * @return array Test result
     */
    public function testConnection(): array
    {
        try {
            $this->addLog('Testing gateway connection...');

            // Test with basic connection check
            $ch = curl_init();

            if ($ch === false) {
                throw new Exception('Failed to initialize cURL');
            }

            curl_setopt($ch, CURLOPT_URL, $this->gatewayUrl);
            curl_setopt($ch, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true); // Just check headers

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            curl_close($ch);

            $isConnected = ($httpCode === 200 || $httpCode === 401 || $httpCode === 302);

            $this->addLog("Gateway test completed. HTTP: {$httpCode}");

            return [
                'success'          => $isConnected,
                'message'          => $isConnected ? 'Gateway is reachable' : 'Gateway unreachable',
                'gateway_url'      => $this->gatewayUrl,
                'http_code'        => $httpCode,
                'curl_error'       => $error ?: 'None',
                'curl_enabled'     => extension_loaded('curl') ? 'Yes' : 'No',
                'timestamp'        => Time::now()->toDateTimeString(),
            ];
        } catch (Exception $e) {
            return [
                'success'   => false,
                'message'   => 'Test failed: ' . $e->getMessage(),
                'gateway_url' => $this->gatewayUrl,
                'timestamp' => Time::now()->toDateTimeString(),
            ];
        }
    }
}
