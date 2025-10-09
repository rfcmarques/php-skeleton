<?php

namespace Core;

use PDO;
use PDOStatement;
use PDOException;
use Exception;

class Database
{
    public $conn;

    protected $table;
    protected $fields = [];
    protected $conditions = [];
    protected $limit;
    protected $orderBy;
    protected $bindings = [];

    /**
     * Constructor for Database class
     * 
     * @param array $config
     */
    public function __construct(array $config)
    {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";


        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];

        try {
            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: {$e->getMessage()}");
        }
    }

    /**
     * Query the database
     * 
     * @param string $query
     * @param array $params
     * 
     * @return PDOStatement
     * @throws PDOException
     */
    public function query(string $query, array $params = []): PDOStatement
    {
        try {
            $sth = $this->conn->prepare($query);

            foreach ($params as $param => $value) {
                $sth->bindValue(':' . $param, $value);
            }

            $sth->execute();
            return $sth;
        } catch (PDOException $e) {
            throw new Exception("Query failed to execute: {$e->getMessage()}");
        }
    }

    /**
     * Define the table to query
     * 
     */
    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Define the fields to select
     * @param string ...$fields
     */
    public function select(string ...$fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Define the conditions for the query
     * 
     * @param string $field
     * @param string $operator
     * @param string $value
     * 
     * @return self
     */
    public function where(string $field, string $operator, $value): self
    {
        return $this->addCondition('AND', $field, $operator, $value);
    }

    /**
     * Define the OR conditions for the query
     * 
     * @param string $field
     * @param string $operator
     * @param string $value
     */
    public function orWhere(string $field, string $operator, $value): self
    {
        return $this->addCondition('OR', $field, $operator, $value);
    }

    /**
     * Define the IN conditions for the query
     * 
     * @param string $field
     * @param array $values
     */
    public function whereIn(string $field, array $values): self
    {
        return $this->addCondition('AND', $field, 'IN', $values);
    }

    /**
     * Define the OR IN conditions for the query
     * 
     * @param string $field
     * @param array $values
     */
    public function orWhereIn(string $field, array $values): self
    {
        return $this->addCondition('OR', $field, 'IN', $values);
    }

    /**
     * Define the NOT IN conditions for the query
     * 
     * @param string $field
     * @param array $values
     */
    public function whereNotIn(string $field, array $values): self
    {
        return $this->addCondition('AND', $field, 'NOT IN', $values);
    }

    /**
     * Define the OR NOT IN conditions for the query
     * 
     * @param string $field
     * @param array $values
     */
    public function orWhereNotIn(string $field, array $values): self
    {
        return $this->addCondition('OR', $field, 'NOT IN', $values);
    }

    /**
     * Define the NULL conditions for the query
     * 
     * @param string $field
     */
    public function whereNull(string $field): self
    {
        return $this->addCondition('AND', $field, 'IS', null);
    }

    /**
     * Define the OR NULL conditions for the query
     * 
     * @param string $field
     */
    public function orWhereNull(string $field): self
    {
        return $this->addCondition('OR', $field, 'IS', null);
    }

    /**
     * Define the NOT NULL conditions for the query
     * 
     * @param string $field
     */
    public function whereNotNull(string $field): self
    {
        return $this->addCondition('AND', $field, 'IS NOT', null);
    }

    /**
     * Define the OR NOT NULL conditions for the query
     * 
     * @param string $field
     */
    public function orWhereNotNull(string $field): self
    {
        return $this->addCondition('OR', $field, 'IS NOT', null);
    }

    /**
     * Define the BETWEEN conditions for the query
     * 
     * @param string $field
     * @param mixed $value1
     * @param mixed $value2
     */
    public function whereBetween(string $field, $value1, $value2): self
    {
        return $this->addCondition('AND', $field, 'BETWEEN', [$value1, $value2]);
    }

    /**
     * Define the OR BETWEEN conditions for the query
     * 
     * @param string $field
     * @param mixed $value1
     * @param mixed $value2
     */
    public function orWhereBetween(string $field, $value1, $value2): self
    {
        return $this->addCondition('OR', $field, 'BETWEEN', [$value1, $value2]);
    }

    /**
     * Define the NOT BETWEEN conditions for the query
     * 
     * @param string $field
     * @param mixed $value1
     * @param mixed $value2
     */
    public function whereNotBetween(string $field, $value1, $value2): self
    {
        return $this->addCondition('AND', $field, 'NOT BETWEEN', [$value1, $value2]);
    }

    /**
     * Define the OR NOT BETWEEN conditions for the query
     * 
     * @param string $field
     * @param mixed $value1
     * @param mixed $value2
     */
    public function orWhereNotBetween(string $field, $value1, $value2): self
    {
        return $this->addCondition('OR', $field, 'NOT BETWEEN', [$value1, $value2]);
    }

    /**
     * Define the LIKE conditions for the query
     * 
     * @param string $field
     * @param string $value
     */
    public function whereLike(string $field, string $value): self
    {
        return $this->addCondition('AND', $field, 'LIKE', $value);
    }

    /**
     * Define the OR LIKE conditions for the query
     * 
     * @param string $field
     * @param string $value
     */
    public function orWhereLike(string $field, string $value): self
    {
        return $this->addCondition('OR', $field, 'LIKE', $value);
    }

    /**
     * Define the NOT LIKE conditions for the query
     * 
     * @param string $field
     * @param string $value
     */
    public function whereNotLike(string $field, string $value): self
    {
        return $this->addCondition('AND', $field, 'NOT LIKE', $value);
    }

    /**
     * Define the OR NOT LIKE conditions for the query
     * 
     * @param string $field
     * @param string $value
     */
    public function orWhereNotLike(string $field, string $value): self
    {
        return $this->addCondition('OR', $field, 'NOT LIKE', $value);
    }

    /**
     * Define the order by clause
     * 
     * @param string $field
     * @param string $direction
     */
    public function orderBy(string $field, string $direction = 'ASC'): self
    {
        $this->orderBy = compact('field', 'direction');
        return $this;
    }

    /**
     * Define the limit clause
     * 
     * @param int $limit
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Get the results of the query
     * 
     * @return array
     */
    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        $results = $this->execute($sql)->fetchAll(PDO::FETCH_OBJ) ?? [];
        $this->reset();
        return $results;
    }

    /**
     * Get the first result of the query
     * 
     * @return array
     */
    public function first(): ?object
    {
        $sql = $this->buildSelectQuery(true);
        $fetch = $this->execute($sql)->fetch(PDO::FETCH_OBJ);
        $this->reset();
        return $fetch ? $fetch : null;
    }

    /**
     * Count the number of rows in the query
     * 
     * @return int
     */
    public function count(): int
    {
        $sql = $this->buildCountQuery();
        $row = $this->execute($sql)->fetch(PDO::FETCH_ASSOC) ?: ['cnt' => 0];
        $this->reset();
        return (int) $row['cnt'];
    }

    /**
     * Get the current SQL query
     * 
     * @return string
     */
    public function toSql(): string
    {
        return $this->buildSelectQuery();
    }

    protected function buildCountQuery(): string
    {
        $sql = "SELECT COUNT(*) AS cnt FROM {$this->table}";
        if (!empty($this->conditions)) {
            $clauses = [];
            foreach ($this->conditions as $index => $cond) {
                $prefix = $index === 0 ? '' : ' ' . $cond['type'] . ' ';
                $clauses[] = $prefix . $cond['clause'];
            }
            $sql .= " WHERE " . implode('', $clauses);
        }
        return $sql;
    }

    /**
     * Build the select query
     * 
     * @param bool $single
     * @return string
     */
    protected function buildSelectQuery(bool $single = false): string
    {
        $fields = empty($this->fields) ? ['*'] : $this->fields;
        $sql = "SELECT " . implode(", ", $fields) . " FROM {$this->table}";

        if (!empty($this->conditions)) {
            $clauses = [];

            foreach ($this->conditions as $index => $condition) {
                $prefix = $index === 0 ? '' : " {$condition['type']} ";
                $clauses[] = $prefix . $condition['clause'];
            }

            $sql .= " WHERE " . implode("", $clauses);
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY {$this->orderBy['field']} {$this->orderBy['direction']}";
        }

        if ($single) {
            return $sql .= " LIMIT 1";
        }

        return $sql .= !empty($this->limit) ? " LIMIT {$this->limit}" : '';
    }

    /**
     * Add a condition to the query
     * 
     * @param string $type
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * 
     * @return self
     */
    protected function addCondition(string $type, string $field, string $operator, $value = null): self
    {
        $clause   = '';
        $bindings = [];

        switch (strtoupper($operator)) {
            case 'IN':
            case 'NOT IN':
                if (!is_array($value) || $value === []) {
                    throw new \InvalidArgumentException("Value for {$operator} must be a non-empty array.");
                }
                $phs = [];
                foreach (array_values($value) as $i => $val) {
                    $ph = ":{$field}_{$i}";
                    $phs[] = $ph;
                    $bindings[$ph] = $val;
                }
                $clause = "{$field} {$operator} (" . implode(', ', $phs) . ")";
                break;

            case 'BETWEEN':
            case 'NOT BETWEEN':
                if (!is_array($value) || count($value) !== 2) {
                    throw new \InvalidArgumentException("Value for {$operator} must be [from, to].");
                }
                [$v1, $v2] = array_values($value);
                $ph1 = ":{$field}_from";
                $ph2 = ":{$field}_to";
                $bindings[$ph1] = $v1;
                $bindings[$ph2] = $v2;
                $clause = "{$field} {$operator} {$ph1} AND {$ph2}";
                break;

            case 'IS':
            case 'IS NOT':
                $clause = "{$field} {$operator} NULL";
                break;

            default:
                $ph = ":{$field}";
                $bindings[$ph] = $value;
                $clause = "{$field} {$operator} {$ph}";
        }

        $this->conditions[] = ['type' => $type, 'clause' => $clause];

        $this->bindings = array_replace($this->bindings, $bindings);

        return $this;
    }


    /**
     * Get the results of the query
     * 
     * @param string $sql
     * 
     * @return PDOStatement
     */
    protected function execute(string $sql): PDOStatement
    {
        $query = $this->conn->prepare($sql);

        foreach ($this->bindings as $param => $value) {
            $query->bindValue($param, $value);
        }

        $query->execute();
        return $query;
    }

    /**
     * Reset the query builder
     * 
     */
    protected function reset(): void
    {
        $this->table = null;
        $this->fields = [];
        $this->conditions = [];
        $this->limit = null;
        $this->orderBy = null;
        $this->bindings = [];
    }
}
