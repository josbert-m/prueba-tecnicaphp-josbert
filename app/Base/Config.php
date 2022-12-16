<?php

namespace App\Base;

use Dotenv\Dotenv;

class Config extends Singleton
{
    /**
     * Returns a instance of config service
     */
    public static function configFactory(): Config 
    {
        $className = static::class;

        if (!isset(self::$singletons[$className])) {
            static::boot($className);
        }

        return self::$singletons[$className];
    }

    /**
     * Boot a config service instance
     * 
     * @param string $current
     * @return void
     */
    protected static function boot(string $current): void
    {
        $dir = __DIR__ . '/../..';

        $config = Dotenv::createImmutable($dir);
        $config->load();

        self::$singletons[$current] = new static();
    }

    /**
     * Overload properties for access to config variables
     * 
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $_ENV[$name] ?? null;
    }

    /**
     * Isset overload properties
     * 
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($_ENV[$name]);
    }
}
