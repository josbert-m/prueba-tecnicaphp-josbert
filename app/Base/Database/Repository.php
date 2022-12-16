<?php

namespace App\Base\Database;

use App\Base\Database\Traits\QueryBuilder;
use App\Base\Exceptions\DatabaseConnectionException;
use Carbon\Carbon;
use Exception;
use PDO;
use ReflectionClass;

class Repository
{
    use QueryBuilder;

    protected PDO $connection;

    protected $model;

    protected $protectedProps = [
        'hidden', 'table'
    ];

    public function __construct(string $class)
    {
        $this->initConnection();

        $reflection = new ReflectionClass($class);
        $this->model = $reflection->newInstance();
    }

    /**
     * Set a new connection with the database
     * 
     * @return void
     */
    protected function initConnection(): void
    {
        $host = app()->config()->DATABASE_HOST;
        $db = app()->config()->DATABASE_NAME;
        $port = app()->config()->DATABASE_PORT;
        $user = app()->config()->DATABASE_USER;
        $pass = app()->config()->DATABASE_PASSWORD;

        try {
            $this->connection = new PDO("mysql:host={$host}:{$port};dbname={$db}", $user, $pass);
        }
        catch(Exception $e) {
            throw new DatabaseConnectionException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get attributes as Model instance
     * 
     * @param array $attributes
     * @return Model
     */
    protected function fetchModel(array $attributes): Model
    {
        $reflectionClass = new ReflectionClass($this->model);
        $class = $reflectionClass->newInstance();

        foreach ($attributes as $key => $value) {
            $reflectionProperty = $reflectionClass->getProperty($key);
            $typed = $reflectionProperty->getType()->getName();

            $mutation = $typed === 'integer' ? intval($value) : ($typed === Carbon::class ? Carbon::parse($value) : $value);

            $class->{$key} = $mutation;
        }

        return $class;
    }

    /**
     * Get all rows as array Model instance
     * 
     * @param array $rows
     * @return Model[]
     */
    protected function fetchAllModel(array $rows)
    {
        return array_map(function($item) {
            return $this->fetchModel($item);
        }, $rows);
    }
}
