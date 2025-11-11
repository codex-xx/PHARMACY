<?php

namespace App\Controllers;

use App\Libraries\SmsService;

/**
 * SMS Controller Example
 * 
 * Demonstrates how to use the SMS Service to send SMS messages
 */
class SmsController extends BaseController
{
    /**
     * SMS Service instance
     * @var SmsService
     */
    protected SmsService $smsService;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize SMS Service
        $this->smsService = service('sms');
    }

    /**
     * Send single SMS
     * 
     * POST /sms/send
     * Body: {
     *   "phone": "09171234567",
     *   "message": "Hello, this is a test message"
     * }
     */
    public function send()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setStatusCode(405)->setJSON([
                'error' => 'Method Not Allowed',
            ]);
        }

        $data = $this->request->getJSON();

        // Validate input
        if (!isset($data->phone) || !isset($data->message)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error'   => 'Missing required fields: phone and message',
            ]);
        }

        // Send SMS
        $result = $this->smsService->send($data->phone, $data->message);

        $statusCode = $result['success'] ? 200 : 500;

        return $this->response->setStatusCode($statusCode)->setJSON($result);
    }

    /**
     * Send bulk SMS
     * 
     * POST /sms/bulk
     * Body: {
     *   "phones": ["09171234567", "09181234567"],
     *   "message": "Hello everyone!"
     * }
     */
    public function sendBulk()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setStatusCode(405)->setJSON([
                'error' => 'Method Not Allowed',
            ]);
        }

        $data = $this->request->getJSON();

        // Validate input
        if (!isset($data->phones) || !isset($data->message)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Missing required fields: phones and message',
            ]);
        }

        if (!is_array($data->phones)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'phones must be an array',
            ]);
        }

        // Send bulk SMS
        $results = $this->smsService->sendBulk($data->phones, $data->message);

        return $this->response->setStatusCode(200)->setJSON([
            'total'    => count($results),
            'results'  => $results,
        ]);
    }

    /**
     * Get service logs
     * 
     * GET /sms/logs
     */
    public function getLogs()
    {
        $logs = $this->smsService->getLogs();

        return $this->response->setStatusCode(200)->setJSON([
            'logs' => $logs,
        ]);
    }

    /**
     * Get service configuration
     * 
     * GET /sms/config
     */
    public function getConfig()
    {
        $config = $this->smsService->getConfig();

        return $this->response->setStatusCode(200)->setJSON($config);
    }

    /**
     * Test SMS Gateway Connection
     * 
     * GET /sms/test
     * Performs connection test to modem gateway
     */
    public function test()
    {
        try {
            $testResult = $this->smsService->testConnection();

            return $this->response->setStatusCode(200)->setJSON($testResult);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get SMS Gateway Status
     * 
     * GET /sms/status
     */
    public function status()
    {
        $status = [
            'enabled'      => env('SMS_ENABLED', false),
            'modem_url'    => env('SMS_MODEM_URL'),
            'username'     => env('SMS_USER'),
            'environment'  => env('CI_ENVIRONMENT'),
            'timestamp'    => date('Y-m-d H:i:s'),
            'php_curl'     => extension_loaded('curl') ? 'Enabled' : 'Disabled',
            'message'      => 'SMS gateway is ' . (env('SMS_ENABLED', false) ? 'enabled' : 'disabled'),
        ];

        return $this->response->setStatusCode(200)->setJSON($status);
    }
}
