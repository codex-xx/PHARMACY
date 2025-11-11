<?php

namespace App\Controllers;

use App\Libraries\SmsService;

/**
 * SMS Verification & Testing Controller
 * 
 * Comprehensive testing and verification tools for SMS gateway
 */
class SmsVerificationController extends BaseController
{
    /**
     * SMS Service instance
     */
    protected SmsService $smsService;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->smsService = service('sms');
    }

    /**
     * Main Verification Dashboard
     * 
     * GET /sms-verify
     */
    public function index()
    {
        $data = [
            'title' => 'SMS Gateway Verification',
            'systemInfo' => $this->getSystemInfo(),
            'smsEnabled' => env('SMS_ENABLED', false),
        ];

        return view('sms_verification', $data);
    }

    /**
     * Full System Check
     * 
     * GET /sms-verify/check
     */
    public function fullCheck()
    {
        $checks = [
            'php_curl' => $this->checkPhpCurl(),
            'env_vars' => $this->checkEnvVariables(),
            'gateway_connection' => $this->checkGatewayConnection(),
            'network' => $this->checkNetworkAccess(),
            'configuration' => $this->checkConfiguration(),
        ];

        $allPassed = !in_array(false, array_column($checks, 'passed'));

        return $this->response->setJSON([
            'all_passed' => $allPassed,
            'timestamp'  => date('Y-m-d H:i:s'),
            'checks'     => $checks,
        ]);
    }

    /**
     * Check PHP cURL Extension
     */
    private function checkPhpCurl(): array
    {
        $curlEnabled = extension_loaded('curl');

        return [
            'name'    => 'PHP cURL Extension',
            'passed'  => $curlEnabled,
            'message' => $curlEnabled ? 'cURL is enabled' : 'cURL is NOT enabled. Run: php -m | grep curl',
            'action'  => !$curlEnabled ? 'Enable PHP cURL extension in php.ini' : null,
        ];
    }

    /**
     * Check Environment Variables
     */
    private function checkEnvVariables(): array
    {
        $modemUrl = env('SMS_MODEM_URL');
        $user = env('SMS_USER');
        $pass = env('SMS_PASS');
        $enabled = env('SMS_ENABLED', false);

        $allSet = !empty($modemUrl) && !empty($user) && !empty($pass);

        $details = [
            'SMS_MODEM_URL' => $modemUrl ? '✓ Set' : '✗ Missing',
            'SMS_USER' => $user ? '✓ Set' : '✗ Missing',
            'SMS_PASS' => $pass ? '✓ Set' : '✗ Missing',
            'SMS_ENABLED' => $enabled ? 'Yes' : 'No',
        ];

        return [
            'name'    => 'Environment Variables',
            'passed'  => $allSet,
            'message' => $allSet ? 'All SMS environment variables are set' : 'Some SMS variables are missing',
            'details' => $details,
            'action'  => !$allSet ? 'Check .env file for SMS_MODEM_URL, SMS_USER, SMS_PASS' : null,
        ];
    }

    /**
     * Check Gateway Connection
     */
    private function checkGatewayConnection(): array
    {
        $testResult = $this->smsService->testConnection();

        return [
            'name'    => 'Gateway Connection',
            'passed'  => $testResult['success'],
            'message' => $testResult['message'],
            'details' => [
                'Gateway URL' => $testResult['gateway_url'],
                'HTTP Code' => $testResult['http_code'] ?? 'N/A',
                'cURL Error' => $testResult['curl_error'] ?? 'None',
                'cURL Available' => $testResult['curl_enabled'] ?? 'Unknown',
            ],
            'action'  => !$testResult['success'] ? 'Verify modem IP (192.168.1.251) is reachable and web interface is working' : null,
        ];
    }

    /**
     * Check Network Access
     */
    private function checkNetworkAccess(): array
    {
        $modemIp = '192.168.1.251';

        // Try to ping or connect to the modem
        $fsockopen = @fsockopen($modemIp, 80, $errno, $errstr, 5);
        $reachable = $fsockopen !== false;

        if ($fsockopen) {
            fclose($fsockopen);
        }

        return [
            'name'    => 'Network Access',
            'passed'  => $reachable,
            'message' => $reachable ? "Modem at {$modemIp} is reachable" : "Cannot reach {$modemIp}",
            'details' => [
                'Target IP' => $modemIp,
                'Port' => 80,
                'Reachable' => $reachable ? 'Yes' : 'No',
                'Error' => $errstr ?? 'None',
            ],
            'action'  => !$reachable ? 'Check network connection and modem IP address' : null,
        ];
    }

    /**
     * Check SMS Configuration
     */
    private function checkConfiguration(): array
    {
        $config = $this->smsService->getConfig();
        $validUrl = filter_var($config['gatewayUrl'], FILTER_VALIDATE_URL) !== false;

        return [
            'name'    => 'SMS Configuration',
            'passed'  => $validUrl,
            'message' => $validUrl ? 'Configuration is valid' : 'Invalid gateway URL',
            'details' => [
                'Gateway URL' => $config['gatewayUrl'],
                'Default Line' => $config['defaultLine'],
                'URL Valid' => $validUrl ? 'Yes' : 'No',
            ],
            'action'  => !$validUrl ? 'Check SMS_MODEM_URL in .env' : null,
        ];
    }

    /**
     * Get System Information
     */
    private function getSystemInfo(): array
    {
        return [
            'php_version' => phpversion(),
            'operating_system' => php_uname(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'codeigniter_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
            'php_curl' => extension_loaded('curl') ? 'Enabled' : 'Disabled',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
        ];
    }

    /**
     * Generate Test Report
     * 
     * GET /sms-verify/report
     */
    public function generateReport()
    {
        $report = [
            'generated_at' => date('Y-m-d H:i:s'),
            'environment' => env('CI_ENVIRONMENT'),
            'sms_enabled' => env('SMS_ENABLED', false),
            'system_info' => $this->getSystemInfo(),
            'full_check' => json_decode(json_encode($this->fullCheck()->getJSON()), true),
        ];

        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Content-Disposition', 'attachment; filename="sms_report_' . date('Y-m-d_H-i-s') . '.json"')
            ->setJSON($report);
    }

    /**
     * Display HTML Verification Page
     * 
     * GET /sms-verify/dashboard
     */
    public function dashboard()
    {
        $checks = json_decode(json_encode($this->fullCheck()->getJSON()), true);

        return view('sms_dashboard', [
            'checks' => $checks['checks'],
            'allPassed' => $checks['all_passed'],
            'systemInfo' => $this->getSystemInfo(),
        ]);
    }

    /**
     * Test SMS Send
     * 
     * POST /sms-verify/test-send
     * Body: { "phone": "09171234567", "message": "Test message" }
     */
    public function testSend()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setStatusCode(405)->setJSON([
                'error' => 'Method Not Allowed',
            ]);
        }

        $data = $this->request->getJSON();

        if (!isset($data->phone) || !isset($data->message)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Missing phone or message',
            ]);
        }

        $result = $this->smsService->send($data->phone, $data->message);

        return $this->response->setJSON($result);
    }

    /**
     * Get Service Logs
     * 
     * GET /sms-verify/logs
     */
    public function getLogs()
    {
        $logs = $this->smsService->getLogs();

        return $this->response->setJSON([
            'total' => count($logs),
            'logs' => $logs,
        ]);
    }
}
