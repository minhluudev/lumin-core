<?php

namespace Lumin\Schemas;

use Lumin\Schemas\Interfaces\SchemaInterface;

class Schema implements SchemaInterface {
    private array $sql = [];

    /**
     * Create a new table with the specified columns.
     *
     * @param  string  $tableName  The name of the table to create.
     * @param  mixed   $callback  A callback function that defines the columns of the table.
     *
     * @return void
     */
    public function create(string $tableName, mixed $callback): void {
        $table = new Blueprint();
        call_user_func($callback, $table);
        $this->sql[] = "CREATE TABLE `$tableName` (".implode(',', $table->getColumns()).");";
    }

    /**
     * Update an existing table with the specified columns.
     *
     * @param  string  $tableName  The name of the table to update.
     * @param  mixed   $callback  A callback function that defines the columns to add to the table.
     *
     * @return void
     */
    public function table(string $tableName, mixed $callback): void {
        $table = new Blueprint();
        call_user_func($callback, $table);

        foreach ($table->getColumns() as $column) {
            $this->sql[] = "ALTER TABLE `$tableName` $column;";
        }
    }

    /**
     * Drop the specified table if it exists.
     *
     * @param  string  $table  The name of the table to drop.
     *
     * @return void
     */
    public function dropIfExists(string $table): void {
        $this->sql[] = "DROP TABLE IF EXISTS `$table`;";
    }

    /**
     * Get the SQL queries that have been executed.
     *
     * @return array
     */
    public function getSql(): array {
        return $this->sql;
    }
}
