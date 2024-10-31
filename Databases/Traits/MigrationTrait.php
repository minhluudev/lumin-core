<?php

namespace Lumin\Databases\Traits;

use DirectoryIterator;
use Lumin\Helper;
use Lumin\Support\Facades\Schema;
use PDO;
use PDOException;

trait MigrationTrait {
    public function handleApplyMigrations(PDO $pdo): void {
        echo "Migration start:".PHP_EOL;
        $this->createMigrationTable($pdo);
        $migrationDirectory = Helper::basePath('/database/migrations');
        $oldMigrations      = $this->getMigrations($pdo);
        $newMigrations      = [];
        foreach (new DirectoryIterator($migrationDirectory) as $file) {
            if ($file->isDot()) {
                continue;
            }

            $fileName = $file->getFilename();
            if ($file->isFile() && !in_array($fileName, $oldMigrations)) {
                $filePath     = $file->getPathname();
                $migrateClass = include $filePath;
                $migrateClass->up();
                echo $file->getFilename().PHP_EOL;
                $newMigrations[] = $fileName;
            }
        }

        $this->insertMigration($pdo, $newMigrations);
        echo 'DONE!'.PHP_EOL;

    }

    private function createMigrationTable(PDO $pdo): void {
        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `migrations` (
            	`id` INT AUTO_INCREMENT PRIMARY KEY,
            	`migration` VARCHAR(255),
            	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            SQL;

        $pdo->exec($sql);
    }

    private function getMigrations(PDO $pdo) {
        try {
            $sql  = "SELECT migration FROM migrations ORDER BY id DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(fn($item) => $item['migration'], $results);
        } catch ( PDOException $e ) {
            echo $e->getMessage().PHP_EOL;

            return null;
        }
    }

    private function insertMigration(PDO $pdo, array $values): void {
        try {
            if (!count($values)) {
                return;
            }

            $pdo->exec("INSERT INTO migrations (migration) VALUES ('".implode("'),('", $values)."');");
            $pdo->exec(implode(' ', Schema::getSql()));

        } catch ( PDOException $e ) {
            echo $e->getMessage().PHP_EOL;
        }
    }

    public function handleRollbackMigrations(PDO $pdo): void {
        echo "Migration rollback start:".PHP_EOL;
        try {
            $migrationDirectory = Helper::basePath("/database/migrations");
            $oldMigrations      = $this->getMigrations($pdo);
            if (!count($oldMigrations)) {
                echo 'DONE!'.PHP_EOL;

                return;
            }

            $migrate      = $oldMigrations[0];
            $migrateClass = include $migrationDirectory.'/'.$migrate;
            $migrateClass->down();
            $this->removeLastMigration($pdo);
            echo $migrate.PHP_EOL;
            echo 'DONE!'.PHP_EOL;
        } catch ( PDOException $e ) {
            echo $e->getMessage().PHP_EOL;
        }
    }

    private function removeLastMigration(PDO $pdo): void {
        try {
            $pdo->beginTransaction();
            $sql = implode(' ', Schema::getSql());
            $sql .= "DELETE FROM `migrations` WHERE id = (SELECT id FROM (SELECT MAX(id) AS id FROM `migrations`) AS `temp_table`);";
            $pdo->exec($sql);
        } catch ( PDOException $e ) {
            throw $e;
        }
    }
}
