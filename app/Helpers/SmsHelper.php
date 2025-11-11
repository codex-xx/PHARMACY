<?php

/**
 * SMS Helper Functions
 * 
 * Convenient functions for sending SMS throughout the application
 */

if (!function_exists('send_sms')) {
    /**
     * Quick function to send a single SMS
     * 
     * Usage:
     * send_sms('09171234567', 'Hello World');
     * 
     * @param string $phoneNumber Phone number to send to
     * @param string $message Message content
     * 
     * @return array Result array with keys: success, message, response
     */
    function send_sms(string $phoneNumber, string $message): array
    {
        $smsService = service('sms');

        return $smsService->send($phoneNumber, $message);
    }
}

if (!function_exists('send_sms_bulk')) {
    /**
     * Quick function to send bulk SMS
     * 
     * Usage:
     * send_sms_bulk(['09171234567', '09181234567'], 'Hello everyone!');
     * 
     * @param array $recipients Array of phone numbers
     * @param string $message Message content
     * 
     * @return array Array of result arrays
     */
    function send_sms_bulk(array $recipients, string $message): array
    {
        $smsService = service('sms');

        return $smsService->sendBulk($recipients, $message);
    }
}

if (!function_exists('get_sms_logs')) {
    /**
     * Get SMS service logs for debugging
     * 
     * Usage:
     * $logs = get_sms_logs();
     * 
     * @return array Array of log entries
     */
    function get_sms_logs(): array
    {
        $smsService = service('sms');

        return $smsService->getLogs();
    }
}

if (!function_exists('get_sms_config')) {
    /**
     * Get SMS service configuration
     * 
     * Usage:
     * $config = get_sms_config();
     * 
     * @return array Configuration array
     */
    function get_sms_config(): array
    {
        $smsService = service('sms');

        return $smsService->getConfig();
    }
}
