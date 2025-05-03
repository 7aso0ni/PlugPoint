<?php
namespace Model;

use PDO;
use PDOException;
use PDOStatement;

class BaseModel
{
    /* ─────────────────────────── DB CONNECTION ─────────────────────────── */
    protected $db;
    private static $pdo = null;

    private static function connect()
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

    public function __construct()
    {
        $this->db = self::connect() ?? die('Database connection error.');
    }

    /* ────────────────────────── QUERY STATE ────────────────────────────── */
    private string $queryBase = '';          // SELECT … FROM … JOIN …
    private string $whereClause = '';          // WHERE …
    private array $whereParams = [];
    private ?int $limit = null;
    private int $offset = 0;
    private array $params = [];          // INSERT / UPDATE params
    private string $table = '';

    /* ────────────────────────── BUILDER METHODS ────────────────────────── */
    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function select($columns = '*'): self
    {
        $cols = is_array($columns) ? implode(', ', $columns) : $columns;
        $this->queryBase = "SELECT $cols FROM {$this->table}";
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->queryBase .= " JOIN $table ON $first $operator $second";
        return $this;
    }

    public function where(string $column, string $operator, $value): self
    {
        // Normalise placeholder name (e.g. cp.availability → cp_availability)
        $param = preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
        $condition = "$column $operator :$param";

        $this->whereClause
            ? $this->whereClause .= " AND $condition"
            : $this->whereClause = " WHERE $condition";

        $this->whereParams[$param] = $value;
        return $this;
    }

    public function limit(int $limit, int $offset = 0): self
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    /* ────────────────────────── CRUD SHORT-CUTS ────────────────────────── */
    public function get(int $mode = PDO::FETCH_ASSOC): array
    {
        return $this->execute()->fetchAll($mode);
    }

    public function first(int $mode = PDO::FETCH_ASSOC)
    {
        return $this->execute()->fetch($mode);
    }

    public function exists(): bool
    {
        return $this->execute()->rowCount() > 0;
    }

    public function insert(array $data)
    {
        $cols = implode(', ', array_keys($data));
        $holes = ':' . implode(', :', array_keys($data));
        $this->queryBase = "INSERT INTO {$this->table} ($cols) VALUES ($holes)";
        $this->params = $data;
        return $this->execute();
    }

    public function update(array $data): PDOStatement
    {
        if (!$data) {
            throw new \InvalidArgumentException('Update data cannot be empty');
        }

        $sets = [];
        foreach ($data as $col => $val) {
            $name = "upd_$col";
            $sets[] = "$col = :$name";
            $this->params[$name] = $val;
        }
        $this->queryBase = "UPDATE {$this->table} SET " . implode(', ', $sets);
        return $this->execute();
    }

    /* ────────────────────────── EXECUTION ──────────────────────────────── */
    private function assemble(): string
    {
        $sql = $this->queryBase;

        if ($this->whereClause) {
            $sql .= $this->whereClause;
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
            if ($this->offset > 0) {
                $sql .= " OFFSET {$this->offset}";
            }
        }

        return $sql;
    }

    private function execute(): PDOStatement
    {
        $sql = $this->assemble();
        $allParams = array_merge($this->params, $this->whereParams);

        // Uncomment for troubleshooting
        // error_log("SQL  : $sql");
        // error_log("PARAM: " . json_encode($allParams));

        $stmt = $this->db->prepare($sql);
        $stmt->execute($allParams);

        /* reset internal state so this instance can be reused safely */
        $this->reset();

        return $stmt;
    }

    private function reset(): void
    {
        $this->queryBase = '';
        $this->whereClause = '';
        $this->whereParams = [];
        $this->limit = null;
        $this->offset = 0;
        $this->params = [];
        $this->table = '';
    }
}
