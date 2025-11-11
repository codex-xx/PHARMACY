<?php

namespace Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /**
     * SMS Service for sending SMS via modem gateway
     *
     * @param bool $getShared Whether to return shared instance
     * @return \App\Libraries\SmsService
     */
    public static function sms($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('sms');
        }

        // Configuration for SMS service
        $config = [
            'gatewayUrl' => 'http://192.168.1.251/default/en_US/send.html?',
            'username'   => 'admin',
            'password'   => '285952',
            'defaultLine' => '1', // 1 = PLDT/Globe, 2 = Smart/TNT/Sun, 3 = DITO
        ];

        return new \App\Libraries\SmsService($config);
    }

    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */
}
