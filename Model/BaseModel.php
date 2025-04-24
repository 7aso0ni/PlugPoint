<?php

namespace Model;

use PDO;
use PDOException;
use PDOStatement;

class BaseModel
{
    // A reference to the PDO instance
    protected $db;

    // Static PDO object to ensure only a single connection is created
    private static $pdo = null;

    // Properties used in query building
    private $query;    // Holds the SQL query being constructed
    private $params = []; // Parameters for prepared SQL statements
    private $table;    // The current table name being operated on

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
        // Append "WHERE" or "AND" based on query state
        // it checks if where condition already exists or not if it does add the AND statement then append it
        $this->query .= (strpos($this->query, 'WHERE') === false ? " WHERE" : " AND") . " $column $operator :$column";

        // Add the condition value to the parameters array
        $this->params[$column] = $value;

        return $this; // Return current instance for chaining
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

    public function update(array $data)
    {
        // Build SET clause: "column1 = :column1, column2 = :column2, ..."
        $setClauses = [];
        foreach ($data as $column => $value) {
            $setClauses[] = "$column = :$column";
        }

        $setClause = implode(', ', $setClauses);

        // Compose the UPDATE query
        $this->query = "UPDATE {$this->table} SET $setClause";

        // Merge new data with existing `where` params
        $this->params = array_merge($this->params, $data);

        return $this->execute(); // Execute and return PDOStatement
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