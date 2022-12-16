<?php

namespace App\Base\Database\Traits;

use App\Base\Database\Model;
use App\Base\Exceptions\DatabaseConnectionException;
use App\Base\Exceptions\SQLSyntaxErrorException;
use Exception;
use PDO;

trait DeleteStatement
{
    /**
     * Delete one model from database
     * 
     * @param Model $model
     * @return void
     */
    public static function remove(Model &$model)
    {
        $host = app()->config()->DATABASE_HOST;
        $db = app()->config()->DATABASE_NAME;
        $port = app()->config()->DATABASE_PORT;
        $user = app()->config()->DATABASE_USER;
        $pass = app()->config()->DATABASE_PASSWORD;

        try {
            $connection = new PDO("mysql:host={$host}:{$port};dbname={$db}", $user, $pass);
        }
        catch(Exception $e) {
            throw new DatabaseConnectionException($e->getMessage(), $e->getCode());
        }

        $query = "DELETE FROM {$model->table} WHERE id = :id";
        $pdoStatement = $connection->prepare($query);

        if (!$pdoStatement->execute([':id' => $model->id])) {
            throw new SQLSyntaxErrorException($pdoStatement->errorInfo()[2], $pdoStatement->errorInfo()[0]);
        }

        unset($model->id);
    }

    /**
     * Add a full delete statement to query
     * 
     * @return string
     */
    protected function getDeleteStatement()
    {
        return "DELETE FROM {$this->model->table}";
    }
}
