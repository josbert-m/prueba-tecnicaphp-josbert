<?php

namespace App\Base;

class Singleton 
{
    /**
     * Array of singletons instances
     * 
     * @var mixed[]
     */
    protected static $singletons = [];

    /**
     * set magic method as unreachable
     * 
     * @return void
     */
    protected function __construct() {  }

    /**
     * set magic method as unreachable
     * 
     * @return void
     */
    protected function __clone() {  }

    /**
     * Boot a self singleton
     * 
     * @param string $current
     * @return void
     */
    protected static function boot(string $current): void {  }
}
