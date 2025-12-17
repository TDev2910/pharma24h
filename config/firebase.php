<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình Firebase cho ứng dụng. Các giá trị này được sử dụng để
    | kết nối với Firebase Authentication, Firestore, và các dịch vụ khác.
    |
    */

    'api_key' => env('FIREBASE_API_KEY', 'AIzaSyBCOzuBfeEwhs1Ybnn2Q9hFoPf2NnSDKuE'),
    'auth_domain' => env('FIREBASE_AUTH_DOMAIN', 'pharma24h-f0cd2.firebaseapp.com'),
    'project_id' => env('FIREBASE_PROJECT_ID', 'pharma24h-f0cd2'),
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', 'pharma24h-f0cd2.firebasestorage.app'),
    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', '989050282805'),
    'app_id' => env('FIREBASE_APP_ID', '1:989050282805:web:cecb5ec012be833b67227c'),
    'measurement_id' => env('FIREBASE_MEASUREMENT_ID', 'G-21D5BKCZX2'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Service Account
    |--------------------------------------------------------------------------
    |
    | Đường dẫn đến file service account JSON hoặc nội dung JSON.
    | Sử dụng cho backend operations với kreait/firebase-php
    |
    */
    'service_account' => env('FIREBASE_SERVICE_ACCOUNT', null),

    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration Array (for frontend)
    |--------------------------------------------------------------------------
    |
    | Array cấu hình đầy đủ để sử dụng trong frontend JavaScript
    |
    */
    'config' => [
        'apiKey' => env('FIREBASE_API_KEY', 'AIzaSyBCOzuBfeEwhs1Ybnn2Q9hFoPf2NnSDKuE'),
        'authDomain' => env('FIREBASE_AUTH_DOMAIN', 'pharma24h-f0cd2.firebaseapp.com'),
        'projectId' => env('FIREBASE_PROJECT_ID', 'pharma24h-f0cd2'),
        'storageBucket' => env('FIREBASE_STORAGE_BUCKET', 'pharma24h-f0cd2.firebasestorage.app'),
        'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID', '989050282805'),
        'appId' => env('FIREBASE_APP_ID', '1:989050282805:web:cecb5ec012be833b67227c'),
        'measurementId' => env('FIREBASE_MEASUREMENT_ID', 'G-21D5BKCZX2'),
    ],
];