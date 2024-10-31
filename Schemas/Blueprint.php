<?php

namespace Lumin\Schemas;

use Lumin\Schemas\Interfaces\BlueprintInterface;

class Blueprint implements BlueprintInterface {
    private array $columns;

    public function __construct() {
        $this->columns = [];
    }

    public function getColumns(): array {
        return $this->columns;
    }

    public function id(string $name = 'id'): void {
        $this->columns[] = "`$name` INT NOT NULL AUTO_INCREMENT PRIMARY KEY";
    }

    public function string(string $name, array $properties = []): static {
        $size            = $properties['size'] ?? 255;
        $nullable        = isset($properties['nullable']) && $properties['nullable'] ? 'NULL' : 'NOT NULL';
        $this->columns[] = "`$name` VARCHAR($size) $nullable";

        return $this;
    }

    public function timestamps(): void {
        $this->columns[] = "`created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP";
    }

    public function softDeletes(): void {
        $this->columns[] = "`deleted_at` TIMESTAMP NULL";
    }

    public function unique(): void {
        $lastItem        = array_pop($this->columns);
        $lastItem        .= " UNIQUE";
        $this->columns[] = $lastItem;
    }

    public function new(): void {
        $lastItem        = array_pop($this->columns);
        $lastItem        = "ADD COLUMN $lastItem";
        $this->columns[] = $lastItem;
    }

    public function change(): void {
        $lastItem        = array_pop($this->columns);
        $lastItem        = "MODIFY COLUMN $lastItem";
        $this->columns[] = $lastItem;
    }

    public function foreignIdFor(string $table, string $column, array $properties = []): void {
        $this->columns[] = "`$column` INT NOT NULL";
        $this->columns[] = "FOREIGN KEY (`$column`) REFERENCES `$table`(`id`)";
    }

    public function dropColumn(string $name): void {
        $this->columns[] = "DROP COLUMN `$name`";
    }
}
