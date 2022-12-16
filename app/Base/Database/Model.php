<?php

namespace App\Base\Database;

use Carbon\Carbon;
use ReflectionClass;

class Model
{
    public string $table;

    protected array $hidden = [];

    public Carbon $createdAt;

    public Carbon $updatedAt;

    public function __construct()
    {
        $this->table = $this->getTableName();
    }

    /**
     * Get a database table name
     * 
     * @return string
     */
    protected function getTableName(): string
    {
        $class = new ReflectionClass($this);
        $name = $class->getShortName() . (preg_match("/(ss|x|ch|sh|y)$/i", $class->getShortName()) ? 'es' : 's');

        return strtolower($name);
    }
}
