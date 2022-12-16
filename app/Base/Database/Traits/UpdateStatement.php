<?php

namespace App\Base\Database\Traits;

trait UpdateStatement
{
    protected array $updates = [];

    /**
     * Add column to update
     * 
     * @param strign $column
     * @param mixed $value
     */
    protected function addUpdate(string $column, $value)
    {
        $this->updates[$column] = strval($value);
    }

    /**
     * Add a full update statement to query
     */
    protected function getUpdateStatement(): string
    {
        $setters = [];

        foreach ($this->updates as $key => $value) {
            $rand = random_int(0, 999999);

            array_push($setters, "{$key} = :{$key}{$rand}");
            $this->params[":{$key}{$rand}"] = strval($value);
        }

        return "UPDATE {$this->model->table} SET " . implode(', ', $setters);
    }
}
