<?php
namespace Model;

use PDO;
use PDOException;
use PDOStatement;

/**
 * BaseModel - Core database abstraction layer
 *
 * This class provides a fluent query builder interface for database operations.
 * It implements a simple active record pattern and supports method chaining
 * for building complex SQL queries in an object-oriented way.
 *
 * Features:
 * - Singleton PDO connection management
 * - Fluent query builder API
 * - Support for joins, where clauses, grouping, ordering, and pagination
 * - Basic CRUD operations (Create, Read, Update, Delete)
 * - SQL injection protection via prepared statements
 * - Transaction support
 *
 * @package Model
 */
class BaseModel
{
    /* ───── DB connection (singleton) ───── */
    /**
     * @var PDO Database connection instance
     */
    protected PDO $db;
    
    /**
     * @var ?PDO Singleton PDO instance
     */
    private static ?PDO $pdo = null;

    /**
     * Establishes a database connection (singleton pattern)
     *
     * @return ?PDO Database connection or null on failure
     */
    private static function connect(): ?PDO
    {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
                    DB_USER,
                    DB_PASSWORD,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                error_log('DB connection failed: ' . $e->getMessage());
                return null;
            }
        }
        return self::$pdo;
    }

    /**
     * Constructor - initializes the database connection
     * 
     * @throws \Exception If database connection fails
     */
    public function __construct()
    {
        $this->db = self::connect() ?? die('Database connection error.');
    }

    /* ───── Query-builder state ───── */
    /**
     * @var string Base SQL query (SELECT/INSERT/UPDATE/DELETE)
     */
    private string $queryBase = '';
    
    /**
     * @var string WHERE clause of the SQL query
     */
    private string $whereClause = '';
    
    /**
     * @var array Parameters for the WHERE clause
     */
    private array $whereParams = [];
    
    /**
     * @var string GROUP BY clause of the SQL query
     */
    private string $groupByClause = '';
    
    /**
     * @var string HAVING clause of the SQL query
     */
    private string $havingClause = '';
    
    /**
     * @var string ORDER BY clause of the SQL query
     */
    private string $orderByClause = '';
    
    /**
     * @var ?int Maximum number of rows to return
     */
    private ?int $limit = null;
    
    /**
     * @var int Number of rows to skip
     */
    private int $offset = 0;
    
    /**
     * @var array Parameters for INSERT/UPDATE operations
     */
    private array $params = [];
    
    /**
     * @var string Current table name
     */
    private string $table = '';

    /* ───── Core builder methods ───── */
    /**
     * Sets the table to query
     *
     * @param string $table Table name
     * @return self For method chaining
     */
    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Begins a SELECT query
     *
     * @param string|array $columns Columns to select (string or array of column names)
     * @return self For method chaining
     */
    public function select($columns = '*'): self
    {
        $cols = is_array($columns) ? implode(', ', $columns) : $columns;
        $this->queryBase = "SELECT $cols FROM {$this->table}";
        return $this;
    }

    /**
     * Adds a JOIN clause to the query
     *
     * @param string $table Table to join
     * @param string $first First column for join condition
     * @param string $operator Comparison operator (=, <, >, etc.)
     * @param string $second Second column for join condition
     * @return self For method chaining
     */
    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->queryBase .= " JOIN $table ON $first $operator $second";
        return $this;
    }

    /**
     * Adds a WHERE condition to the query (AND logic)
     *
     * @param string $column Column name
     * @param string $operator Comparison operator (=, <, >, LIKE, etc.)
     * @param mixed $value Value to compare against
     * @return self For method chaining
     */
    public function where(string $column, string $operator, $value): self
    {
        $param = preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
        $condition = "$column $operator :$param";

        $this->whereClause
            ? $this->whereClause .= " AND $condition"
            : $this->whereClause = " WHERE $condition";

        $this->whereParams[$param] = $value;
        return $this;
    }

    /**
     * Adds a group of OR conditions to the query
     *
     * @param callable $callback Function that builds the OR conditions
     * @return self For method chaining
     * @example
     * $model->whereOr(function($or) {
     *     $or->where('column1', '=', 'value1')
     *        ->where('column2', '=', 'value2');
     * });
     * // Produces: WHERE (column1 = 'value1' OR column2 = 'value2')
     */
    public function whereOr(callable $callback): self
    {
        $orBuilder = new OrConditionBuilder();
        $callback($orBuilder);                 // user builds OR group

        $conds = $orBuilder->getConditions();
        if ($conds) {
            $orClause = '(' . implode(' OR ', $conds) . ')';
            $this->whereClause
                ? $this->whereClause .= " AND $orClause"
                : $this->whereClause = " WHERE $orClause";

            $this->whereParams = array_merge(
                $this->whereParams,
                $orBuilder->getParameters()
            );
        }
        return $this;
    }

    /**
     * Adds a WHERE IN condition to the query
     *
     * @param string $column Column name
     * @param array $values Array of values to match against
     * @return self For method chaining
     * @throws \InvalidArgumentException If values array is empty
     * @example
     * $model->whereIn('id', [1, 2, 3]);
     * // Produces: WHERE id IN (1, 2, 3)
     */
    public function whereIn(string $column, array $values): self
    {
        if (!$values) {
            throw new \InvalidArgumentException('whereIn() needs at least one value');
        }

        // make one placeholder per value  (:col_0, :col_1 …)
        $ph = [];
        $counter = 0;
        foreach ($values as $v) {
            $name = preg_replace('/[^a-zA-Z0-9_]/', '_', $column) . '_' . $counter++;
            $ph[] = ":$name";
            $this->whereParams[$name] = $v;
        }

        $condition = "$column IN (" . implode(', ', $ph) . ')';
        $this->whereClause
            ? $this->whereClause .= " AND $condition"
            : $this->whereClause = " WHERE $condition";

        return $this;
    }


    /**
     * Adds a GROUP BY clause to the query
     *
     * @param string $column Column to group by
     * @return self For method chaining
     */
    public function groupBy(string $column): self
    {
        $this->groupByClause
            ? $this->groupByClause .= ", $column"
            : $this->groupByClause = " GROUP BY $column";
        return $this;
    }

    /**
     * Adds a HAVING clause to the query
     *
     * @param string $condition HAVING condition
     * @return self For method chaining
     */
    public function having(string $condition): self
    {
        $this->havingClause = " HAVING $condition";
        return $this;
    }

    /**
     * Adds an ORDER BY clause to the query
     *
     * @param string $column Column to order by
     * @param string $direction Sort direction (ASC or DESC)
     * @return self For method chaining
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orderByClause
            ? $this->orderByClause .= ", $column $direction"
            : $this->orderByClause = " ORDER BY $column $direction";
        return $this;
    }

    /**
     * Sets LIMIT and OFFSET for pagination
     *
     * @param int $limit Maximum number of rows to return
     * @param int $offset Number of rows to skip
     * @return self For method chaining
     */
    public function limit(int $limit, int $offset = 0): self
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    /* ───── CRUD helpers ───── */
    /**
     * Executes the query and returns all matching rows
     *
     * @param int $mode PDO fetch mode
     * @return array Array of matching rows
     */
    public function get(int $mode = PDO::FETCH_ASSOC): array
    {
        return $this->execute()->fetchAll($mode);
    }

    /**
     * Executes the query and returns the first matching row
     *
     * @param int $mode PDO fetch mode
     * @return array|false First matching row or false if none found
     */
    public function first(int $mode = PDO::FETCH_ASSOC)
    {
        return $this->execute()->fetch($mode);
    }

    /**
     * Checks if any rows match the query
     *
     * @return bool True if at least one row matches
     */
    public function exists(): bool
    {
        return $this->execute()->rowCount() > 0;
    }

    /**
     * Inserts a new row into the table
     *
     * @param array $data Associative array of column => value pairs
     * @return PDOStatement The executed statement
     */
    public function insert(array $data): PDOStatement
    {
        $cols = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $this->queryBase = "INSERT INTO {$this->table} ($cols) VALUES ($placeholders)";
        $this->params = $data;
        return $this->execute();
    }

    /**
     * Updates rows in the table
     *
     * @param array $data Associative array of column => value pairs to update
     * @return PDOStatement The executed statement
     * @throws \InvalidArgumentException If data array is empty
     */
    public function update(array $data): PDOStatement
    {
        if (!$data) {
            throw new \InvalidArgumentException('Update data cannot be empty');
        }
        foreach ($data as $col => $val) {
            $name = "upd_$col";
            $sets[] = "$col = :$name";
            $this->params[$name] = $val;
        }
        $this->queryBase = "UPDATE {$this->table} SET " . implode(', ', $sets ?? []);
        return $this->execute();
    }


    /**
     * Deletes rows from the table
     *
     * @return PDOStatement The executed statement
     * @throws \RuntimeException If no WHERE clause is specified (safety feature)
     */
    public function delete(): PDOStatement
    {
        if ($this->whereClause === '') {
            throw new \RuntimeException('Refusing to DELETE without a WHERE clause');
        }
        $this->queryBase = "DELETE FROM {$this->table}";
        return $this->execute();
    }

    /**
     * Executes a raw SQL query with parameters
     *
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @return PDOStatement The executed statement
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }


    /**
     * Calculates the sum of a column
     *
     * @param string $column Column to sum
     * @param string $alias Alias for the sum column in the result
     * @return float Sum of the column values
     */
    public function sum(string $column, string $alias = 'sum'): float
    {
        $result = $this->select("SUM($column) AS $alias")->first();
        return (float) ($result[$alias] ?? 0);
    }

    /**
     * Counts rows matching the query
     *
     * @param string $column Column to count (defaults to '*')
     * @param string $alias Alias for the count column in the result
     * @return int Number of matching rows
     */
    public function count(string $column = '*', string $alias = 'count'): int
    {
        $result = $this->select("COUNT($column) AS $alias")->first();
        return (int) ($result[$alias] ?? 0);
    }

    /* ───── Build & execute ───── */
    /**
     * Assembles the complete SQL query from all components
     *
     * @return string The complete SQL query
     */
    private function assemble(): string
    {
        $sql = $this->queryBase .
            $this->whereClause .
            $this->groupByClause .
            $this->havingClause .
            $this->orderByClause;

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
            if ($this->offset > 0) {
                $sql .= " OFFSET {$this->offset}";
            }
        }
        return $sql;
    }

    /**
     * Executes the current query
     *
     * @return PDOStatement The executed statement
     */
    private function execute(): PDOStatement
    {
        $stmt = $this->db->prepare($this->assemble());
        $stmt->execute(array_merge($this->params, $this->whereParams));
        $this->reset();
        return $stmt;
    }

    /**
     * Resets the query builder state
     *
     * @return void
     */
    private function reset(): void
    {
        $this->queryBase = '';
        $this->whereClause = '';
        $this->whereParams = [];
        $this->groupByClause = '';
        $this->havingClause = '';
        $this->orderByClause = '';
        $this->limit = null;
        $this->offset = 0;
        $this->params = [];
        $this->table = '';
    }
}

/**
 * Helper class for building OR conditions in WHERE clauses
 *
 * This class is used internally by BaseModel::whereOr() to build
 * a group of conditions joined by OR operators.
 */
class OrConditionBuilder
{
    /**
     * @var array List of SQL condition strings
     */
    private array $conditions = [];
    
    /**
     * @var array Parameters for the conditions
     */
    private array $parameters = [];
    
    /**
     * @var int Counter for generating unique parameter names
     */
    private int $counter = 0;

    /**
     * Adds a condition to the OR group
     *
     * @param string $column Column name
     * @param string $operator Comparison operator
     * @param mixed $value Value to compare against
     * @return self For method chaining
     */
    public function where(string $column, string $operator, $value): self
    {
        $paramName = 'or_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $column) . '_' . $this->counter++;
        $this->conditions[] = "$column $operator :$paramName";
        $this->parameters[$paramName] = $value;
        return $this;
    }

    /**
     * Gets the list of SQL condition strings
     *
     * @return array List of conditions
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }
    
    /**
     * Gets the parameters for the conditions
     *
     * @return array Parameters
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
