<?php

namespace App\Helpers;

class Crypto
{
    private static $algo = 'sha256';

    /**
     * Get the app secure key
     * 
     * @return string
     */
    private static function getKey(): string 
    {
        return app()->config()->APP_KEY;
    }

    public static function bcrypt($value): string 
    {
        $bin = hash_hmac(static::$algo, $value, static::getKey(), true);

        return base64_encode($bin);
    }
}
