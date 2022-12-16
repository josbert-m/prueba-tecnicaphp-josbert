<?php

namespace App\Base;

class Kernel extends Singleton
{
    /**
     * Returns a application instance
     * 
     * @return Kernel
     */
    public static function appFactory(): Kernel
    {
        $className = static::class;

        if (!isset(self::$singletons[$className])) {
            static::boot($className);
        }

        return self::$singletons[$className];
    }

    /**
     * Boot a kernel instance
     * 
     * @param string $current
     * @return void
     */
    protected static function boot(string $current): void
    {
        Config::configFactory();
        self::$singletons[$current] = new static();
    }

    /**
     * Access to config service
     * 
     * @return Config
     */
    public function config(): Config
    {
        return Config::configFactory();
    }
}
