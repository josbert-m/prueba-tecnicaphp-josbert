<?php

use App\Base\Kernel;

require_once __DIR__ . '/../vendor/autoload.php';

if (!function_exists('app')) {

    function app(): Kernel 
    {
        return Kernel::appFactory();
    }

}
