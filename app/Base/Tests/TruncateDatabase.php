<?php

namespace App\Base\Tests;

use App\Base\Exceptions\DatabaseConnectionException;
use Exception;
use PDO;

trait TruncateDatabase
{
    protected PDO $connection;

    /**
     * Truncate all database tables after each test
     */
    protected function truncateTables(): void
    {
        $this->initConnection();

        $tables = $this->getTables();

        foreach ($tables as $table) {
            $currentPdo =$this->connection->prepare("TRUNCATE TABLE {$table}");
            $currentPdo->execute();
        }
    }

    protected function initConnection()
    {
        $host =$this->app->config()->DATABASE_HOST;
        $db = $this->app->config()->DATABASE_NAME;
        $port = $this->app->config()->DATABASE_PORT;
        $user = $this->app->config()->DATABASE_USER;
        $pass = $this->app->config()->DATABASE_PASSWORD;

        try {
            $this->connection = new PDO("mysql:host={$host}:{$port};dbname={$db}", $user, $pass);
        }
        catch(Exception $e) {
            throw new DatabaseConnectionException($e->getMessage(), $e->getCode());
        }
    }

    protected function getTables()
    {
        $pdoStatement = $this->connection->prepare('SHOW TABLES');
        $pdoStatement->execute();

        return $pdoStatement->fetchAll(PDO::FETCH_COLUMN);
    }
}
