<?php

namespace Lumin\Databases\Traits;

use PDOException;

trait StaticModelTrait {
    protected static string $query    = '';
    protected static string $select   = '*';
    protected static string $where    = '';
    protected static array  $orderBy  = [];
    protected static array  $join     = [];
    protected static array  $groupBy  = [];
    protected static array  $with     = [];
    protected static array  $withOne  = [];
    protected static array  $withMany = [];

    public static function create(array $data) {
        try {
            $instance      = new static();
            $table         = $instance->getTableName();
            $fields        = implode('`, `', $instance->fillable);
            $convertValues = array_map(fn($value) => $data[$value] ?? null, $instance->fillable);
            $values        = implode("', '", $convertValues);
            $query         = "INSERT INTO $table (`$fields`) VALUES ('$values')";

            $stmt = $instance->pdo->prepare($query);
            $stmt->execute();
            $lastInsertId = $instance->pdo->lastInsertId();

            return self::findById($lastInsertId);
        } catch ( PDOException $e ) {
            return $e->getMessage();
        } finally {
            self::resetProperties();
        }
    }

    public static function findById(int $id) {
        try {
            $instance = new static();
            $table    = $instance->getTableName();
            $query    = "SELECT * FROM $table WHERE id = $id";

            $stmt = $instance->pdo->prepare($query);
            $stmt->execute();

            return $stmt->fetch();
        } catch ( PDOException $e ) {
            return $e->getMessage();
        } finally {
            self::resetProperties();
        }
    }

    protected static function resetProperties(): void {
        self::$query    = '';
        self::$select   = '*';
        self::$where    = '';
        self::$orderBy  = [];
        self::$join     = [];
        self::$groupBy  = [];
        self::$with     = [];
        self::$withOne  = [];
        self::$withMany = [];
    }

    public static function update(array $data): bool {
        try {
            $instance     = new static();
            $isSoftDelete = static::isCheckDeleted();
            $table        = $instance->getTableName();
            $query        = "UPDATE $table SET ";
            $filter       = array_filter($data, fn($key) => in_array($key, $instance->fillable), ARRAY_FILTER_USE_KEY);
            $sets         = array_map(fn($key, $value) => "$key = '$value'", array_keys($filter), $filter);
            $query        .= implode(', ', $sets);
            $query        .= ", updated_at = NOW()";

            if (self::$where) {
                $query .= ' WHERE '.self::$where;
            }

            if ($isSoftDelete) {
                if (self::$where) {
                    $query .= " AND deleted_at IS NULL";
                } else {
                    $query .= " WHERE deleted_at IS NULL";
                }
            }

            $stmt = $instance->pdo->prepare($query);
            $stmt->execute();

            return true;
        } catch ( PDOException $e ) {
            return false;
        } finally {
            self::resetProperties();
        }
    }

    public static function isCheckDeleted(): bool {
        return false;
    }

    public static function delete(): bool {
        try {
            $instance = new static();
            $table    = $instance->getTableName();
            if (static::isCheckDeleted()) {
                $query = "UPDATE $table SET deleted_at = NOW()";
            } else {
                $query = "DELETE FROM $table";
            }

            if (self::$where) {
                $query .= ' WHERE '.self::$where;
            }

            $stmt = $instance->pdo->prepare($query);
            $stmt->execute();

            return true;
        } catch ( PDOException $e ) {
            return false;
        } finally {
            self::resetProperties();
        }
    }

    public static function all(): static {
        return new static();
    }

    public static function select(array $columns = ['*']): static {
        $instance     = new static();
        self::$select = implode(', ', $columns);

        return $instance;
    }

    public static function where(string $column, string $operator, mixed $value): static {
        $instance = new static();
        $where    = "$column $operator '$value'";
        if (self::$where) {
            self::$where .= " AND $where";
        } else {
            self::$where = $where;
        }

        return $instance;
    }

    public static function whereLike(string $column, mixed $value): static {
        $instance = new static();
        $where    = "$column LIKE '%$value%'";
        if (self::$where) {
            self::$where .= " AND $where";
        } else {
            self::$where = $where;
        }

        return $instance;
    }

    public static function orWhere(string $column, string $operator, mixed $value): static {
        $instance = new static();
        $where    = "$column $operator '$value'";
        if (self::$where) {
            self::$where .= " OR $where";
        } else {
            self::$where = $where;
        }

        return $instance;
    }

    public static function orderByAsc(string $column): static {
        $instance        = new static();
        self::$orderBy[] = "$column ASC";

        return $instance;
    }

    public static function orderByDesc(string $column): static {
        $instance        = new static();
        self::$orderBy[] = "$column DESC";

        return $instance;
    }

    public static function with(string $method, array $select = []) {
        $instance      = new static();
        $table         = $instance->getTableName();
        $relation      = $instance->$method();
        $relationTable = $relation['relatedTable'];
        if (self::$select === '*') {
            self::$select = "$table.*";
        }

        $joinSql = "JSON_ARRAYAGG(JSON_OBJECT(".implode(', ', array_map(fn($column) => "'$column', $relationTable.$column", $select)).")) AS $relationTable";

        self::$select    .= ", $joinSql";
        self::$join[]    = $relation['sql'];
        self::$groupBy[] = "$table.id";

        if ($relation['with'] === 'one') {
            self::$withOne[$relationTable] = $method;
        } else {
            self::$withMany[$relationTable] = $method;
        }

        return $instance;
    }

    public function get(): array | null {
        try {
            $isSoftDelete = static::isCheckDeleted();
            $select       = self::$select;
            $where        = self::$where;
            $orderBy      = implode(', ', self::$orderBy);
            $table        = $this->getTableName();
            $query        = "SELECT $select FROM $table ";

            if (!empty(self::$join)) {
                $query .= implode(' ', self::$join);
            }

            if ($where) {
                $query .= " WHERE $where";
            }

            if ($isSoftDelete) {
                if ($where) {
                    $query .= " AND deleted_at IS NULL";
                } else {
                    $query .= " WHERE deleted_at IS NULL";
                }
            }

            if ($orderBy) {
                $query .= " ORDER BY $orderBy";
            }

            if (!empty(self::$groupBy)) {
                $query .= " GROUP BY ".implode(', ', self::$groupBy);
            }
            echo $query;
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll();

            return $this->mapRelations($result);
        } catch ( PDOException $e ) {
            return null;
        } finally {
            self::resetProperties();
        }
    }

    public function mapRelations(array $response): array {
        if (empty(self::$with)) {
            return $response;
        }

        foreach ($response as $key => $item) {
            foreach ($item as $column => $value) {
                if (!in_array($column, self::$with)) {
                    continue;
                }

                $newColumn = json_decode($value, true);

                if (isset(self::$withOne[$column])) {
                    $response[$key][self::$withOne[$column]] = $newColumn[0] ?? null;
                } else {
                    $response[$key][self::$withMany[$column]] = $newColumn;
                }
            }
        }

        return $response;
    }

    public function pagination(int $perPage = 10, int $page = 1): array | null {
        try {
            $select     = self::$select;
            $where      = self::$where;
            $orderBy    = implode(', ', self::$orderBy);
            $table      = $this->getTableName();
            $query      = "SELECT $select FROM $table ";
            $countQuery = str_replace($select, 'COUNT(*)', $query);

            if (!empty(self::$join)) {
                $query      .= implode(' ', self::$join);
                $countQuery .= implode(' ', self::$join);
            }

            if ($where) {
                $query      .= " WHERE $where";
                $countQuery .= " WHERE $where";
            }

            if ($orderBy) {
                $query .= " ORDER BY $orderBy";
            }

            if (!empty(self::$groupBy)) {
                $query .= " GROUP BY ".implode(', ', self::$groupBy);
            }

            $totalPage = $this->pdo
                ->query($countQuery)
                ->fetchColumn();
            $totalPage = ceil($totalPage / $perPage);

            if ($page > $totalPage) {
                $page = $totalPage;
            }

            $offset = ($page - 1) * $perPage;
            $query  .= " LIMIT $perPage OFFSET $offset";
            $stmt   = $this->pdo->prepare($query);
            $stmt->execute();
            $data = $stmt->fetchAll();

            return ['data' => $data, 'total_page' => $totalPage, 'per_page' => $perPage, 'page' => $page,];
        } catch ( PDOException $e ) {
            return null;
        } finally {
            self::resetProperties();
        }
    }

    public function hasMany(string $relatedTable, string $foreignKey, string $localKey = 'id'): array {
        $localTable   = $this->getTableName();
        self::$with[] = $relatedTable;

        return ["sql"          => "LEFT JOIN $relatedTable ON $relatedTable.$foreignKey = $localTable.$localKey",
                "relatedTable" => $relatedTable, "with" => "many"];
    }

    public function hasOne(string $relatedTable, string $foreignKey, string $localKey = 'id'): array {
        $localTable   = $this->getTableName();
        self::$with[] = $relatedTable;

        return ["sql"          => "LEFT JOIN $relatedTable ON $relatedTable.$localKey = $localTable.$foreignKey",
                "relatedTable" => $relatedTable, "with" => "one"];
    }
}