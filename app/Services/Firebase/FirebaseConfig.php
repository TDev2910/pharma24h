<?php

namespace App\Services\Firebase;

use Illuminate\Support\Facades\Config;

/**
 * Firebase Config Helper
 * 
 * Helper class để lấy và quản lý Firebase configuration
 */
class FirebaseConfig
{
    /**
     * Lấy Firebase config array cho frontend JavaScript
     * 
     * @return array
     */
    public static function getConfig(): array
    {
        return Config::get('firebase.config', []);
    }

    /**
     * Lấy Firebase config dạng JSON string
     * 
     * @return string
     */
    public static function getConfigJson(): string
    {
        return json_encode(self::getConfig(), JSON_UNESCAPED_SLASHES);
    }

    /**
     * Lấy giá trị config cụ thể
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Config::get("firebase.{$key}", $default);
    }

    /**
     * Lấy API Key
     * 
     * @return string
     */
    public static function getApiKey(): string
    {
        return self::get('api_key', '');
    }

    /**
     * Lấy Auth Domain
     * 
     * @return string
     */
    public static function getAuthDomain(): string
    {
        return self::get('auth_domain', '');
    }

    /**
     * Lấy Project ID
     * 
     * @return string
     */
    public static function getProjectId(): string
    {
        return self::get('project_id', '');
    }

    /**
     * Lấy Storage Bucket
     * 
     * @return string
     */
    public static function getStorageBucket(): string
    {
        return self::get('storage_bucket', '');
    }

    /**
     * Lấy Messaging Sender ID
     * 
     * @return string
     */
    public static function getMessagingSenderId(): string
    {
        return self::get('messaging_sender_id', '');
    }

    /**
     * Lấy App ID
     * 
     * @return string
     */
    public static function getAppId(): string
    {
        return self::get('app_id', '');
    }

    /**
     * Lấy Measurement ID
     * 
     * @return string
     */
    public static function getMeasurementId(): string
    {
        return self::get('measurement_id', '');
    }
}