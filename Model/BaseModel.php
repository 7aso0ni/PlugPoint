<?php

namespace Model;

use PDO;
use PDOException;
use PDOStatement;

class BaseModel
{
    // A reference to the PDO instance
    protected $db;
    private static $pdo = null;

    private $query;
    private $params = [];
    private $table;

    private $where = '';
    private $whereParams = [];

    /**
     * Establishes a database connection using PDO.
     * Implements singleton behavior to reuse the same connection instance.
     */
    private static function connect()
    {
        if (self::$pdo === null) {
            try {
                // Create a new PDO instance for the database connection
                self::$pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                    DB_USER,
                    DB_PASSWORD,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] // Use exceptions for error handling
                );
            } catch (PDOException $e) {
                // Log the error and return null if the connection fails
                error_log("Database Connection Failed: " . $e->getMessage());
                return null;
            }
        }

        return self::$pdo; // Return the PDO instance
    }

    /**
     * Initializes the BaseModel by setting up the database connection.
     * Throws an error if the connection cannot be established.
     */
    public function __construct()
    {
        $this->db = self::connect(); // Establish connection
        if ($this->db === null) {
            die("Database connection error. Please check logs."); // Terminate if connection fails
        }
    }

    /**
     * Sets the table to operate on.
     *
     * @param string $table The name of the table.
     * @return $this
     */
    public function table(string $table): BaseModel
    {
        $this->table = $table; // Set the active table
        return $this; // Return the current instance to allow method chaining
    }

    /**
     * Builds a SELECT query for the specified columns.
     *
     * @param string|array $columns The columns to select, defaults to all (`*`).
     * @return $this
     */
    public function select($columns = '*'): BaseModel
    {
        // Support array input for column names
        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }

        $this->query = "SELECT $columns FROM {$this->table}"; // Formulate SELECT query
        return $this; // Return current instance for chaining
    }

    /**
     * Adds WHERE conditions to the query.
     *
     * @param string $column    The column name for the condition.
     * @param string $operator  The comparison operator (`=`, `>`, `<`, etc.).
     * @param mixed  $value     The value to compare the column against.
     * @return $this
     */
    public function where(string $column, string $operator, $value): BaseModel
    {
        $safeParam = str_replace('.', '_', $column);

        if ($this->where === '') {
            $this->where = " WHERE $column $operator :$safeParam";
        } else {
            $this->where .= " AND $column $operator :$safeParam";
        }

        $this->whereParams[$safeParam] = $value;

        return $this;
    }


    /**
     * Builds an INSERT query for the specified data.
     *
     * @param array $data Key-value pairs representing column names and their values.
     * @return mixed The executed statement or query result.
     */
    public function insert(array $data)
    {
        // Create comma-separated lists for columns and placeholders
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        // Formulate the INSERT query
        $this->query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        // Store the data as the query parameters
        $this->params = $data;

        return $this->execute(); // Execute the query
    }

    public function join(string $table, string $first, string $operator, string $second): BaseModel
    {
        $this->query .= " JOIN $table ON $first $operator $second";
        return $this;
    }

    /**
     * Executes the current query and fetches all results as an array.
     *
     * @param int $fetchMode PDO fetch mode (default is `PDO::FETCH_ASSOC`).
     * @return array The fetched rows.
     */
    public function get(int $fetchMode = PDO::FETCH_ASSOC): array
    {
        return $this->execute()->fetchAll($fetchMode); // Fetch all rows
    }

    /**
     * Executes the current query and fetches the first result.
     *
     * @param int $fetchMode PDO fetch mode (default is `PDO::FETCH_ASSOC`).
     * @return mixed The first row or false if no rows are found.
     */
    public function first(int $fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->execute()->fetch($fetchMode); // Fetch one row
    }

    /**
     * Executes the query and checks if it returned anything by returning a boolean value
     *
     * @param int $fetchMode
     * @return bool
     */
    public function exists(int $fetchMode = PDO::FETCH_ASSOC): bool
    {
        return $this->execute()->rowCount() > 0;
    }


    /**
     * Updates records in the database.
     *
     * @param array $data The data to update.
     * @return PDOStatement The executed statement.
     */
    public function update(array $data): PDOStatement
    {
        if (empty($data)) {
            throw new \InvalidArgumentException("Update data cannot be empty");
        }

        // Build SET clause
        $setClauses = [];
        $updateParams = [];

        foreach ($data as $column => $value) {
            $paramName = "update_" . $column;
            $setClauses[] = "$column = :$paramName";
            $updateParams[$paramName] = $value;
        }

        $setClause = implode(', ', $setClauses);

        // 🔥 Completely rebuild correct UPDATE query
        $this->query = "UPDATE {$this->table} SET $setClause" . $this->where;

        // 🔥 Merge parameters
        $this->params = $updateParams + $this->whereParams;

        return $this->execute();
    }



    /**
     * Executes the currently built query with the provided parameters.
     *
     * @return PDOStatement The executed statement.
     */
    private function execute(): PDOStatement
    {
        // Prepare the query
        $stmt = $this->db->prepare($this->query);

        // Execute with bound parameters
        $stmt->execute($this->params);

        // Reset query and params to allow building new queries
        $this->query = null;
        $this->params = [];

        return $stmt; // Return the executed statement
    }
}