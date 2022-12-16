<?php 

namespace App\Base\Database\Traits;

trait SelectStatement 
{
    protected array $selecteds = [];

    /**
     * Add SELECT columns to query
     * 
     * @return $this
     */
    public function select(...$columns)
    {
        $this->selecteds = $columns;

        return $this;
    }
    
    /**
     * Add a full select statement to query
     * 
     * @return string
     */
    protected function getSelectStatement()
    {
        return count($this->selecteds) === 0 ? "SELECT * FROM {$this->model->table}" : 'SELECT ' . implode(', ', $this->selecteds) . "FROM {$this->model->table}";
    }
}
