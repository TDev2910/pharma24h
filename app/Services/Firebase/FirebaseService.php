<?php

namespace App\Services\Firebase;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Firestore;
use Kreait\Firebase\Contract\Storage;
use Illuminate\Support\Facades\Config;

/**
 * Firebase Service
 * 
 * Service class để quản lý kết nối và các operations với Firebase
 */
class FirebaseService
{
    protected ?Factory $factory = null;
    protected ?Auth $auth = null;
    protected ?Firestore $firestore = null;
    protected ?Storage $storage = null;

    /**
     * Khởi tạo Firebase Factory
     */
    protected function getFactory(): Factory
    {
        if ($this->factory === null) {
            $serviceAccount = Config::get('firebase.service_account');
            
            if ($serviceAccount) {
                // Nếu có service account file
                if (file_exists($serviceAccount)) {
                    $this->factory = (new Factory)->withServiceAccount($serviceAccount);
                } else {
                    // Nếu là JSON string
                    $this->factory = (new Factory)->withServiceAccount(json_decode($serviceAccount, true));
                }
            } else {
                // Khởi tạo với credentials từ config
                $config = Config::get('firebase.config');
                $this->factory = (new Factory)
                    ->withProjectId($config['projectId']);
            }
        }

        return $this->factory;
    }

    /**
     * Lấy Firebase Auth instance
     */
    public function getAuth(): Auth
    {
        if ($this->auth === null) {
            $this->auth = $this->getFactory()->createAuth();
        }

        return $this->auth;
    }

    /**
     * Lấy Firebase Firestore instance
     */
    public function getFirestore(): Firestore
    {
        if ($this->firestore === null) {
            $this->firestore = $this->getFactory()->createFirestore();
        }

        return $this->firestore;
    }

    /**
     * Lấy Firebase Storage instance
     */
    public function getStorage(): Storage
    {
        if ($this->storage === null) {
            $this->storage = $this->getFactory()->createStorage();
        }

        return $this->storage;
    }

    /**
     * Lấy config Firebase để sử dụng trong frontend
     * 
     * @return array
     */
    public function getConfig(): array
    {
        return Config::get('firebase.config');
    }

    /**
     * Xác thực Firebase ID Token
     * 
     * @param string $idToken
     * @return \Kreait\Firebase\Auth\Token|null
     */
    public function verifyIdToken(string $idToken)
    {
        try {
            $auth = $this->getAuth();
            return $auth->verifyIdToken($idToken);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Lấy thông tin user từ Firebase UID
     * 
     * @param string $uid
     * @return \Kreait\Firebase\Auth\UserRecord|null
     */
    public function getUser(string $uid)
    {
        try {
            $auth = $this->getAuth();
            return $auth->getUser($uid);
        } catch (\Exception $e) {
            return null;
        }
    }
}