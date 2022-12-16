<?php

namespace App\Base\Database\Traits;

trait InsertStatement
{
    protected array $inserts = [];

    /**
     * Add insert attribute to query
     * 
     * @param string $column
     * @param mixed $value
     */
    protected function addInsert(string $column, $value)
    {
        $this->inserts[$column] = strval($value);
    }

    /**
     * Add a full insert statement to query
     * 
     * @return string
     */
    protected function getInsertStatement()
    {
        $keys = array_keys($this->inserts);
        $values = array_values($this->inserts);

        return "INSERT INTO {$this->model->table} (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ")";
    }
}
