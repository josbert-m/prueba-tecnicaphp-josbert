<?php 

namespace App\Base\Database\Traits;

use App\Base\Database\Model;
use App\Base\Exceptions\BadOrderBySortException;
use App\Base\Exceptions\BadStatementException;
use App\Base\Exceptions\NotFoundRecordException;
use App\Base\Exceptions\SQLSyntaxErrorException;
use Carbon\Carbon;
use PDO;
use ReflectionClass;

/**
 * @property \PDO $connection
 */
trait QueryBuilder
{
    use SelectStatement, UpdateStatement, InsertStatement, DeleteStatement;

    protected array $query = [];

    protected array $params = [];

    protected string $statement = 'SELECT';

    protected string $orderClause = '';

    /**
     * Add WHERE clause to query
     * 
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return $this
     */
    public function where(string $column, string $operator = '=', $value = null)
    {
        $rand = random_int(0, 999999);

        if (is_null($value)) {
            array_push($this->query, "{$column} = :{$column}{$rand}");
            $this->params[":{$column}{$rand}"] = strval($operator);
        }
        else {
            array_push($this->query, "{$column} {$operator} :{$column}{$rand}");
            $this->params[":{$column}{$rand}"] = strval($value);
        }

        return $this;
    }

    /**
     * Add WHERE IN clause to query
     * 
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function whereIn(string $column, array $values)
    {
        $in = [];

        foreach ($values as $val) {
            $rand = random_int(0, 999999);

            $this->params[":{$column}{$rand}"] = $val;
            array_push($in, ":{$column}{$rand}");
        }

        array_push($this->query, "{$column} IN (" . implode(', ', $in) . ")");

        return $this;
    }

    /**
     * Add ORDER BY clause to query
     * 
     * @param string $column
     * @param string $order
     * @return $this
     */
    public function orderBy(string $column, $order = 'ASC')
    {
        if (!in_array($order, ['ASC', 'asc', 'DESC', 'desc'])) {
            throw new BadOrderBySortException();
        }

        $this->orderClause = "ORDER BY {$column} {$order}";

        return $this;
    }

    /**
     * Get completed query as SQL string
     * 
     * @return string
     */
    public function toSql(): string
    {
        $outputStatement = $this->statement === 'SELECT' ? 
            $this->getSelectStatement() : (
                $this->statement === 'UPDATE' ? 
                $this->getUpdateStatement() : (
                    $this->statement === 'INSERT' ? $this->getInsertStatement() : (
                        $this->statement === "DELETE" ? 
                        $this->getDeleteStatement() : ''
                    )
                )
            );

        $outputWhere = count($this->query) > 0 ? 'WHERE ' . implode(' AND ', $this->query): '';

        return preg_replace("/(\s+$|^\s+)/m", '',"{$outputStatement} {$outputWhere} {$this->orderClause}");
    }

    /**
     * Execute query and returns one row
     * 
     * @return Model|null
     */
    public function getOne()
    {
        $this->setStatement('SELECT');

        $fullQuery = $this->toSql();
        $fullQuery .= ' LIMIT 1';

        $pdoStatement = $this->connection->prepare($fullQuery);

        if (!$pdoStatement->execute($this->params)) {
            throw new SQLSyntaxErrorException($pdoStatement->errorInfo()[2], $pdoStatement->errorInfo()[0]);
        }

        $attributes = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        $this->resetParams();

        return is_array($attributes) ? $this->fetchModel($attributes) : null;
    }

    /**
     * Execute query and returns one row or thown a Exception
     * 
     * @return Model|NotFoundRecordException
     */
    public function getOneOrFail()
    {
        $result = $this->getOne();

        if (is_null($result)) {
            throw new NotFoundRecordException();
        }

        return $result;
    }

    /**
     * Execute query and returns all rows
     * 
     * @param int $take
     * @return Model[]
     */
    public function getMany(int $take = -1)
    {
        $this->setStatement('SELECT');

        $fullQuery = $this->toSql();

        if ($take > -1) {
            $fullQuery .= " LIMIT {$take}";
        }

        $pdoStatement = $this->connection->prepare($fullQuery);

        if (!$pdoStatement->execute($this->params)) {
            throw new SQLSyntaxErrorException($pdoStatement->errorInfo()[2], $pdoStatement->errorInfo()[0]);
        }

        $rows = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);

        return $this->fetchAllModel($rows);
    }

    /**
     * Execute query and returns all rows
     * 
     * @param int $take
     * @return Model[]|NotFoundRecordException
     */
    public function getManyOrFail(int $take = -1)
    {
        $result = $this->getMany($take);

        if (count($result) === 0) {
            throw new NotFoundRecordException();
        }

        return $result;
    }

    /**
     * Insert new record
     * 
     * @param Model $model
     * @return Model
     */
    public function save(Model &$model)
    {
        $this->setStatement('INSERT');

        $reflection = new ReflectionClass($model);
        $properties = $reflection->getProperties();
        $rand = random_int(0, 999999);

        foreach ($properties as $property) {
            if (!in_array($property->name, $this->protectedProps) && $property->isInitialized($model)) {
                $this->addInsert($property->name, ":{$property->name}{$rand}");

                $this->params[":{$property->name}{$rand}"] = $model->{$property->name};
            }
        }

        $fullQuery = $this->toSql();

        $pdoStatement = $this->connection->prepare($fullQuery);

        if (!$pdoStatement->execute($this->params)) {
            throw new SQLSyntaxErrorException($pdoStatement->errorInfo()[2], $pdoStatement->errorInfo()[0]);
        }

        $model->id = $this->connection->lastInsertId();
        $model->createdAt = Carbon::now();
        $model->updatedAt = Carbon::now();

        $this->resetParams();

        return $model;
    }

    /**
     * Execute DELETE query statement
     * 
     * @return void
     */
    public function delete()
    {
        $this->setStatement('DELETE');

        $fullQuery = $this->toSql();

        $pdoStatement = $this->connection->prepare($fullQuery);

        if (!$pdoStatement->execute($this->params)) {
            throw new SQLSyntaxErrorException($pdoStatement->errorInfo()[2], $pdoStatement->errorInfo()[0]);
        }

        $this->resetParams();
    }

    /**
     * Execute UPDATE query statement
     * 
     * @param array $attributes
     * @return void
     */
    public function update(array $attributes)
    {
        $this->setStatement('UPDATE');

        foreach ($attributes as $column => $value) {
            $this->addUpdate($column, $value);
        }

        $fullQuery = $this->toSql();

        $pdoStatement = $this->connection->prepare($fullQuery);

        if (!$pdoStatement->execute($this->params)) {
            throw new SQLSyntaxErrorException($pdoStatement->errorInfo()[2], $pdoStatement->errorInfo()[0]);
        }

        $this->resetParams();
    }

    /**
     * Set the statement type to execute
     * 
     * @param string $statement
     */
    protected function setStatement(string $statement) 
    {
        if (in_array($statement, ['update', 'UPDATE', 'select', 'SELECT', 'DELETE', 'delete', 'insert', 'INSERT'])) {
            $this->statement = strtoupper($statement);
        }
        else {
            throw new BadStatementException();
        }
    }

    /**
     * Reset params properties
     * 
     * @return void
     */
    protected function resetParams()
    {
        $this->params = [];
        $this->query = [];
        $this->inserts = [];
        $this->updates = [];
        $this->orderClause = '';
    }
}
