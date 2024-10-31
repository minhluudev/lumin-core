<?php

namespace Lumin\Schemas\Interfaces;

interface BlueprintInterface {
    public function getColumns(): array;

    /**
     * Define an auto-incrementing integer column.
     *
     * This function defines an auto-incrementing integer column in the table schema. The column
     * name is specified as the first argument, and the default name is `id`. The column is also
     * set as the primary key of the table.
     *
     * @param  string  $name  The name of the column.
     *
     * @return void
     */
    public function id(string $name = 'id'): void;

    /**
     * Define a varchar column.
     *
     * This function defines a varchar column in the table schema. The column name is specified
     * as the first argument, and the properties of the column are specified as an array in the
     * second argument. The properties array can contain the following keys:
     *
     * - `size`: The size of the column (default is 255).
     * - `nullable`: A boolean indicating whether the column can be null (default is false).
     *
     * @param  string  $name  The name of the column.
     * @param  array   $properties  The properties of the column.
     *
     * @return $this
     */
    public function string(string $name, array $properties = []): static;

    /**
     * Define the timestamps columns.
     *
     * This function defines the `created_at` and `updated_at` columns in the table schema.
     * The columns are set to be nullable and have a default value of the current timestamp.
     *
     * @return void
     */
    public function timestamps(): void;

    /**
     * Define the soft deletes column.
     *
     * This function defines the `deleted_at` column in the table schema. The column is set to be
     * nullable and is used to mark the record as deleted without actually removing it from the
     * database.
     *
     * @return void
     */
    public function softDeletes(): void;

    /**
     * Define a unique constraint on the column.
     *
     * This function defines a unique constraint on the last column defined in the table schema.
     * It is used to ensure that the values in the column are unique across all records in the table.
     *
     * @return void
     */
    public function unique(): void;

    /**
     * Define a foreign key column.
     *
     * This function defines a foreign key column in the table schema. The column name is specified
     * as the first argument, and the properties of the column are specified as an array in the
     * second argument. The properties array can contain the following keys:
     *
     * - `table`: The name of the table that the foreign key references.
     * - `column`: The name of the column in the referenced table.
     *
     * @param  string  $table  The name of the table that the foreign key references.
     * @param  string  $column  The name of the column in the referenced table.
     * @param  array   $properties  The properties of the column.
     *
     * @return void
     */
    public function foreignIdFor(string $table, string $column, array $properties = []): void;
}