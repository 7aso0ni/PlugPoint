<?php
namespace Model;

use PDO;
use PDOException;
use PDOStatement;


class BaseModel
{
    /* ───── DB connection (singleton) ───── */
    protected PDO $db;
    private static ?PDO $pdo = null;

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

    public function __construct()
    {
        $this->db = self::connect() ?? die('Database connection error.');
    }

    /* ───── Query-builder state ───── */
    private string $queryBase = '';    // SELECT / INSERT / UPDATE / DELETE
    private string $whereClause = '';
    private array $whereParams = [];
    private string $groupByClause = '';
    private string $havingClause = '';
    private string $orderByClause = '';
    private ?int $limit = null;
    private int $offset = 0;
    private array $params = [];    // for insert/update
    private string $table = '';

    /* ───── Core builder methods ───── */
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
        $param = preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
        $condition = "$column $operator :$param";

        $this->whereClause
            ? $this->whereClause .= " AND $condition"
            : $this->whereClause = " WHERE $condition";

        $this->whereParams[$param] = $value;
        return $this;
    }

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

    /** Filter column IN (v1, v2, …) */
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


    public function groupBy(string $column): self
    {
        $this->groupByClause
            ? $this->groupByClause .= ", $column"
            : $this->groupByClause = " GROUP BY $column";
        return $this;
    }

    public function having(string $condition): self
    {
        $this->havingClause = " HAVING $condition";
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orderByClause
            ? $this->orderByClause .= ", $column $direction"
            : $this->orderByClause = " ORDER BY $column $direction";
        return $this;
    }

    public function limit(int $limit, int $offset = 0): self
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    /* ───── CRUD helpers ───── */
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

    public function insert(array $data): PDOStatement
    {
        $cols = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $this->queryBase = "INSERT INTO {$this->table} ($cols) VALUES ($placeholders)";
        $this->params = $data;
        return $this->execute();
    }

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


    public function delete(): PDOStatement
    {
        if ($this->whereClause === '') {
            throw new \RuntimeException('Refusing to DELETE without a WHERE clause');
        }
        $this->queryBase = "DELETE FROM {$this->table}";
        return $this->execute();
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }


    public function sum(string $column, string $alias = 'sum'): float
    {
        $result = $this->select("SUM($column) AS $alias")->first();
        return (float) ($result[$alias] ?? 0);
    }

    public function count(string $column = '*', string $alias = 'count'): int
    {
        $result = $this->select("COUNT($column) AS $alias")->first();
        return (int) ($result[$alias] ?? 0);
    }

    /* ───── Build & execute ───── */
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

    private function execute(): PDOStatement
    {
        $stmt = $this->db->prepare($this->assemble());
        $stmt->execute(array_merge($this->params, $this->whereParams));
        $this->reset();
        return $stmt;
    }

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

/* ─────────────────── Helper: OR condition builder ─────────────────── */
class OrConditionBuilder
{
    private array $conditions = [];
    private array $parameters = [];
    private int $counter = 0;

    public function where(string $column, string $operator, $value): self
    {
        $paramName = 'or_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $column) . '_' . $this->counter++;
        $this->conditions[] = "$column $operator :$paramName";
        $this->parameters[$paramName] = $value;
        return $this;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
